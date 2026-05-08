<?php

/**
 * Helper function to handle flash messages.
 * 
 * @param string|null $key The session key.
 * @param string|null $message The message to store.
 * @return string|null The message if retrieved, null otherwise.
 */
function flash(?string $key = null, ?string $message = null): ?string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($message !== null) {
        $_SESSION['flash_' . $key] = $message;
        return null;
    }

    if ($key !== null) {
        if (isset($_SESSION['flash_' . $key])) {
            $msg = $_SESSION['flash_' . $key];
            unset($_SESSION['flash_' . $key]);
            return $msg;
        }
        return null;
    }

    return null;
}

/**
 * Check if the user is authenticated.
 * 
 * @return bool
 */
function isAuthenticated(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

/**
 * Check if the user is an admin.
 * 
 * @return bool
 */
function isAdmin(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID.
 * 
 * @return int|null
 */
function getCurrentUserId(): ?int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}

/**
 * Redirect and exit.
 * 
 * @param string $url The URL to redirect to.
 * @return void
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}
