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
        ORDER BY slots.created_at DESC, slots.slot_date ASC
        LIMIT :limit OFFSET :offset"
    );

    // To not use inside execute ()
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActiveSlots(): array
{
    $stmt = prepare(
        "SELECT slots.*
         FROM slots
         JOIN rooms ON slots.room_id = rooms.room_id
         WHERE slots.is_active = 1
           AND rooms.is_active = 1
           AND CONCAT(slots.slot_date, ' ', slots.start_time) > NOW()
         ORDER BY slots.slot_date, slots.start_time"
    );

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

