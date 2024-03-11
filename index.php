<?php
// Include the game logic script
include('game.php');

// Start or reset the game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    resetGame();
}

// Handle player move
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move'])) {
    $position = $_POST['move'];
    makeMove($position);
}

// Render the game board
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tic Tac Toe Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Tic Tac Toe Game</h1>
    <form method="POST" action="index.php">
        <?php echo renderBoard(); ?>
        <button type="submit" name="reset" value="reset">Restart Game</button>
    </form>
</body>
</html>
