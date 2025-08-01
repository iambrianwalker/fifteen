<?php
require_once 'functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to save your game.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $puzzle_size = intval($_POST['puzzle_size'] ?? 4); // default 4x4
    $time = intval($_POST['time'] ?? 0);
    $moves = intval($_POST['moves'] ?? 0);
    $background_image_id = intval($_POST['background_image_id'] ?? 0);
    $won = isset($_POST['won']) && $_POST['won'] === 'true' ? 1 : 0;

    save_game($user_id, $puzzle_size, $time, $moves, $background_image_id, $won);

    echo json_encode(['success' => true, 'message' => 'Game saved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
