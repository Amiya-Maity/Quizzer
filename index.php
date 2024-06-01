<?php
include ('functions.php');

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
    if (isset($_GET["category"]))
        $category = $_GET["category"];
    if (isset($_GET["count"]))
        $count = $_GET["count"];
    // echo $category;
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
        echo '<li><a href="?category=' . str_replace(" ", "-", $value) . '" class="categories">' . $value . '</a></li></br>';
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
                            <?php
                            if (isset($results)) {
                                $results = array_map(function ($result) {
                                    return str_replace("'", '`', $result);
                                }, $results);
                            }
                            echo $question['Question']; ?>
                        </p>
                        <div class="options-container radio-group">
                            <?php
                            $options = randomizeOptions($question);
                            foreach ($options as $option):
                                ?>
                                <div class="option"
                                    onclick="selectRadioButton('answers[<?php echo $question['Question']; ?>]<?php echo $option; ?>')">
                                    <input type="radio" name="answers[<?php echo $question['Question']; ?>]"
                                        id="answers[<?php echo $question['Question']; ?>]<?php echo $option; ?>"
                                        value="<?php echo $option; ?>">
                                    <label for="answers[<?php echo $question['Question']; ?>]<?php echo $option; ?>">
                                        <?php echo $option; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <input type="submit" id="submit" value="Submit Answers">
                <button onclick="restartPage()">Restart</button>

                </br>
                </br>

            </form>
            </br>
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
        document.getElementById("categories").addEventListener("close", function () {
            document.getElementById("category-list").style.display = "none";
        });
        var submitButtonClickCount = 0;

        document.getElementById('quiz-form').addEventListener('submit', function (event) {
            event.preventDefault();
            submitButtonClickCount++;
            submitAnswers();
            console.log(submitButtonClickCount);
        });
        if (submitButtonClickCount == 0) {
            let myVar = setInterval(myTimer, 1000);
            let time = <?php echo $time; ?>;

            function myTimer() {
                time--;
                let timer = document.getElementById("timer");
                timer.innerHTML = formatTime(time);
                if (time == <?php echo $time; ?> * 0.9) {
                    timer.classList.add("f");
                }
                if (time == 0) {
                    clearInterval(myVar);
                    alert("Time's Up!!");
                    document.getElementById("submit").click();
                }
            }
            function formatTime(seconds) {
                let hours = Math.floor(seconds / 3600);
                let minutes = Math.floor((seconds % 3600) / 60);
                let remainingSeconds = seconds % 60;

                return pad(hours) + ":" + pad(minutes) + ":" + pad(remainingSeconds);
            }

            function pad(number) {
                if (number < 10) {
                    return "0" + number;
                }
                return number;
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
                    var resultContainer = document.getElementById("result");
                    var results = JSON.parse(xhr.responseText).results;

                    var resultHtml = "<h3>Results:</h3>";
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
            var divid = document.getElementById(divId);
            divid.checked = true;
        }
    </script>
</body>

</html>