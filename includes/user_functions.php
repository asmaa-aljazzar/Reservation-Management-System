<?php

require_once __DIR__ . '/db.php';

function countAllUsers(): int
{
    $stmt = prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function userExists(int $userId): bool
{
    $stmt = prepare("SELECT 1 FROM users WHERE user_id = :userId");
    $stmt->execute([':userId' => $userId]);

    return $stmt->fetchColumn() !== false;
}

function setUserStatus(int $userId, bool $isActive): bool
{
    $stmt = prepare(
        "UPDATE users
         SET is_active = :isActive
         WHERE user_id = :userId"
    );
    $stmt->execute([
        ':isActive' => $isActive,
        ':userId' => $userId,
    ]);

    return $stmt->rowCount() === 1;
}

function getAllUsers(int $page = 1, int $perPage = 10): array
{
    $page = max(1, $page);
    $perPage = max(1, $perPage);
    $offset = ($page - 1) * $perPage;

    $stmt = prepare(
        "SELECT user_id, full_name, email, role, is_active, created_at
         FROM users
         ORDER BY created_at DESC, full_name ASC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
