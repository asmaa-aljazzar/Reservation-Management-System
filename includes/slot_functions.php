<?php

require_once __DIR__ . '/db.php';

function countAllSlots(): int
{
    $stmt = prepare("SELECT COUNT(*) FROM slots");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function getAllSlots(int $page = 1, int $perPage = 10): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT slots.*, rooms.room_name
        FROM slots
        JOIN rooms ON slots.room_id = rooms.room_id
        ORDER BY CASE WHEN slots.slot_date >= CURRENT_DATE THEN 0 ELSE 1 END,
                 CASE WHEN slots.slot_date >= CURRENT_DATE THEN slots.slot_date END ASC,
                 CASE WHEN slots.slot_date < CURRENT_DATE THEN slots.slot_date END DESC,
                 slots.start_time ASC
        LIMIT :limit OFFSET :offset"
    );

    // To not use inside execute ()
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countAvailableSlots(): int
{
    $stmt = prepare(
        "SELECT COUNT(*) FROM (
            SELECT s.slot_id,
                   GREATEST(s.slot_capacity - COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0), 0) AS remaining_seats
            FROM slots s
            JOIN rooms r ON r.room_id = s.room_id AND r.is_active = 1
            LEFT JOIN bookings b ON b.slot_id = s.slot_id AND b.status = 'confirmed'
            WHERE s.is_active = 1
              AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
            GROUP BY s.slot_id, s.slot_capacity
            HAVING remaining_seats > 0
        ) AS available_slots"
    );
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function getActiveSlots(int $page = 1, int $perPage = 9): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time,
                s.slot_capacity, r.room_name, r.image_url,
                COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0) AS confirmed_seats,
                COALESCE(SUM(CASE WHEN b.status = 'pending' THEN b.number_of_people ELSE 0 END), 0) AS pending_seats,
                GREATEST(s.slot_capacity - COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0), 0) AS remaining_seats
         FROM slots s
         JOIN rooms r ON r.room_id = s.room_id
         LEFT JOIN bookings b ON b.slot_id = s.slot_id AND b.status IN ('confirmed', 'pending')
         WHERE s.is_active = 1
           AND r.is_active = 1
           AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
         GROUP BY s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time,
                  s.slot_capacity, r.room_name, r.image_url
         HAVING remaining_seats > 0
         ORDER BY s.slot_date ASC, s.start_time ASC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function countAvailableSlotsForRoom(int $roomId): int
{
    $stmt = prepare(
        "SELECT COUNT(*) FROM (
            SELECT s.slot_id,
                   GREATEST(s.slot_capacity - COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0), 0) AS remaining_seats
            FROM slots s
            LEFT JOIN bookings b ON b.slot_id = s.slot_id AND b.status = 'confirmed'
            WHERE s.room_id = :roomId
              AND s.is_active = 1
              AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
            GROUP BY s.slot_id, s.slot_capacity
            HAVING remaining_seats > 0
        ) AS available_slots"
    );
    $stmt->execute([':roomId' => $roomId]);

    return (int) $stmt->fetchColumn();
}

function getUpcomingSlotsForRoom(int $roomId, int $page = 1, int $perPage = 9): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time,
                s.slot_capacity,
                COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0) AS confirmed_seats,
                COALESCE(SUM(CASE WHEN b.status = 'pending' THEN b.number_of_people ELSE 0 END), 0) AS pending_seats,
                GREATEST(s.slot_capacity - COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.number_of_people ELSE 0 END), 0), 0) AS remaining_seats
         FROM slots s
         LEFT JOIN bookings b ON b.slot_id = s.slot_id AND b.status IN ('confirmed', 'pending')
         WHERE s.room_id = :roomId
           AND s.is_active = 1
           AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
         GROUP BY s.slot_id, s.room_id, s.slot_date, s.start_time, s.end_time, s.slot_capacity
         HAVING remaining_seats > 0
         ORDER BY s.slot_date ASC, s.start_time ASC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function slotExists(int $slotId): bool
{
    $stmt = prepare("SELECT 1 FROM slots WHERE slot_id = :slotId");
    $stmt->execute([":slotId" => $slotId]);
    return $stmt->fetchColumn() !== false;
}

function createSlot(
    int $roomId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity
): bool {
    $stmt = prepare(
        "INSERT INTO slots
        (room_id, slot_date, start_time, end_time, slot_capacity)
     VALUES
        (:roomId, :slotDate, :startTime, :endTime, :slotCapacity)"
    );

    $stmt->execute([
        ":roomId" => $roomId,
        ":slotDate" => $slotDate,
        ":startTime" => $startTime,
        ":endTime" => $endTime,
        ":slotCapacity" => $slotCapacity,
    ]);

    return $stmt->rowCount() === 1;
}

function updateSlot(
    int $slotId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity
): bool|array {
    if (!slotExists($slotId)) {
        return ["Slot does not exist."];
    }

    $stmt = prepare(
        "UPDATE slots
         SET slot_date = :slotDate,
             start_time = :startTime,
             end_time = :endTime,
             slot_capacity = :slotCapacity
         WHERE slot_id = :slotId"
    );

    $stmt->execute([
        ":slotDate" => $slotDate,
        ":startTime" => $startTime,
        ":endTime" => $endTime,
        ":slotCapacity" => $slotCapacity,
        ":slotId" => $slotId,
    ]);

    return true;
}

function setSlotStatus(int $slotId, bool $isActive): bool
{
    if (!slotExists($slotId)) {
        return false;
    }

    $stmt = prepare(
        "UPDATE slots
         SET is_active = :isActive
         WHERE slot_id = :slotId"
    );

    $stmt->execute([
        ":isActive" => $isActive,
        ":slotId" => $slotId,
    ]);

    return true;
}

