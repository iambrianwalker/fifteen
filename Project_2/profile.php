<?php
require_once 'functions.php';
if (!is_logged_in()) { header('Location: login.php'); exit(); }
$user_id = $_SESSION['user_id'];
$preferences = get_user_preferences($user_id);
$images = get_all_images(); // For dropdown background selection
?>

<h2>Edit Preferences</h2>

<form method="POST" action="save_preferences.php">
  <label for="puzzle_size">Puzzle Size:</label>
  <select name="puzzle_size" id="puzzle_size">
    <option value="3" <?= $preferences['puzzle_size'] == 3 ? 'selected' : '' ?>>3x3</option>
    <option value="4" <?= $preferences['puzzle_size'] == 4 ? 'selected' : '' ?>>4x4</option>
    <option value="5" <?= $preferences['puzzle_size'] == 5 ? 'selected' : '' ?>>5x5</option>
  </select><br><br>

  <label for="background_image">Background Image:</label>
  <select name="background_image" id="background_image">
    <?php foreach ($images as $img): ?>
      <option value="<?= $img['image_id'] ?>" <?= $preferences['background_image'] == $img['image_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($img['image_name']) ?>
      </option>
    <?php endforeach; ?>
  </select><br><br>

  <input type="submit" value="Save Preferences">
</form>
