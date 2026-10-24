<?php

require_once __DIR__ . '/db.php';

function getUpcomingReservations(int $limit = 5): array
{
    $limit = max(1, $limit);
    $stmt = prepare(
        "SELECT b.booking_id, b.number_of_people, b.purpose, b.status, b.created_at,
                u.full_name, u.email, s.slot_date, s.start_time, s.end_time, r.room_name
         FROM bookings b
         JOIN users u ON u.user_id = b.user_id
         JOIN slots s ON s.slot_id = b.slot_id
         JOIN rooms r ON r.room_id = s.room_id
         WHERE b.status IN ('pending', 'confirmed')
           AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
         ORDER BY CASE WHEN b.status = 'pending' THEN 0 ELSE 1 END,
                  s.slot_date ASC, s.start_time ASC, b.created_at ASC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countUserReservations(int $userId): int
{
    $stmt = prepare("SELECT COUNT(*) FROM bookings WHERE user_id = :userId");
    $stmt->execute([':userId' => $userId]);

    return (int) $stmt->fetchColumn();
}

function getUserReservations(int $userId, int $page = 1, int $perPage = 10): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT b.booking_id, b.number_of_people, b.purpose, b.status, b.created_at,
                s.slot_date, s.start_time, s.end_time, r.room_name
         FROM bookings b
         JOIN slots s ON s.slot_id = b.slot_id
         JOIN rooms r ON r.room_id = s.room_id
         WHERE b.user_id = :userId
         ORDER BY CASE WHEN s.slot_date >= CURRENT_DATE THEN 0 ELSE 1 END,
                  CASE WHEN s.slot_date >= CURRENT_DATE THEN s.slot_date END ASC,
                  CASE WHEN s.slot_date < CURRENT_DATE THEN s.slot_date END DESC,
                  s.start_time ASC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cancelReservation(int $bookingId, int $userId): bool
{
    $stmt = prepare(
        "UPDATE bookings b
         JOIN slots s ON s.slot_id = b.slot_id
         SET b.status = 'cancelled'
         WHERE b.booking_id = :bookingId
           AND b.user_id = :userId
           AND b.status IN ('pending', 'confirmed')
           AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))"
    );
    $stmt->execute([
        ':bookingId' => $bookingId,
        ':userId' => $userId,
    ]);

    return $stmt->rowCount() === 1;
}

function userHasActiveReservation(int $userId, int $slotId): bool
{
    $stmt = prepare(
        "SELECT 1 FROM bookings
         WHERE user_id = :userId
           AND slot_id = :slotId
           AND status IN ('pending', 'confirmed')
         LIMIT 1"
    );
    $stmt->execute([
        ':userId' => $userId,
        ':slotId' => $slotId,
    ]);

    return $stmt->fetchColumn() !== false;
}

function remainingSeats(int $slotId): int
{
    $slot = getSlotForReservation($slotId);
    return (int) ($slot['remaining_seats'] ?? 0);
}

function setReservationStatus(int $bookingId, string $status, int $adminId): bool|array
{
    // TODO: Validate the pending booking and status; confirm capacity transactionally when confirming.
}

function getSlotForReservation(int $slotId): ?array
{
    $stmt = prepare(
        "SELECT s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time,
                s.slot_capacity, r.room_name, r.image_url,
                GREATEST(
                    s.slot_capacity - COALESCE(SUM(b.number_of_people), 0),
                    0
                ) AS remaining_seats
         FROM slots s
         JOIN rooms r ON r.room_id = s.room_id
         LEFT JOIN bookings b
            ON b.slot_id = s.slot_id
            AND b.status = 'confirmed'
         WHERE s.slot_id = :slotId
           AND s.is_active = 1
           AND r.is_active = 1
           AND (
                s.slot_date > CURRENT_DATE
                OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME)
           )
         GROUP BY s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time,
                  s.slot_capacity, r.room_name, r.image_url"
    );
    $stmt->execute([':slotId' => $slotId]);

    $slot = $stmt->fetch(PDO::FETCH_ASSOC);
    return $slot === false ? null : $slot;
}

function createReservation(int $userId, int $slotId, int $numberOfPeople, string $purpose): bool|array
{
    try {
        $stmt = prepare(
            "INSERT INTO bookings (user_id, slot_id, number_of_people, purpose)
             VALUES (:userId, :slotId, :numberOfPeople, :purpose)"
        );
        $stmt->execute([
            ':userId' => $userId,
            ':slotId' => $slotId,
            ':numberOfPeople' => $numberOfPeople,
            ':purpose' => trim($purpose),
        ]);

        return $stmt->rowCount() === 1;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return ['You already have an active reservation for this slot.'];
        }

        throw $e;
    }
}