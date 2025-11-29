<!-- Exercise content: Cộng Trừ Số Nguyên -->
<div style="font-size: 100%; color: #666; margin-bottom: 20px;">
    <strong><?php echo $lang['difficulty']; ?>:</strong> <span id="difficulty-level"></span>
    <strong><?php echo $lang['question']; ?>:</strong> <span id="question-number"></span>
</div>

<div class="problem" id="problem-display"></div>

<div>
    <input type="number" id="answer-input" placeholder="<?php echo $lang['result']; ?>" autocomplete="off">
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
        var numOperands;
        var minNum, maxNum;
        var requireNegative = false;
        var difficultyLevel = '';
        
        if (problemCount < CONFIG.easy.threshold) {
            numOperands = CONFIG.easy.num_operands;
            minNum = CONFIG.easy.min;
            maxNum = CONFIG.easy.max;
            requireNegative = CONFIG.easy.require_negative;
            difficultyLevel = 'easy';
        } else if (problemCount < CONFIG.medium.threshold) {
            numOperands = getRndInteger(CONFIG.medium.num_operands_min, CONFIG.medium.num_operands_max);
            minNum = CONFIG.medium.min;
            maxNum = CONFIG.medium.max;
            requireNegative = CONFIG.medium.require_negative;
            difficultyLevel = 'medium';
        } else {
            numOperands = getRndInteger(CONFIG.hard.num_operands_min, CONFIG.hard.num_operands_max);
            minNum = CONFIG.hard.min;
            maxNum = CONFIG.hard.max;
            requireNegative = CONFIG.hard.require_negative;
            difficultyLevel = 'hard';
        }
        
        var numbers = [];
        var operators = [];
        
        for (var i = 0; i < numOperands; i++) {
            numbers.push(getRndInteger(minNum, maxNum));
            if (i < numOperands - 1) {
                operators.push(Math.random() < 0.5 ? '+' : '-');
            }
        }
        
        if (requireNegative) {
            var hasNegative = false;
            for (var i = 0; i < numbers.length; i++) {
                if (numbers[i] < 0) {
                    hasNegative = true;
                    break;
                }
            }
            
            if (!hasNegative) {
                var randomIndex = getRndInteger(0, numbers.length - 1);
                numbers[randomIndex] = -Math.abs(numbers[randomIndex]);
                if (numbers[randomIndex] === 0) {
                    numbers[randomIndex] = -1;
                }
            }
        }
        
        var result = numbers[0];
        for (var i = 0; i < operators.length; i++) {
            if (operators[i] === '+') {
                result += numbers[i + 1];
            } else {
                result -= numbers[i + 1];
            }
        }
        
        currentProblem = {
            numbers: numbers,
            operators: operators,
            correctAnswer: result,
            difficulty: difficultyLevel
        };
        
        currentWrongAnswers = [];
        
        displayProblem();
        saveToLocalStorage();
    }

    function displayProblem() {
        if (currentProblem === null) return;
        
        var problemText = formatNumber(currentProblem.numbers[0]);
        
        for (var i = 0; i < currentProblem.operators.length; i++) {
            problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
        }
        
        $('#problem-display').html(problemText);
        clearAnswerInput();
        focusAnswerInput();
        hideFeedback();
        
        var difficultyText = '';
        var easyText = t('difficulty_easy', 'Dễ');
        var mediumText = t('difficulty_medium', 'Trung bình');
        var hardText = t('difficulty_hard', 'Khó');
        
        var hasNegativeText = t('has_negative', 'có số âm');
        var numberText = t('number', 'số');
        var toText = t('to', 'đến');
        var operatorText = t('operator', 'toán tử');
        
        if (problemCount < CONFIG.easy.threshold) {
            difficultyText = easyText + ' (' + numberText + ' ' + CONFIG.easy.min + ' ' + toText + ' ' + CONFIG.easy.max + ', ' + (CONFIG.easy.num_operands - 1) + ' ' + operatorText + ')';
        } else if (problemCount < CONFIG.medium.threshold) {
            difficultyText = mediumText + ' (' + hasNegativeText + ', ' + CONFIG.medium.min + ' ' + toText + ' ' + CONFIG.medium.max + ', ' + (CONFIG.medium.num_operands_min - 1) + '-' + (CONFIG.medium.num_operands_max - 1) + ' ' + operatorText + ')';
        } else {
            difficultyText = hardText + ' (' + hasNegativeText + ', ' + CONFIG.hard.min + ' ' + toText + ' ' + CONFIG.hard.max + ', ' + (CONFIG.hard.num_operands_min - 1) + '-' + (CONFIG.hard.num_operands_max - 1) + ' ' + operatorText + ')';
        }
        
        $('#difficulty-level').html(difficultyText);
        $('#question-number').html((problemCount + 1));
    }

    function checkAnswer() {
        var userAnswer = parseInt($('#answer-input').val());
        
        if (isNaN(userAnswer)) {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        
        if (userAnswer === currentProblem.correctAnswer) {
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
        if (!currentProblem || !currentProblem.numbers || !currentProblem.operators) {
            return;
        }
        
        var problemText = formatNumber(currentProblem.numbers[0]);
        
        for (var i = 0; i < currentProblem.operators.length; i++) {
            problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
        }
        
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
                if (err) console.error('Failed to save history to server');
            }
        );
        
        displayHistory();
    }

    function saveToLocalStorage() {
        saveToStorage('currentProblem_congtru', currentProblem);
        saveToStorage('currentWrongAnswers_congtru', currentWrongAnswers);
    }

    function loadFromLocalStorage() {
        currentProblem = loadFromStorage('currentProblem_congtru');
        currentWrongAnswers = loadFromStorage('currentWrongAnswers_congtru') || [];
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
