<?php
session_start();
include('functions.php');

// Check if the form was submitted and process the user's answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = $_POST['answers'] ?? [];
    $correctAnswers = $_SESSION['correctAnswers'] ?? [];

    // Check answers and store results
    $results = checkAnswers($userAnswers, $correctAnswers);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <center>
        <div class="quiz-container">
            <h2>Quiz Results</h2>
            <div id="result-container">
                <?php if (!empty($results)): ?>
                    <h3>Your Answers:</h3>
                    <ul>
                        <?php foreach ($results as $question => $result): ?>
                            <li class="<?php echo strtolower($result); ?>">
                                <strong>Question:</strong> <?php echo htmlspecialchars($question); ?>
                                <br>
                                <strong>Your Answer:</strong> <?php echo htmlspecialchars($userAnswers[$question]); ?>
                                <br>
                                <strong>Result:</strong> <?php echo $result; ?>
                            </li>
                            <br>
                        <?php endforeach; ?>
                    </ul>
                    <h4>Total Correct Answers: <?php echo array_count_values($results)['Correct!'] ?? 0; ?></h4>
                    <h4>Total Incorrect Answers: <?php echo array_count_values($results)['Incorrect!'] ?? 0; ?></h4>
                <?php else: ?>
                    <p>No answers submitted.</p>
                <?php endif; ?>
            </div>
            <button onclick="window.location.href='index.php'">Try Again</button>
        </div>
    </center>
</body>

</html>
