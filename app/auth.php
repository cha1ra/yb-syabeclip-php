<?php
function initSession() {
    if (session_status() == PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        session_start();
    }

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    } elseif (time() - $_SESSION['last_activity'] > 30 * 24 * 60 * 60) {
        // 30日間アクティビティがない場合、セッションを破棄
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();

    // セッションIDを再生成
    regenerateSessionId();
}

function regenerateSessionId() {
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) {
        // 5分ごとにセッションIDを再生成
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

function requireLogin() {
    initSession();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId() {
    if (session_status() == PHP_SESSION_NONE) {
        initSession();
    }
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserName() {
    initSession();
    return $_SESSION['user_name'] ?? null;
}

function logout() {
    initSession();
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}