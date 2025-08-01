<?php
require_once 'functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$username = get_current_username();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #eef2f5; }
        .dashboard { background: white; padding: 20px; border-radius: 6px; width: 600px; margin: auto; }
        h1 { color: #333; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; }
        a { text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
        <h2><?= ucfirst($role) ?> Dashboard</h2>

        <ul>
            <li><a href="fifteen.php">Play Puzzle</a></li>
            <li><a href="profile.php">Edit Preferences</a></li>
            <li><a href="stats.php">View My Stats</a></li>
        </ul>

        



        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
