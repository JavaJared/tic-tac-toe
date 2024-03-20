<?php
session_start();

if (!isset($_SESSION['board'])) {
    resetGame();
}

function resetGame() {
    $_SESSION['board'] = [
        ['', '', ''],
        ['', '', ''],
        ['', '', '']
    ];
    $_SESSION['currentPlayer'] = 'X';
    unset($_SESSION['gameOver']); // Clear the game over flag
}

function renderBoard() {
    $boardHtml = '<div class="board">';
    foreach ($_SESSION['board'] as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
            $boardHtml .= sprintf(
                '<button class="cell" type="submit" name="move" value="%s">%s</button>',
                $rowIndex * 3 + $colIndex,
                htmlspecialchars($cell)
            );
        }
    }
    $boardHtml .= '</div>';
    return $boardHtml;
}

function makeComputerMove() {
    $bestScore = PHP_INT_MIN;
    $move = null;

    foreach ($_SESSION['board'] as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
            if ($cell === '') {
                $_SESSION['board'][$rowIndex][$colIndex] = 'O'; // Try an 'O' move
                $score = minimax($_SESSION['board'], 0, false);
                $_SESSION['board'][$rowIndex][$colIndex] = ''; // Undo move

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $move = [$rowIndex, $colIndex];
                }
            }
        }
    }

    if ($move !== null) {
        $_SESSION['board'][$move[0]][$move[1]] = 'O';
        checkGameStatus();
    }
}



function makeMove($position) {
    // Check if the game is over or not
    if (!empty($_SESSION['gameOver'])) {
        return;
    }

    // Determine row and column from position
    $row = floor($position / 3);
    $col = $position % 3;

    if ($_SESSION['board'][$row][$col] === '') {
        $_SESSION['board'][$row][$col] = $_SESSION['currentPlayer'];

        // Switch players in Multiplayer mode or make a computer move in Singleplayer
        if ($_SESSION['gameMode'] === 'multiplayer') {
            $_SESSION['currentPlayer'] = ($_SESSION['currentPlayer'] === 'X') ? 'O' : 'X';
        } else if ($_SESSION['gameMode'] === 'singleplayer' && $_SESSION['currentPlayer'] === 'X') {
            // Computer makes its move after player in Singleplayer mode
            makeComputerMove(); // Ensure this function does not make a move if the game is over
        }

        checkGameStatus(); // Check if the game has ended
    }
}



function checkWinCondition($board) {
    $winningCombinations = [
        [[0, 0], [0, 1], [0, 2]],
        [[1, 0], [1, 1], [1, 2]],
        [[2, 0], [2, 1], [2, 2]],
        [[0, 0], [1, 0], [2, 0]],
        [[0, 1], [1, 1], [2, 1]],
        [[0, 2], [1, 2], [2, 2]],
        [[0, 0], [1, 1], [2, 2]],
        [[2, 0], [1, 1], [0, 2]],
    ];

    foreach ($winningCombinations as $combination) {
        [$a, $b, $c] = $combination;
        if ($board[$a[0]][$a[1]] !== '' &&
            $board[$a[0]][$a[1]] === $board[$b[0]][$b[1]] &&
            $board[$a[0]][$a[1]] === $board[$c[0]][$c[1]]) {
            return $board[$a[0]][$a[1]];
        }
    }

    return null; // No winner yet
}


function checkTie() {
    foreach ($_SESSION['board'] as $row) {
        if (in_array('', $row)) {
            return false;
        }
    }
    return true;
}

function minimax($board, $depth, $isMaximizing) {
    $winner = checkWinCondition($board);
    if ($winner !== null) {
        return $winner === 'O' ? (10 - $depth) : ($winner === 'X' ? ($depth - 10) : 0);
    }

    if ($depth == 9) { // Max depth reached or board is full
        return 0; // Tie
    }

    if ($isMaximizing) {
        $bestScore = PHP_INT_MIN;
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                if ($cell === '') {
                    $board[$rowIndex][$colIndex] = 'O'; // Try an 'O' move
                    $score = minimax($board, $depth + 1, false);
                    $board[$rowIndex][$colIndex] = ''; // Undo move
                    $bestScore = max($score, $bestScore);
                }
            }
        }
        return $bestScore;
    } else {
        $bestScore = PHP_INT_MAX;
        foreach ($board as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                if ($cell === '') {
                    $board[$rowIndex][$colIndex] = 'X'; // Try an 'X' move
                    $score = minimax($board, $depth + 1, true);
                    $board[$rowIndex][$colIndex] = ''; // Undo move
                    $bestScore = min($score, $bestScore);
                }
            }
        }
        return $bestScore;
    }
}

function checkGameStatus() {
    $winner = checkWinCondition($_SESSION['board']);
    if ($winner) {
        $_SESSION['gameOver'] = true;
        $_SESSION['message'] = "Winner is $winner! Game over.";
    } elseif (checkTie()) {
        $_SESSION['gameOver'] = true;
        $_SESSION['message'] = "It's a tie! Game over.";
    }
}

function renderGameOverMessage() {
    if (isset($_SESSION['gameOver']) && $_SESSION['gameOver']) {
        // Display the game over message
        echo "<h1>" . $_SESSION['message'] . "</h1>";

        // Display the "New Game" button
        echo '<form method="POST" action="index.php">';
        echo '<button type="submit" name="reset" value="reset">New Game</button>';
        echo '</form>';

        // Clear the game over message
        unset($_SESSION['message']);

        // Don't clear the gameOver flag here because it's needed to prevent moves after game over
    }
}



