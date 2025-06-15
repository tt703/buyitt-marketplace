<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\includes\auth.php

// Start the session (only once)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the current logged-in user.
 *
 * @return array|null Returns the user data if logged in, or null if not logged in.
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}
/**
 * Log in a user by setting their data in the session.
 *
 * @param array $user The user data to store in the session.
 */
function logIn(array $user): void
{
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'role' => $user['role'],
        'email' => $user['email']
    ];
}

/**
 * Redirect to a specific URL.
 *
 * @param string $url The URL to redirect to.
 */
function redirectTo(string $url): void
{
    header("Location: $url");
    exit();
}
/**
 * Get the current logged-in user or return null (optional login).
 *
 * @return array|null Returns the user data if logged in, or null if not logged in.
 */
function optionalCurrentUser(): ?array
{
    if (isset($_SESSION['user_id'])) {
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

/**
 * Require the user to be logged in.
 * Redirects to the login page if the user is not logged in.
 */
function requireLogin(): void
{
    if (!currentUser()) {
        header("Location: /public/login.php");
        exit();
    }
}
/**
 * Ensure the user is logged in.
 * Redirects to the login page if not logged in.
 */
function  requireAdmin(): void
{
    $user = currentUser();
    if(!$user || ($user['role']??'') !== 'admin')
       header('Location: /public/index.php');
       exit();
}
/**
 * Log in a user by setting their data in the session.
 *
 * @param array $user The user data to store in the session.
 */
/**
 * Log out the current user by clearing the session.
 */
function logoutUser(): void
{
    session_unset();
    session_destroy();
    header("Location: /public/login.php");
    exit();
}