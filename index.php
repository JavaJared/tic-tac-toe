<?php
// Include the game logic script
include('game.php');

// Check if the game mode is set or if a reset is requested
if (!isset($_SESSION['gameMode']) || isset($_POST['reset'])) {
    unset($_SESSION['gameMode']);
    resetGame(); // Make sure to define or modify resetGame accordingly
}

// Handle setting the game mode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gameMode'])) {
    $_SESSION['gameMode'] = $_POST['gameMode'];
    resetGame();
}

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rakkas&family=Spicy+Rice&display=swap" rel="stylesheet">
</head>
<body>
<?php if (!isset($_SESSION['gameMode'])): ?>
        <h1>Tic-Tac-Tactics: Master the Grid!</h1>
        <form method="POST" action="index.php">
            <button class="option" type="submit" name="gameMode" value="singleplayer">Singleplayer</button>
            <button class="option" type="submit" name="gameMode" value="multiplayer">Multiplayer</button>
        </form>
    <?php else: ?>
        <h1>Tic Tac Toe</h1>
        <form method="POST" action="index.php">
            <?php echo renderBoard();
            if (!isset($_SESSION['gameOver']) || !$_SESSION['gameOver']) {
                echo '<button class="restart" type="submit" name="reset" value="reset">Restart Game</button>';
            }
            renderGameOverMessage(); ?>
            
        </form>
    <?php endif; ?>
</body>
</html>
