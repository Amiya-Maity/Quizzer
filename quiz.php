<?php
// Define the access code
$accessCode = "legend";
// Check if the access code is entered
if ($_POST['access_code'] !== $accessCode && !isset($_POST['question'])) {
    $_COOKIE["login"] = True;
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
    include ('config.php');
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

        $sql = "INSERT INTO que VALUES ('$question', '$optionA', '$optionB', '$optionC', '$optionD', '$optionE', '$answer', '$category')";
        echo ($sql);
        if (mysqli_query($conn, $sql)) {
            echo "Question added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            echo '<br>';
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

        // Ensure $answer is one of 'OptionA', 'OptionB', 'OptionC', 'OptionD'
        insertQuestion($question, $optionA, $optionB, $optionC, $optionD, $optionE, $answer, $category);
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
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }

            form {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                width: 400px;
            }

            label {
                display: block;
                margin-bottom: 8px;
            }

            input {
                width: 100%;
                padding: 8px;
                margin-bottom: 16px;
                box-sizing: border-box;
            }

            input[type="submit"] {
                background-color: #4caf50;
                color: #fff;
                cursor: pointer;
            }

            p {
                display: flex;
            }

            p label {
                width: 100px;
            }

            input[type="submit"]:hover {
                background-color: #45a049;
            }
        </style>

    </head>

    <body>
        <h2>Add a New Question</h2>
        <form method="post" action="quiz.php">
            <p>
                <label for="question">Question:</label>
                <input type="text" name="question" id="question" required>
            </p>
            <p>
                <label for="optionA">Option A:</label>
                <input type="text" name="optionA" id="optionA" required>
            </p>
            <p>
                <label for="optionB">Option B:</label>
                <input type="text" name="optionB" id="optionB" required>
            </p>
            <p>
                <label for="optionC">Option C:</label>
                <input type="text" name="optionC" id="optionC" required>
            </p>
            <p>
                <label for="optionD">Option D:</label>
                <input type="text" name="optionD" id="optionD" required>
            </p>
            <p>
                <label for="optionE">Option E:</label>
                <input type="text" name="optionE" id="optionE" placeholder="Keep blank if no need !!!">
            </p>
            <p>
                <label for="answer">Correct Answer:</label>
                <input type="text" name="answer" id="answer" required>
            </p>
            <p>
                <label for="category">Category:</label>
                <input type="text" name="category" id="category" required>
            </p>
            <input type="submit" value="Add Question">
        </form>
    </body>

    </html>
    <?php
}
?>