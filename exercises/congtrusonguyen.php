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
    var storage = createLocalStorageManager('congtru');

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
        
        var numOperands = config.num_operands || 
            getRndInteger(config.num_operands_min, config.num_operands_max);
        var minNum = config.min;
        var maxNum = config.max;
        var requireNegative = config.require_negative;
        
        var numbers = [];
        var operators = [];
        
        for (var i = 0; i < numOperands; i++) {
            numbers.push(getRndInteger(minNum, maxNum));
            if (i < numOperands - 1) {
                operators.push(Math.random() < 0.5 ? '+' : '-');
            }
        }
        
        if (requireNegative) {
            var hasNegative = numbers.some(function(n) { return n < 0; });
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
        
        $('#difficulty-level').html(getDifficultyText(currentProblem.difficulty));
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
        if (!currentProblem || !currentProblem.numbers || !currentProblem.operators) {
            return;
        }
        
        var problemText = formatNumber(currentProblem.numbers[0]);
        for (var i = 0; i < currentProblem.operators.length; i++) {
            problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
        }
        
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
