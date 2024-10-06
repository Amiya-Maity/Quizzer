<?php
// Define the access code
$accessCode = "legend";

// Include autoload if you're using Composer (for Guzzle)
require 'vendor/autoload.php';

// Check if the access code is entered
if ($_POST['access_code'] !== $accessCode && !isset($_POST['question'])) {
    $_COOKIE["login"] = true;
    // If not, display a simple access form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "Incorrect access code. Please try again.";
    }
    // Display the access form
    echo '<form method="post" action="quiz.php" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); width: 400px;">
            <label for="access_code">Access Code:</label>
            <input type="password" name="access_code" required>
            <input type="submit" value="Submit">
          </form>';
} else {

    // Database Configuration
    include('config.php');
    // Connect to the database
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Function to insert a new question into the database
    function insertQuestion($question, $optionA, $optionB, $optionC, $optionD, $optionE, $answer, $category)
    {
        global $conn;

        $sql = "INSERT INTO que (question, optionA, optionB, optionC, optionD, optionE, answer, category) 
                VALUES ('$question', '$optionA', '$optionB', '$optionC', '$optionD', '$optionE', '$answer', '$category')";
        if (mysqli_query($conn, $sql)) {
            echo "Question added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Function to generate a question using AI
    function generateAIQuestion($category, $difficulty)
    {
        // Set your OpenAI API key in an environment variable or in your config
        $apiKey = getenv('OPENAI_API_KEY');

        if (!$apiKey) {
            die("API key not found. Please set your OpenAI API key.");
        }

        // Create the client for the HTTP request
        $client = new \GuzzleHttp\Client();

        // Call OpenAI API or another AI service for generating a question
        try {
            $response = $client->post('https://api.openai.com/v1/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'text-davinci-003',
                    'prompt' => "Generate a $difficulty level question for the category: $category with four answer options.",
                    'max_tokens' => 150,
                ]
            ]);

            // Get the response and decode it
            $data = json_decode($response->getBody(), true);

            // Extract the question and options
            $generatedText = $data['choices'][0]['text'];

            // Parse AI-generated question (you could improve this parsing based on how the response is structured)
            // Assume the response returns a question followed by options A, B, C, D, and a correct answer
            list($question, $optionA, $optionB, $optionC, $optionD, $answer) = explode("\n", trim($generatedText));

            // Return the generated question
            return [
                'question' => $question,
                'optionA' => trim($optionA),
                'optionB' => trim($optionB),
                'optionC' => trim($optionC),
                'optionD' => trim($optionD),
                'answer' => trim($answer)
            ];

        } catch (Exception $e) {
            echo "Error generating question: " . $e->getMessage();
        }
    }

    // Process the form data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && strlen($_POST['question']) != 0) {
        $question = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['question']));
        $optionA = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['optionA']));
        $optionB = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['optionB']));
        $optionC = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['optionC']));
        $optionD = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['optionD']));
        $optionE = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['optionE']));
        $answer = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['answer']));
        $category = mysqli_real_escape_string($conn, str_replace("'", '’', $_POST['category']));

        // Insert the user-submitted question into the database
        insertQuestion($question, $optionA, $optionB, $optionC, $optionD, $optionE, $answer, $category);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auto_generate'])) {
        // Generate an AI-based question
        $difficulty = $_POST['difficulty'];
        $category = $_POST['category'];

        $aiQuestion = generateAIQuestion($category, $difficulty);

        // Insert the AI-generated question into the database
        insertQuestion($aiQuestion['question'], $aiQuestion['optionA'], $aiQuestion['optionB'], $aiQuestion['optionC'], $aiQuestion['optionD'], '', $aiQuestion['answer'], $category);
    }

    mysqli_close($conn);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Page</title>
        <style>
            /* Styles omitted for brevity */
        </style>
    </head>

    <body>
        <h2>Add a New Question</h2>
        <form method="post" action="quiz.php">
            <p>
                <label for="question">Question:</label>
                <input type="text" name="question" id="question">
            </p>
            <p>
                <label for="optionA">Option A:</label>
                <input type="text" name="optionA" id="optionA">
            </p>
            <p>
                <label for="optionB">Option B:</label>
                <input type="text" name="optionB" id="optionB">
            </p>
            <p>
                <label for="optionC">Option C:</label>
                <input type="text" name="optionC" id="optionC">
            </p>
            <p>
                <label for="optionD">Option D:</label>
                <input type="text" name="optionD" id="optionD">
            </p>
            <p>
                <label for="optionE">Option E:</label>
                <input type="text" name="optionE" id="optionE" placeholder="Keep blank if no need !!!">
            </p>
            <p>
                <label for="answer">Correct Answer:</label>
                <input type="text" name="answer" id="answer">
            </p>
            <p>
                <label for="category">Category:</label>
                <input type="text" name="category" id="category">
            </p>
            <input type="submit" value="Add Question">
        </form>

        <h2>Or Automatically Generate a Question</h2>
        <form method="post" action="quiz.php">
            <p>
                <label for="difficulty">Difficulty Level:</label>
                <select name="difficulty" id="difficulty">
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </p>
            <p>
                <label for="category">Category:</label>
                <input type="text" name="category" id="category" required>
            </p>
            <input type="hidden" name="auto_generate" value="1">
            <input type="submit" value="Generate Question">
        </form>
    </body>

    </html>
    <?php
}
?>
