<?php

function getAdminSlots(int $page = 1, int $perPage = 10): array
{
    // TODO: Return paginated slots for the admin, including cancelled slots.
}

function slotExists(int $slotId): bool
{
    // TODO: Check whether this slot exists.
}

function isValidSlotData(
    int $roomId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity,
    ?int $excludeSlotId = null
): bool {
    // TODO: Check room status/capacity, date/time order, and the 30-minute gap rule.
}

function createSlot(
    int $roomId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity
): bool|array {
    // TODO: Validate and insert the slot.
}

function updateSlot(
    int $slotId,
    string $slotDate,
    string $startTime,
    string $endTime,
    int $slotCapacity
): bool|array {
    // TODO: Check existence, validate while excluding this slot from overlap checks, then update.
}

function setSlotStatus(int $slotId, string $status): bool|array
{
    // TODO: Allow only the schema's slot statuses: active or cancelled.
}
