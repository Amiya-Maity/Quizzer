<?php
include('functions.php');
session_start();

// Process user's answer on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['answers'])) {
        $userAnswers = $_POST['answers'];
        $correctAnswers = $_SESSION['correctAnswers'];
        // Check answers and get results
        $results = checkAnswers($userAnswers, $correctAnswers);

        echo json_encode(['results' => $results]);
        exit;
    }
}

$time = 0;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get a new set of random questions
    $category = '';
    $count = 5;
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    }
    if (isset($_GET['count'])) {
        $count = $_GET['count'];
    }
    $time = $count * 60;
    $questions = getRandomQuestions($category, $count);
}

// Store correct answers in the session
foreach ($questions as $question) {
    $_SESSION['correctAnswers'][$question['Question']] = $question['Answer'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <?php
    echo '<h1 class="h1" id="categories">Categories</h1><ul id="category-list" class="hidden">';
    foreach (getAllCategories() as $value) {
        echo '<li><a href="?category=' . str_replace(" ", "-", $value) . '" class="categories">' . $value . '</a></li><br>';
    }
    echo '</ul>';
    ?>
    <center>
        <div class="quiz-container">
            <h2 style="text-align: center;">Quiz</h2>
            <p>
                <span id="timer"></span>
            </p>
            <form id="quiz-form" method="post">
                <?php foreach ($questions as $question): ?>
                    <div class="question-container">
                        <p style="font-size:20px;">
                            <?php echo htmlspecialchars($question['Question'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="options-container radio-group">
                            <?php
                            $options = randomizeOptions($question);
                            foreach ($options as $option): ?>
                                <div class="option" onclick="selectRadioButton('answers[<?php echo htmlspecialchars($question['Question'], ENT_QUOTES, 'UTF-8'); ?>]<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>')">
                                    <input type="radio" name="answers[<?php echo htmlspecialchars($question['Question'], ENT_QUOTES, 'UTF-8'); ?>]"
                                        id="answers[<?php echo htmlspecialchars($question['Question'], ENT_QUOTES, 'UTF-8'); ?>]<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"
                                        value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>">
                                    <label for="answers[<?php echo htmlspecialchars($question['Question'], ENT_QUOTES, 'UTF-8'); ?>]<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <input type="submit" id="submit" value="Submit Answers">
                <button type="button" onclick="restartPage()">Restart</button>
                <br><br>
            </form>
            <br>
        </div>
    </center>

    <script>
        let category_visibility = 0;
        document.getElementById("categories").addEventListener("click", function () {
            if (category_visibility)
                document.getElementById("category-list").classList.add("hidden");
            else
                document.getElementById("category-list").classList.remove("hidden");
            category_visibility = !category_visibility;
        });

        var submitButtonClickCount = 0;
        document.getElementById('quiz-form').addEventListener('submit', function (event) {
            event.preventDefault();
            submitButtonClickCount++;
            submitAnswers();
        });

        if (submitButtonClickCount === 0) {
            let myVar = setInterval(myTimer, 1000);
            let time = <?php echo $time; ?>;

            function myTimer() {
                time--;
                let timer = document.getElementById("timer");
                timer.innerHTML = formatTime(time);
                if (time === 0) {
                    clearInterval(myVar);
                    alert("Time's Up!!");
                    document.getElementById("submit").click();
                }
            }

            function formatTime(seconds) {
                let minutes = Math.floor(seconds / 60);
                let remainingSeconds = seconds % 60;
                return pad(minutes) + ":" + pad(remainingSeconds);
            }

            function pad(number) {
                return (number < 10) ? "0" + number : number;
            }
        }

        function restartPage() {
            location.reload();
        }

        function submitAnswers() {
            var form = document.getElementById('quiz-form');
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "index.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var results = JSON.parse(xhr.responseText).results;
                    for (var questionId in results) {
                        var optionElements = document.querySelectorAll('input[name="answers[' + questionId + ']"]');
                        optionElements.forEach(function (optionElement) {
                            optionElement.parentNode.classList.remove('correct', 'incorrect');
                            if (results[questionId] === 'Correct!') {
                                optionElement.parentNode.classList.add('correct');
                            } else {
                                optionElement.parentNode.classList.add('incorrect');
                            }
                        });
                    }
                }
            };
            xhr.send(formData);
        }

        function selectRadioButton(divId) {
            document.getElementById(divId).checked = true;
        }
    </script>
</body>

</html>
