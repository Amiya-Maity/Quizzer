<?php
include('config.php');
// Connect to the database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function getRandomQuestions($category, $count)
// Function to get a set of random questions from the database
{
    global $conn;
    $sql = "SELECT * FROM que ";
    if ($category !== '')
        $category = str_replace("-", " ", $category);
        $sql .= ' WHERE CATEGORY="' . $category . '"';
    $sql .= 'ORDER BY RAND() LIMIT ' . $count;
    // echo $sql;
    $result = mysqli_query($conn, $sql);

    $questions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }

    return $questions;
}
function getAllCategories()
// Function to get a set of random questions from the database
{
    global $conn;
    $sql = "SELECT CatId FROM categories";
    $result = mysqli_query($conn, $sql);

    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row["CatId"];
    }

    return $categories;
}

function randomizeOptions($question)
//Function to randomize options
{
    if ($question['OptionE'] !== '')
        $options = array($question['OptionA'], $question['OptionB'], $question['OptionC'], $question['OptionD'], $question['OptionE']);
    else
        $options = array($question['OptionA'], $question['OptionB'], $question['OptionC'], $question['OptionD']);
    shuffle($options);
    return $options;
}

function checkAnswers($userAnswers, $correctAnswers)
// Process user's answer and check correctness
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