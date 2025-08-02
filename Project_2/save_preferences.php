<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$puzzle_size = $_POST['puzzle_size'] ?? 4;
$background_image_id = $_POST['background_image_id'] ?? null;

$success = save_user_preferences($user_id, $puzzle_size, $background_image_id);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Preferences saved']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save preferences']);
}
