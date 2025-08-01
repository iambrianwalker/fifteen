<?php
require_once 'functions.php';
require_login();

if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'deactivate_user':
        $user_id = intval($_POST['user_id']);
        if ($user_id) {
            deactivate_user($user_id);
            $response = ['success' => true, 'message' => 'User deactivated'];
        }
        break;

    case 'delete_image':
        $image_id = intval($_POST['image_id']);
        if ($image_id) {
            delete_image($image_id);
            $response = ['success' => true, 'message' => 'Image deleted'];
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
