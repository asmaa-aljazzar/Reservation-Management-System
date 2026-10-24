<?php

require_once __DIR__ . '/db.php';

function getDashboardSummary(): array
{
    $stmt = prepare(
        "SELECT
            (SELECT COUNT(*)
             FROM bookings b
             JOIN slots s ON s.slot_id = b.slot_id
             WHERE b.status = 'pending'
               AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))) AS pending_reservations,
            (SELECT COUNT(*)
             FROM bookings b
             JOIN slots s ON s.slot_id = b.slot_id
             WHERE b.status = 'confirmed'
               AND s.slot_date = CURRENT_DATE) AS confirmed_today,
            (SELECT COUNT(*) FROM rooms WHERE is_active = 1) AS active_rooms,
            (SELECT COUNT(*)
             FROM slots s
             JOIN rooms r ON r.room_id = s.room_id AND r.is_active = 1
             WHERE s.is_active = 1
               AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))) AS upcoming_slots"
    );
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'pending_reservations' => 0,
        'confirmed_today' => 0,
        'active_rooms' => 0,
        'upcoming_slots' => 0,
    ];
}

function getPendingReservations(int $limit = 5): array
{
    $limit = max(1, $limit);
    $stmt = prepare(
        "SELECT b.booking_id, b.number_of_people, b.created_at,
                u.full_name, s.slot_date, s.start_time, r.room_name
         FROM bookings b
         JOIN users u ON u.user_id = b.user_id
         JOIN slots s ON s.slot_id = b.slot_id
         JOIN rooms r ON r.room_id = s.room_id
         WHERE b.status = 'pending'
           AND (s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME))
         ORDER BY b.created_at DESC, b.booking_id DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTodaysBookings(int $limit = 5): array
{
    $limit = max(1, $limit);
    $stmt = prepare(
        "SELECT b.booking_id, b.number_of_people,
                u.full_name, s.start_time, s.end_time, r.room_name
         FROM bookings b
         JOIN users u ON u.user_id = b.user_id
         JOIN slots s ON s.slot_id = b.slot_id
         JOIN rooms r ON r.room_id = s.room_id
         WHERE b.status = 'confirmed'
           AND s.slot_date = CURRENT_DATE
         ORDER BY s.start_time ASC, b.booking_id ASC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getConfirmedBookingCountsByDay(int $days = 7): array
{
    $days = max(1, min(31, $days));
    $lastDay = $days - 1;
    $stmt = prepare(
        "SELECT s.slot_date, COUNT(*) AS booking_count
         FROM bookings b
         JOIN slots s ON s.slot_id = b.slot_id
         WHERE b.status = 'confirmed'
           AND s.slot_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL $lastDay DAY)
         GROUP BY s.slot_date
         ORDER BY s.slot_date ASC"
    );
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingReservationStatusCounts(): array
{
    $counts = ['pending' => 0, 'confirmed' => 0, 'rejected' => 0, 'cancelled' => 0];
    $stmt = prepare(
        "SELECT b.status, COUNT(*) AS reservation_count
         FROM bookings b
         JOIN slots s ON s.slot_id = b.slot_id
         WHERE s.slot_date > CURRENT_DATE OR (s.slot_date = CURRENT_DATE AND s.start_time > CURRENT_TIME)
         GROUP BY b.status"
    );
    $stmt->execute();

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($counts[$row['status']])) {
            $counts[$row['status']] = (int) $row['reservation_count'];
        }
    }

    return $counts;
}
