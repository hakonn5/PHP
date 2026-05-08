<?php
session_start();
require_once __DIR__ . '/db.php';

/**
 * Очищает строку от лишних пробелов и экранирует спецсимволы для предотвращения XSS.
 *
 * @param string $data Входная строка.
 * @return string Очищенная строка.
 */
function sanitizeInput(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Проверяет, авторизован ли текущий пользователь.
 *
 * @return bool Возвращает true, если пользователь авторизован, иначе false.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Возвращает ID текущего пользователя.
 *
 * @return int|null ID пользователя или null, если не авторизован.
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Проверяет, является ли текущий пользователь администратором.
 *
 * @return bool Возвращает true, если администратор.
 */
function isAdmin(): bool
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

/**
 * Проверяет, забанен ли пользователь по его username.
 *
 * @param string $username Имя пользователя.
 * @return bool Возвращает true, если забанен.
 */
function isBanned(string $username): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT is_banned FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    return $user && $user['is_banned'] == 1;
}

/**
 * Проверяет, забанен ли пользователь по его ID.
 *
 * @param int $userId ID пользователя.
 * @return bool Возвращает true, если забанен.
 */
function isUserBanned(int $userId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT is_banned FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user && $user['is_banned'] == 1;
}
