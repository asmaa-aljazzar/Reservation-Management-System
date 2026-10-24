<?php

function getUpcomingReservations(int $limit = 5): array
{
    // TODO: Return upcoming reservations for admin review.
}

function remainingSeats(int $slotId): int
{
    // TODO: Return slot capacity minus seats in confirmed reservations.
}

function setReservationStatus(int $bookingId, string $status, int $adminId): bool|array
{
    // TODO: Validate the pending booking and status; confirm capacity transactionally when confirming.
}
