-- Active: 1791886845083@@127.0.0.1@3306@library_study_room_reservation
-- Study Nook: library study-room reservation schema
-- Import this file into MySQL/phpMyAdmin. It creates the database if needed.

CREATE DATABASE IF NOT EXISTS library_study_room_reservation
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE library_study_room_reservation;

CREATE TABLE IF NOT EXISTS users (
    user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role_active (role, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rooms (
    room_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_name VARCHAR(120) NOT NULL,
    room_capacity INT UNSIGNED NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (room_id),
    UNIQUE KEY uq_rooms_name (room_name),
    KEY idx_rooms_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For a database created before is_active was added, run this once:
-- ALTER TABLE rooms ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER description;
-- CREATE INDEX idx_rooms_active ON rooms (is_active);
-- For an existing database, run once to add room images:
-- ALTER TABLE rooms ADD COLUMN image_url VARCHAR(255) NULL AFTER description;

CREATE TABLE IF NOT EXISTS slots (
    slot_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_id BIGINT UNSIGNED NOT NULL,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_capacity INT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (slot_id),
    UNIQUE KEY uq_slots_room_date_start (room_id, slot_date, start_time),
    KEY idx_slots_date_time (slot_date, start_time),
    KEY idx_slots_active_date_time (is_active, slot_date, start_time),
    CONSTRAINT fk_slots_room
        FOREIGN KEY (room_id) REFERENCES rooms (room_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For a database created before slot is_active was added, run this once:
-- ALTER TABLE slots ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER slot_capacity;
-- CREATE INDEX idx_slots_active_date_time ON slots (is_active, slot_date, start_time);

CREATE TABLE IF NOT EXISTS bookings (
    booking_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    slot_id BIGINT UNSIGNED NOT NULL,
    number_of_people INT UNSIGNED NOT NULL,
    purpose TEXT NOT NULL,
    status ENUM('pending', 'confirmed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    reviewed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    -- Pending and confirmed requests prevent the same user booking this slot again.
    -- Rejected and cancelled rows remain in history and do not block a new request.
    active_user_id BIGINT UNSIGNED GENERATED ALWAYS AS (
        CASE WHEN status IN ('pending', 'confirmed') THEN user_id ELSE NULL END
    ) STORED,
    PRIMARY KEY (booking_id),
    UNIQUE KEY uq_bookings_active_user_slot (slot_id, active_user_id),
    KEY idx_bookings_user_created (user_id, created_at),
    KEY idx_bookings_slot_status (slot_id, status),
    KEY idx_bookings_reviewer (reviewed_by),
    CONSTRAINT fk_bookings_user
        FOREIGN KEY (user_id) REFERENCES users (user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_slot
        FOREIGN KEY (slot_id) REFERENCES slots (slot_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_reviewer
        FOREIGN KEY (reviewed_by) REFERENCES users (user_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enforce these business rules in the PHP actions using validation and transactions:
-- * room_capacity and slot_capacity must be greater than zero.
-- * slot_capacity cannot exceed its room's room_capacity.
-- * A slot's end_time must be later than its start_time, and same-room slots need
--   at least 30 minutes between them.
-- * number_of_people must be positive and cannot exceed slot_capacity.
-- * Pending bookings do not consume seats. Before confirming, lock the slot row
--   and ensure confirmed seats + this booking <= slot_capacity.
-- * Only an admin can confirm/reject a booking; only the booking owner can cancel it.
