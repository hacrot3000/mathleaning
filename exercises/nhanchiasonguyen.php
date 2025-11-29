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
    });

    function formatNumber(num) {
        if (num < 0) {
            return '(' + num + ')';
        }
        return num;
    }
    
    function generateNewProblem() {
        var minNum, maxNum;
        var requireNegative = false;
        var operator;
        var operators;
        var difficultyLevel = '';
        
        if (problemCount < CONFIG.easy.threshold) {
            minNum = CONFIG.easy.min;
            maxNum = CONFIG.easy.max;
            requireNegative = CONFIG.easy.require_negative;
            operators = CONFIG.easy.operators;
            difficultyLevel = 'easy';
        } else if (problemCount < CONFIG.medium.threshold) {
            minNum = CONFIG.medium.min;
            maxNum = CONFIG.medium.max;
            requireNegative = CONFIG.medium.require_negative;
            operators = CONFIG.medium.operators;
            difficultyLevel = 'medium';
        } else {
            minNum = CONFIG.hard.min;
            maxNum = CONFIG.hard.max;
            requireNegative = CONFIG.hard.require_negative;
            operators = CONFIG.hard.operators;
            difficultyLevel = 'hard';
        }
        
        operator = operators[Math.floor(Math.random() * operators.length)];
        
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
        
        var difficultyText = '';
        var easyText = t('difficulty_easy', 'Dễ');
        var mediumText = t('difficulty_medium', 'Trung bình');
        var hardText = t('difficulty_hard', 'Khó');
        var multiplyText = t('multiply', 'nhân');
        var divideText = t('divide', 'chia');
        var multiplyDivideText = multiplyText + '/' + divideText;
        var onlyMultiplyText = t('only_multiply', 'chỉ nhân');
        var onlyDivideText = t('only_divide', 'chỉ chia');
        var hasNegativeText = t('has_negative', 'có số âm');
        var numberText = t('number', 'số');
        var operatorText = t('operator', 'toán tử');
        var toText = t('to', 'đến');
        
        var operatorNames = '';
        if (problemCount < CONFIG.easy.threshold) {
            operatorNames = CONFIG.easy.operators.length > 1 ? multiplyDivideText : (CONFIG.easy.operators[0] === '×' ? onlyMultiplyText : onlyDivideText);
            difficultyText = easyText + ' (' + operatorNames + ', ' + numberText + ' ' + CONFIG.easy.min + '-' + CONFIG.easy.max + ')';
        } else if (problemCount < CONFIG.medium.threshold) {
            operatorNames = CONFIG.medium.operators.length > 1 ? multiplyDivideText : (CONFIG.medium.operators[0] === '×' ? onlyMultiplyText : onlyDivideText);
            difficultyText = mediumText + ' (' + operatorNames + ', ' + hasNegativeText + ', ' + CONFIG.medium.min + ' ' + toText + ' ' + CONFIG.medium.max + ')';
        } else {
            operatorNames = CONFIG.hard.operators.length > 1 ? multiplyDivideText : (CONFIG.hard.operators[0] === '×' ? onlyMultiplyText : onlyDivideText);
            difficultyText = hardText + ' (' + operatorNames + ', ' + hasNegativeText + ', ' + CONFIG.hard.min + ' ' + toText + ' ' + CONFIG.hard.max + ')';
        }
        
        $('#difficulty-level').html(difficultyText);
        $('#question-number').html((problemCount + 1));
    }

    function checkAnswer() {
        var userAnswerStr = $('#answer-input').val().trim();
        
        if (userAnswerStr === '') {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        
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
            saveProblemToHistory(false);
            
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
        problemCount++;
        saveProblemToHistory(true);
        generateNewProblem();
    }

    function saveProblemToHistory(skipped) {
        if (!currentProblem) {
            return;
        }
        
        var problemText = formatNumber(currentProblem.num1) + ' ' + 
                          currentProblem.operator + ' ' + 
                          formatNumber(currentProblem.num2);
        
        // Format timestamp as YYYY-MM-DD HH:MM:SS (like server)
        var now = new Date();
        var createdAt = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + ' ' + 
                       String(now.getHours()).padStart(2, '0') + ':' + 
                       String(now.getMinutes()).padStart(2, '0') + ':' + 
                       String(now.getSeconds()).padStart(2, '0');
        
        var historyItem = {
            problem: problemText,
            correctAnswer: currentProblem.correctAnswer,
            wrongAnswers: currentWrongAnswers.slice(),
            skipped: skipped || false,
            createdAt: createdAt
        };
        
        problemHistory.unshift(historyItem);
        
        saveHistoryToServer(
            historyManager,
            problemText,
            currentProblem.correctAnswer.toString(),
            currentWrongAnswers,
            skipped,
            function(err) {
                if (err) console.error('Failed to save history');
            }
        );
        
        displayHistory();
    }

    function saveToLocalStorage() {
        saveToStorage('currentProblemMultDiv', currentProblem);
        saveToStorage('currentWrongAnswersMultDiv', currentWrongAnswers);
    }

    function loadFromLocalStorage() {
        currentProblem = loadFromStorage('currentProblemMultDiv');
        currentWrongAnswers = loadFromStorage('currentWrongAnswersMultDiv') || [];
    }

    // Event handlers
    $('#submit-btn').click(function() {
        checkAnswer();
    });

    $('#skip-btn').click(function() {
        skipProblem();
    });

    setupEnterKeyHandler('#answer-input', checkAnswer);
</script>
