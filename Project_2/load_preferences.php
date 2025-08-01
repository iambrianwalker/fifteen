<?php
require_once 'functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$preferences = get_user_preferences($user_id);

if ($preferences) {
    echo json_encode([
        'success' => true,
        'preferences' => $preferences
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No preferences found'
    ]);
}
?>
