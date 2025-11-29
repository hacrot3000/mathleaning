<!-- Exercise content: Nhân Chia Số Nguyên -->
<h1><?php echo $lang['practice_multiply_divide_integers']; ?></h1>

<div style="font-size: 100%; color: #666; margin-bottom: 20px;">
    <strong><?php echo $lang['difficulty']; ?>:</strong> <span id="difficulty-level"></span> | 
    <strong><?php echo $lang['question']; ?>:</strong> <span id="question-number"></span>
</div>

<div class="problem" id="problem-display"></div>

<div>
    <input type="text" id="answer-input" placeholder="<?php echo $lang['result']; ?>" autocomplete="off">
    <p style="font-size: 70%; color: #999; margin-top: 5px;">
        <em><?php echo $lang['rounding_note']; ?></em>
    </p>
</div>

<div>
    <button class="submit-btn" id="submit-btn"><?php echo $lang['submit']; ?></button>
    <button class="submit-btn" id="skip-btn" style="background-color: #ff9800;"><?php echo $lang['skip']; ?></button>
</div>

<div id="feedback" class="feedback" style="display: none;"></div>

<script type="text/javascript">
    var currentProblem = null;
    var currentWrongAnswers = [];
    var problemHistory = [];
    var problemCount = 0;
    var historyManager = null;
    var storage = createLocalStorageManager('nhanchia');

    // Initialize
    $(function () {
        historyManager = initHistoryManager('<?php echo $exercise_type; ?>');
        if (!historyManager) return;
        
        $('#user-info-display').html(displayUserInfo());
        initializeSounds("../lib/ion.sound-3.0.7/sounds/");

        loadHistoryFromServer(historyManager, function(err, serverHistory) {
            problemHistory = serverHistory || [];
            displayHistory();
        });
        
        loadFromLocalStorage();
        problemCount = 0;
        
        if (currentProblem === null) {
            generateNewProblem();
        } else {
            displayProblem();
        }
        
        setupStandardEventHandlers({
            inputSelector: '#answer-input',
            checkAnswerFn: checkAnswer,
            skipProblemFn: skipProblem
        });
    });
    
    function generateNewProblem() {
        var difficulty = getDifficultyConfig(problemCount, CONFIG);
        var config = difficulty.config;
        var difficultyLevel = difficulty.difficultyLevel;
        
        var minNum = config.min;
        var maxNum = config.max;
        var requireNegative = config.require_negative;
        var operators = config.operators;
        var operator = operators[Math.floor(Math.random() * operators.length)];
        
        var num1, num2, result;
        
        if (operator === '×') {
            num1 = getRndInteger(minNum, maxNum);
            num2 = getRndInteger(minNum, maxNum);
            
            while (num1 === 0 || num1 === 1 || num1 === -1) {
                num1 = getRndInteger(minNum, maxNum);
            }
            while (num2 === 0 || num2 === 1 || num2 === -1) {
                num2 = getRndInteger(minNum, maxNum);
            }
            
            result = num1 * num2;
        } else {
            var attempts = 0;
            var decimalPlaces = CONFIG_GENERAL.decimal_places;
            var integerRatio = CONFIG_GENERAL.division_integer_ratio;
            
            do {
                num2 = getRndInteger(minNum < 0 ? 2 : minNum, maxNum);
                while (num2 === 0 || num2 === 1 || num2 === -1) {
                    num2 = getRndInteger(minNum < 0 ? 2 : minNum, maxNum);
                }
                
                if (Math.random() < integerRatio) {
                    result = getRndInteger(minNum, maxNum);
                    while (result === 0) {
                        result = getRndInteger(minNum, maxNum);
                    }
                    num1 = result * num2;
                } else {
                    var tempInt = getRndInteger(minNum, maxNum);
                    while (tempInt === 0) {
                        tempInt = getRndInteger(minNum, maxNum);
                    }
                    num1 = tempInt;
                    result = roundToTwoDecimals(num1 / num2);
                    
                    var resultStr = result.toString();
                    var decimalPart = resultStr.split('.')[1];
                    if (decimalPart && decimalPart.length > decimalPlaces) {
                        continue;
                    }
                }
                
                attempts++;
            } while (attempts < 100 && (num1 === 0 || Math.abs(num1) > Math.abs(maxNum * maxNum)));
            
            result = roundToTwoDecimals(num1 / num2);
        }
        
        if (requireNegative && num1 > 0 && num2 > 0) {
            if (Math.random() < 0.5) {
                num1 = -num1;
            } else {
                num2 = -num2;
            }
            if (operator === '×') {
                result = num1 * num2;
            } else {
                result = roundToTwoDecimals(num1 / num2);
            }
        }
        
        currentProblem = {
            num1: num1,
            num2: num2,
            operator: operator,
            correctAnswer: result,
            difficulty: difficultyLevel
        };
        
        currentWrongAnswers = [];
        
        displayProblem();
        saveToLocalStorage();
    }

    function displayProblem() {
        if (currentProblem === null) return;
        
        var problemText = formatNumber(currentProblem.num1) + ' ' + 
                          currentProblem.operator + ' ' + 
                          formatNumber(currentProblem.num2) + ' = ???';
        
        $('#problem-display').html(problemText);
        clearAnswerInput();
        focusAnswerInput();
        hideFeedback();
        
        $('#difficulty-level').html(getDifficultyText(currentProblem.difficulty));
        $('#question-number').html((problemCount + 1));
    }

    function checkAnswer() {
        var userAnswerStr = $('#answer-input').val().trim();
        
        if (userAnswerStr === '') {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        
        // Normalize: replace comma with dot for decimal separator
        userAnswerStr = userAnswerStr.replace(',', '.');
        
        var userAnswer = parseFloat(userAnswerStr);
        
        if (isNaN(userAnswer)) {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        
        userAnswer = roundToTwoDecimals(userAnswer);
        var correctAnswer = roundToTwoDecimals(currentProblem.correctAnswer);
        
        if (Math.abs(userAnswer - correctAnswer) < 0.01) {
            showFeedback(true);
            
            problemCount++;
            saveProblemToHistoryLocal(false);
            
            setTimeout(function() {
                generateNewProblem();
            }, 1500);
        } else {
            showFeedback(false);
            
            currentWrongAnswers.push(userAnswer);
            saveToLocalStorage();
            
            selectAnswerInput();
        }
    }

    function skipProblem() {
        standardSkipProblem(saveProblemToHistoryLocal, generateNewProblem);
    }

    function saveProblemToHistoryLocal(skipped) {
        if (!currentProblem) {
            return;
        }
        
        var problemText = formatNumber(currentProblem.num1) + ' ' + 
                          currentProblem.operator + ' ' + 
                          formatNumber(currentProblem.num2);
        
        saveProblemToHistory({
            problemState: {
                currentProblem: currentProblem,
                currentWrongAnswers: currentWrongAnswers,
                problemHistory: problemHistory,
                historyManager: historyManager
            },
            problemText: problemText,
            correctAnswerText: currentProblem.correctAnswer.toString(),
            skipped: skipped
        });
    }

    function saveToLocalStorage() {
        storage.saveState(currentProblem, currentWrongAnswers);
    }

    function loadFromLocalStorage() {
        var state = storage.loadState();
        currentProblem = state.currentProblem;
        currentWrongAnswers = state.currentWrongAnswers;
    }
</script>
