<?php

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/validation.php";

function countAllRooms(): int
{
    $stmt = prepare("SELECT COUNT(*) FROM rooms");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function getAllRooms(int $page = 1, int $perPage = 10): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT * FROM rooms
        ORDER BY created_at DESC, room_name ASC
        LIMIT :limit OFFSET :offset"
    );

    // To not use inside execute ()
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getActiveRooms(): array
{
    $stmt = prepare(
        "SELECT * FROM rooms
        WHERE is_active = 1
        ORDER BY room_name ASC"
    );

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function roomExists(int $roomId): bool
{
    $stmt = prepare("SELECT 1 FROM rooms WHERE room_id = :roomId");
    $stmt->execute([":roomId" => $roomId]);
    return $stmt->fetchColumn() !== false;
}

function isRoomActive(int $roomId): bool
{
    $stmt = prepare("SELECT 1 FROM rooms WHERE room_id = :roomId AND is_active = 1");
    $stmt->execute([":roomId" => $roomId]);
    return $stmt->fetchColumn() !== false;
}

function maxSlotCapacity(int $roomId): int
{
    //? COALESCE(value, fallback) returns the first value that isn’t NULL.
    $stmt = prepare(
        "SELECT  COALESCE(MAX(slot_capacity), 0)
         FROM slots
         WHERE room_id = :roomId AND is_active = 1"
    );

    $stmt->execute([':roomId' => $roomId]);
    return (int) $stmt->fetchColumn();
}

function createRoom(string $roomName, int $roomCapacity, ?string $description): bool
{
    $stmt = prepare("INSERT INTO rooms (room_name, room_capacity, description) VALUES (:roomName, :roomCapacity, :description)");
    $stmt->execute([
        ":roomName" => $roomName,
        ":roomCapacity" => $roomCapacity,
        ":description" => $description,
    ]);

    return $stmt->rowCount() === 1;
}

function updateRoom(int $roomId, string $roomName, int $roomCapacity, ?string $description): bool|array
{
    if (!isValidRoomData($roomName, $roomCapacity, $description)) {
        return ['Please enter valid room details.'];
    }

    if (!roomExists($roomId)) {
        return ['Room does not exist.'];
    }

    $maxSlotCapacity = maxSlotCapacity($roomId);

    if ($roomCapacity < $maxSlotCapacity) {
        return ["Room capacity must be at least $maxSlotCapacity."];
    }

    $stmt = prepare(
        'UPDATE rooms
         SET room_name = :roomName,
             room_capacity = :roomCapacity,
             description = :description
         WHERE room_id = :roomId'
    );

    $stmt->execute([
        ':roomName' => $roomName,
        ':roomCapacity' => $roomCapacity,
        ':description' => $description,
        ':roomId' => $roomId,
    ]);

    // MySQL reports zero changed rows when all submitted values are unchanged.
    return true;
}

function setRoomStatus(int $roomId, bool $isActive): bool
{
    $stmt = prepare(
        "UPDATE rooms
        SET is_active = :isActive
        WHERE room_id = :roomId"
    );

    $stmt->execute([
        ":isActive" => $isActive,
        ":roomId" => $roomId
    ]);
    return $stmt->rowCount() === 1;
}

function roomCapacity(int $roomId): int
{
    $stmt = prepare("SELECT room_capacity FROM rooms WHERE room_id = :roomId");
    $stmt->execute([":roomId" => $roomId]);
    $capacity = $stmt->fetchColumn();
    return (int) $capacity;
}
