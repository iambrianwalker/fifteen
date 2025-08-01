<?php
require_once 'functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, registration_date) VALUES (?, ?, ?, 'user', NOW())");
        $stmt->execute([$username, $email, $hashed]);

        // Optional auto-login after registration
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = 'user';

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(['success' => false, 'message' => 'Username or email already taken.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
