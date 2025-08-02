<?php
require_once 'functions.php';
session_start();

if (!is_logged_in()) {
    echo "You must be logged in to save your game.";
    exit;
}

$user_id = $_SESSION['user_id'];
$puzzle_size = 4; // assuming fixed puzzle size for now
$time = intval($_POST['time'] ?? 0);
$moves = intval($_POST['moves'] ?? 0);
$won = intval($_POST['won'] ?? 0);
$background_image_id = $_SESSION['background_image_id'] ?? null;

if ($background_image_id === null) {
    echo "Background image not selected.";
    exit;
}

// Call your reusable function
save_game($user_id, $puzzle_size, $time, $moves, $background_image_id, $won);

echo "Game saved successfully!";
