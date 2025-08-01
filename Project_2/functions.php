<?php
// functions.php
require_once 'db.php';
session_start();

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_current_username() {
    global $pdo;

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchColumn();
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function get_user_preferences($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function save_game($user_id, $puzzle_size, $time, $moves, $background_image_id, $won) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO game_stats (user_id, puzzle_size, time_taken_seconds, moves_count, background_image_id, win_status)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $puzzle_size, $time, $moves, $background_image_id, $won]);
}

function get_global_stats() {
    global $pdo;
    return $pdo->query("SELECT puzzle_size, COUNT(*) AS games_played, AVG(time_taken_seconds) AS avg_time, AVG(moves_count) AS avg_moves
                        FROM game_stats GROUP BY puzzle_size")->fetchAll();
}

function get_all_users() {
    global $pdo;
    return $pdo->query("SELECT user_id, username, email, role, registration_date, last_login FROM users")->fetchAll();
}

function get_all_images() {
    global $pdo;
    return $pdo->query("SELECT * FROM background_images")->fetchAll();
}

function upload_image($name, $url, $uploader_id) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO background_images (image_name, image_url, uploaded_by_user_id) VALUES (?, ?, ?)");
    $stmt->execute([$name, $url, $uploader_id]);
}

function deactivate_user($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
}

function delete_image($image_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM background_images WHERE image_id = ?");
    $stmt->execute([$image_id]);
}
