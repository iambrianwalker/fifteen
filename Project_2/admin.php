<?php
require_once 'functions.php';
require_login();

if (!is_admin()) {
    echo "Access denied. Admins only.";
    exit();
}

$users = get_all_users();
$images = get_all_images();
$stats = get_global_stats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f7f7f7;
        }
        h1, h2 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #eee;
        }
        button {
            padding: 6px 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
    <script>
    function deactivateUser(userId) {
        if (!confirm("Deactivate this user?")) return;
        fetch('admin_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=deactivate_user&user_id=' + encodeURIComponent(userId)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }

    function deleteImage(imageId) {
        if (!confirm("Delete this image?")) return;
        fetch('admin_actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=delete_image&image_id=' + encodeURIComponent(imageId)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    }
    </script>
</head>
<body>

<h1>Admin Dashboard</h1>

<h2>Users</h2>
<table>
    <tr>
        <th>ID</th><th>Username</th><th>Email</th>
        <th>Role</th><th>Registered</th><th>Last Login</th><th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['user_id']) ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td><?= htmlspecialchars($user['registration_date']) ?></td>
        <td><?= htmlspecialchars($user['last_login']) ?></td>
        <td>
            <?php if ($user['role'] !== 'admin'): ?>
                <button onclick="deactivateUser(<?= $user['user_id'] ?>)">Deactivate</button>
            <?php else: ?>
                &mdash;
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Background Images</h2>
<table>
    <tr>
        <th>ID</th><th>Name</th><th>URL</th><th>Active</th><th>Uploaded By</th><th>Actions</th>
    </tr>
    <?php foreach ($images as $img): ?>
    <tr>
        <td><?= htmlspecialchars($img['image_id']) ?></td>
        <td><?= htmlspecialchars($img['image_name']) ?></td>
        <td><a href="<?= htmlspecialchars($img['image_url']) ?>" target="_blank">View</a></td>
        <td><?= $img['is_active'] ? 'Yes' : 'No' ?></td>
        <td><?= htmlspecialchars($img['uploaded_by_user_id'] ?? 'N/A') ?></td>
        <td><button onclick="deleteImage(<?= $img['image_id'] ?>)">Delete</button></td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Global Game Statistics</h2>
<table>
    <tr>
        <th>Puzzle Size</th><th>Games Played</th><th>Avg Time (s)</th><th>Avg Moves</th>
    </tr>
    <?php foreach ($stats as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['puzzle_size']) ?></td>
        <td><?= htmlspecialchars($row['games_played']) ?></td>
        <td><?= round($row['avg_time'], 2) ?></td>
        <td><?= round($row['avg_moves'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
