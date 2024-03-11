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
}

function renderBoard() {
    $boardHtml = '<div class="board">';
    foreach ($_SESSION['board'] as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
            $boardHtml .= sprintf(
                '<button type="submit" name="move" value="%s">%s</button>',
                $rowIndex * 3 + $colIndex,
                $cell
            );
        }
        $boardHtml .= '<br>';
    }
    $boardHtml .= '</div>';
    return $boardHtml;
}

function makeMove($position) {
    $row = floor($position / 3);
    $col = $position % 3;

    if ($_SESSION['board'][$row][$col] === '') {
        $_SESSION['board'][$row][$col] = $_SESSION['currentPlayer'];
        $_SESSION['currentPlayer'] = ($_SESSION['currentPlayer'] === 'X') ? 'O' : 'X';
        checkGameStatus();
    }
}

function checkWin() {
    $board = $_SESSION['board'];
    $lines = [
        [[0, 0], [0, 1], [0, 2]],
        [[1, 0], [1, 1], [1, 2]],
        [[2, 0], [2, 1], [2, 2]],
        [[0, 0], [1, 0], [2, 0]],
        [[0, 1], [1, 1], [2, 1]],
        [[0, 2], [1, 2], [2, 2]],
        [[0, 0], [1, 1], [2, 2]],
        [[0, 2], [1, 1], [2, 0]],
    ];

    foreach ($lines as $line) {
        [$a, $b, $c] = $line;
        if ($board[$a[0]][$a[1]] && $board[$a[0]][$a[1]] === $board[$b[0]][$b[1]] && $board[$a[0]][$a[1]] === $board[$c[0]][$c[1]]) {
            return $board[$a[0]][$a[1]];
        }
    }

    return null;
}

function checkTie() {
    foreach ($_SESSION['board'] as $row) {
        if (in_array('', $row)) {
            return false;
        }
    }
    return true;
}

function checkGameStatus() {
    $winner = checkWin();
    if ($winner) {
        echo "<p>Winner is $winner! Game over.</p>";
        session_destroy();
        exit;
    } elseif (checkTie()) {
        echo "<p>It's a tie! Game over.</p>";
        session_destroy();
        exit;
    }
}

