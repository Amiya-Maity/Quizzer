document.getElementById("categories").addEventListener("click", function () {
    let categoryList = document.getElementById("category-list");
    categoryList.classList.toggle("hidden");
});

var submitButtonClickCount = 0;
document.getElementById('quiz-form').addEventListener('submit', function (event) {
    event.preventDefault();
    submitButtonClickCount++;
    submitAnswers();
});

if (submitButtonClickCount === 0) {
    let myVar = setInterval(myTimer, 1000);
    let time = parseInt(document.getElementById("timer").getAttribute("data-time"));

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
        let minutes = Math.floor((seconds % 3600) / 60);
        let remainingSeconds = seconds % 60;
        return pad(minutes) + ":" + pad(remainingSeconds);
    }

    function pad(number) {
        return number < 10 ? "0" + number : number;
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
        if (xhr.readyState === 4 && xhr.status === 200) {
            var resultContainer = document.getElementById("result");
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
