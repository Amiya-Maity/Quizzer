<?php
include('config.php');

// Connect to the database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/**
 * Function to get a set of random questions from the database.
 * @param string $category - The category to filter questions by.
 * @param int $count - The number of random questions to fetch.
 * @return array - An array of random questions.
 */
function getRandomQuestions($category = '', $count = 5)
{
    global $conn;
    $sql = "SELECT * FROM que";

    // Add a condition for the category if provided
    if ($category !== '') {
        $category = str_replace("-", " ", mysqli_real_escape_string($conn, $category));
        $sql .= ' WHERE CATEGORY="' . $category . '"';
    }

    // Fetch random questions based on the limit provided
    $sql .= ' ORDER BY RAND() LIMIT ' . (int)$count;
    
    $result = mysqli_query($conn, $sql);

    $questions = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $questions[] = $row;
        }
    }

    return $questions;
}

/**
 * Function to get all categories from the database.
 * @return array - An array of all category IDs.
 */
function getAllCategories()
{
    global $conn;
    $sql = "SELECT CatId FROM categories";
    $result = mysqli_query($conn, $sql);

    $categories = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row["CatId"];
        }
    }

    return $categories;
}

/**
 * Function to randomize the options for a given question.
 * @param array $question - The question array containing options.
 * @return array - A shuffled array of options.
 */
function randomizeOptions($question)
{
    $options = array($question['OptionA'], $question['OptionB'], $question['OptionC'], $question['OptionD']);

    // Include OptionE if it exists
    if (!empty($question['OptionE'])) {
        $options[] = $question['OptionE'];
    }

    // Shuffle the options array
    shuffle($options);

    return $options;
}

/**
 * Function to check user's answers against correct answers.
 * @param array $userAnswers - An associative array of user's answers (questionId => answer).
 * @param array $correctAnswers - An associative array of correct answers (questionId => correctAnswer).
 * @return array - An array of results indicating whether each answer was correct or incorrect.
 */
function checkAnswers($userAnswers, $correctAnswers)
{
    $results = array();

    foreach ($userAnswers as $questionId => $userAnswer) {
        $correctAnswer = $correctAnswers[$questionId];
        $isCorrect = ($correctAnswer === $userAnswer);
        $results[$questionId] = $isCorrect ? 'Correct!' : 'Incorrect!';
    }

    return $results;
}
?>
