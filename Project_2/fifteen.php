<?php
require_once 'functions.php';
session_start();

// Save background selection to session if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['background_image_id'])) {
    $_SESSION['background_image_id'] = intval($_POST['background_image_id']);
}

$logged_in = is_logged_in();
$username = $logged_in ? get_current_username() : null;

// Default puzzle size and background
$puzzleSize = 4;
$backgroundImageUrl = null;

// Fetch user preferences if logged in
if ($logged_in) {
    $user_id = $_SESSION['user_id'];
    $preferences = get_user_preferences($user_id);

    if ($preferences) {
        $puzzleSize = $preferences['puzzle_size'] ?? 4;

        // If preference exists, it overrides session bg
        if (!empty($preferences['background_image_id'])) {
            $bgId = $preferences['background_image_id'];
            $stmt = $pdo->prepare("SELECT image_url FROM background_images WHERE image_id = ?");
            $stmt->execute([$bgId]);
            $result = $stmt->fetch();
            if ($result) {
                $backgroundImageUrl = $result['image_url'];
            }
        }
    }
}

// If no preference, fallback to session background
if (!$backgroundImageUrl && isset($_SESSION['background_image_id'])) {
    $bgId = $_SESSION['background_image_id'];
    $stmt = $pdo->prepare("SELECT image_url FROM background_images WHERE image_id = ?");
    $stmt->execute([$bgId]);
    $result = $stmt->fetch();
    if ($result) {
        $backgroundImageUrl = $result['image_url'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Fifteen Puzzle Game</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>Fifteen Puzzle</h1>

    <?php if (!$logged_in): ?>
        <!-- Login Form -->
        <form id="login-form" method="POST" action="login.php">
            <h2>Login</h2>
            <label>Username: <input type="text" name="username" required></label><br />
            <label>Password: <input type="password" name="password" required></label><br />
            <button type="submit">Login</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" method="POST" action="register.php">
            <h2>Register</h2>
            <label>Username: <input type="text" name="username" required></label><br />
            <label>Email: <input type="email" name="email" required></label><br />
            <label>Password: <input type="password" name="password" required></label><br />
            <button type="submit">Register</button>
        </form>

    <?php else: ?>
        <p>Welcome, <?= htmlspecialchars($username) ?>! <a href="logout.php">Logout</a></p>

        <!-- Background selection form -->
        <form method="POST" action="">
            <label>Select Background:</label>
            <select name="background_image_id">
                <?php foreach (get_all_images() as $img): ?>
                    <option value="<?= $img['image_id'] ?>"
                      <?= (isset($_SESSION['background_image_id']) && $_SESSION['background_image_id'] == $img['image_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($img['image_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Change Background" />
        </form>

        <!-- Puzzle game board -->
        <div id="puzzle">
            <!-- Tiles added dynamically by JS -->
        </div>

        <!-- Game Controls -->
        <div id="controls">
            <button id="shuffle-button">Shuffle</button>
            <button id="save-button">Save Game</button>
            <button id="autosolve-button">Autosolve</button>
            <div id="messages" style="margin-top: 10px; color: green;"></div>
        </div>
    <?php endif; ?>

    <p style="text-align: right;">
      <a href="https://validator.w3.org/check/referer">
        <img src="https://www.w3.org/Icons/valid-html401" alt="Valid HTML" style="border:0;" />
      </a>
      <a href="https://jigsaw.w3.org/css-validator/check/referer">
        <img src="https://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS" style="border:0;" />
      </a>
    </p>

    <!-- Pass preferences to JS -->
    <script>
      window.backgroundImageUrl = <?= json_encode($backgroundImageUrl) ?>;
      window.puzzleSize = <?= json_encode($puzzleSize) ?>;
    </script>

    <script defer src="fifteen.js"></script>
</body>
</html>
