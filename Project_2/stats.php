<?php
require_once 'functions.php';
session_start();

if (!is_logged_in()) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = get_current_username();

// Get recent games with background names
$gamesStmt = $pdo->prepare(
"SELECT game_stats.*, background_images.image_name
FROM game_stats
LEFT JOIN background_images ON game_stats.background_image_id = background_images.image_id
WHERE game_stats.user_id = ?
ORDER BY game_stats.played_at DESC
");
$gamesStmt->execute([$user_id]);
$games = $gamesStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($username) ?>'s Game Stats</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1><?= htmlspecialchars($username) ?>'s Game Stats</h1>
  <p><a href="fifteen.php">← Back to Game</a></p>

  <h2>Game History</h2>
  <table border="1" cellpadding="5">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>Size</th>
        <th>Time</th>
        <th>Moves</th>
        <th>Background</th>
        <th>Result</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($games as $i => $game): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($game['played_at']) ?></td>
          <td><?= $game['puzzle_size'] ?> x <?= $game['puzzle_size'] ?></td>
          <td><?= gmdate("i:s", $game['time_taken_seconds']) ?></td>
          <td><?= $game['moves_count'] ?></td>
          <td><?= htmlspecialchars($game['image_name'] ?? 'N/A') ?></td>
          <td><?= $game['win_status'] ? "Win" : "Loss" ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
