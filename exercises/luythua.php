<!-- Exercise content: Luyện Tập Luỹ Thừa -->
<h1><?php echo $lang['practice_power']; ?></h1>

<div style="font-size: 100%; color: #666; margin-bottom: 20px;">
    <strong><?php echo $lang['difficulty']; ?>:</strong> <span id="difficulty-level"></span>
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
    var storage = createLocalStorageManager('luythua');
    var problemCountManager = createProblemCountManager('luythua');

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
        problemCount = problemCountManager.get();
        
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

    // Generate different types of numbers
    function generateInteger(min, max) {
        return {
            type: 'integer',
            value: getRndInteger(min, max)
        };
    }

    function generateDecimal(min, max, places) {
        return {
            type: 'decimal',
            value: getRndDecimal(min, max, places)
        };
    }

    function generateFraction(min, max) {
        var num, den;
        do {
            num = getRndInteger(min, max);
            den = getRndInteger(Math.max(2, min), max);
            if (den === 0) continue;
            if (num < 0 && den < 0) continue;
            if (num === 0 && Math.random() < 0.7) continue;
            break;
        } while (true);
        
        var simplified = simplifyFraction(num, den);
        return {
            type: 'fraction',
            num: simplified.num,
            den: simplified.den
        };
    }

    function generateMixedNumberForPower(wholeMin, wholeMax, numMax, denMin, denMax) {
        var whole = getRndInteger(wholeMin, wholeMax);
        var numerator = getRndInteger(1, numMax);
        var denominator = getRndInteger(denMin, denMax);
        
        if (numerator >= denominator) {
            numerator = getRndInteger(1, denominator - 1);
        }
        
        var g = gcd(numerator, denominator);
        numerator = numerator / g;
        denominator = denominator / g;
        
        if (Math.random() < 0.3) {
            whole = -whole;
        }
        
        return {
            type: 'mixed',
            whole: whole,
            num: numerator,
            den: denominator
        };
    }

    function generateComposite(config) {
        var operator = ['+', '-', '×', '÷'][getRndInteger(0, 3)];
        var types = ['integer', 'decimal'];
        if (config.number_types.indexOf('fraction') >= 0) types.push('fraction');
        
        var type1 = types[getRndInteger(0, types.length - 1)];
        var type2 = types[getRndInteger(0, types.length - 1)];
        
        var left, right;
        
        if (type1 === 'integer') {
            left = generateInteger(config.integer_min, config.integer_max);
        } else if (type1 === 'decimal') {
            left = generateDecimal(config.integer_min, config.integer_max, config.decimal_places);
        } else {
            left = generateFraction(config.fraction_min, config.fraction_max);
        }
        
        if (type2 === 'integer') {
            right = generateInteger(config.integer_min, config.integer_max);
        } else if (type2 === 'decimal') {
            right = generateDecimal(config.integer_min, config.integer_max, config.decimal_places);
        } else {
            right = generateFraction(config.fraction_min, config.fraction_max);
        }
        
        return {
            type: 'composite',
            left: left,
            right: right,
            operator: operator
        };
    }

    function generateOperand(config) {
        var types = config.number_types.slice();
        
        // Remove composite from normal selection, handle separately
        var compositeIndex = types.indexOf('composite');
        if (compositeIndex >= 0) {
            types.splice(compositeIndex, 1);
        }
        
        // Check if should generate composite
        if (config.allow_composite && Math.random() < (config.composite_probability || 0.4)) {
            return generateComposite(config);
        }
        
        // Generate normal number
        var type = types[getRndInteger(0, types.length - 1)];
        
        if (type === 'integer') {
            return generateInteger(config.integer_min, config.integer_max);
        } else if (type === 'decimal') {
            return generateDecimal(config.integer_min, config.integer_max, config.decimal_places);
        } else if (type === 'fraction') {
            return generateFraction(config.fraction_min, config.fraction_max);
        } else if (type === 'mixed') {
            return generateMixedNumberForPower(
                config.mixed_whole_min,
                config.mixed_whole_max,
                config.mixed_num_max,
                config.mixed_den_min,
                config.mixed_den_max
            );
        }
        
        return generateInteger(config.integer_min, config.integer_max);
    }

    // Calculate value of operand
    function calculateOperand(operand) {
        if (operand.type === 'integer' || operand.type === 'decimal') {
            return operand.value;
        } else if (operand.type === 'fraction') {
            return operand.num / operand.den;
        } else if (operand.type === 'mixed') {
            var sign = operand.whole >= 0 ? 1 : -1;
            return operand.whole + sign * (operand.num / operand.den);
        } else if (operand.type === 'composite') {
            var left = calculateOperand(operand.left);
            var right = calculateOperand(operand.right);
            
            if (operand.operator === '+') return left + right;
            if (operand.operator === '-') return left - right;
            if (operand.operator === '×') return left * right;
            if (operand.operator === '÷') return left / right;
        }
        return 0;
    }

    // Format operand to LaTeX
    function formatOperandLatex(operand, addParentheses) {
        if (operand.type === 'integer') {
            var val = operand.value;
            if (addParentheses && val < 0) {
                return '(' + val + ')';
            }
            return val.toString();
        } else if (operand.type === 'decimal') {
            var val = operand.value;
            if (addParentheses && val < 0) {
                return '(' + val + ')';
            }
            return val.toString();
        } else if (operand.type === 'fraction') {
            var num = operand.num;
            var den = operand.den;
            
            if (den === 1) {
                return addParentheses && num < 0 ? '(' + num + ')' : num.toString();
            }
            
            var latex = num < 0 ? '-\\dfrac{' + Math.abs(num) + '}{' + den + '}' : '\\dfrac{' + num + '}{' + den + '}';
            
            if (addParentheses && num < 0) {
                latex = '\\left(' + latex + '\\right)';
            }
            
            return latex;
        } else if (operand.type === 'mixed') {
            var whole = operand.whole;
            var num = operand.num;
            var den = operand.den;
            
            var latex = whole + '\\dfrac{' + num + '}{' + den + '}';
            
            if (addParentheses && whole < 0) {
                latex = '\\left(' + latex + '\\right)';
            }
            
            return latex;
        } else if (operand.type === 'composite') {
            var leftLatex = formatOperandLatex(operand.left, false);
            var rightLatex = formatOperandLatex(operand.right, 
                operand.operator === '+' || operand.operator === '-' ? 
                (operand.right.type === 'fraction' || operand.right.type === 'mixed') && calculateOperand(operand.right) < 0 : false
            );
            
            return '\\left(' + leftLatex + operand.operator + rightLatex + '\\right)';
        }
        
        return '0';
    }

    // Format operand to text for history
    function formatOperandText(operand) {
        if (operand.type === 'integer' || operand.type === 'decimal') {
            return operand.value.toString();
        } else if (operand.type === 'fraction') {
            if (operand.den === 1) return operand.num.toString();
            return operand.num < 0 ? '(-' + Math.abs(operand.num) + '/' + operand.den + ')' : operand.num + '/' + operand.den;
        } else if (operand.type === 'mixed') {
            var whole = operand.whole;
            var num = operand.num;
            var den = operand.den;
            return whole < 0 ? '(' + whole + ' ' + num + '/' + den + ')' : whole + ' ' + num + '/' + den;
        } else if (operand.type === 'composite') {
            return '(' + formatOperandText(operand.left) + operand.operator + formatOperandText(operand.right) + ')';
        }
        return '0';
    }

    function generateNewProblem() {
        var difficulty = getDifficultyConfig(problemCount, CONFIG);
        var config = difficulty.config;
        var difficultyLevel = difficulty.difficultyLevel;
        
        var numOperands = getRndInteger(config.num_operands_min, config.num_operands_max);
        var operands = [];
        var powers = [];
        var operators = [];
        
        // Generate operands
        for (var i = 0; i < numOperands; i++) {
            operands.push(generateOperand(config));
            
            // Generate power (0 means no power)
            if (Math.random() < config.power_probability) {
                powers.push(getRndInteger(config.power_min, config.power_max));
            } else {
                powers.push(0); // No power
            }
            
            if (i < numOperands - 1) {
                operators.push(['+', '-', '×', '÷'][getRndInteger(0, 3)]);
            }
        }
        
        // Ensure at least one operand has a power > 1 (this is a power exercise!)
        // Power 1 is valid, but not all powers should be 1 or 0
        var hasPowerGreaterThanOne = powers.some(function(p) { return p > 1; });
        if (!hasPowerGreaterThanOne) {
            // Find operands with power 0 or 1
            var candidates = [];
            for (var j = 0; j < powers.length; j++) {
                if (powers[j] <= 1) {
                    candidates.push(j);
                }
            }
            
            if (candidates.length > 0) {
                // Randomly select one operand to add power > 1
                var randomIndex = candidates[getRndInteger(0, candidates.length - 1)];
                // Ensure power is at least 2
                var minPower = Math.max(2, config.power_min);
                powers[randomIndex] = getRndInteger(minPower, config.power_max);
            } else {
                // Fallback: if somehow all are > 1 but we still need to ensure, 
                // just pick a random one and ensure it's > 1
                var randomIndex = getRndInteger(0, numOperands - 1);
                var minPower = Math.max(2, config.power_min);
                powers[randomIndex] = getRndInteger(minPower, config.power_max);
            }
        }
        
        // Calculate result
        var values = [];
        for (var i = 0; i < operands.length; i++) {
            var baseValue = calculateOperand(operands[i]);
            var value = powers[i] > 0 ? Math.pow(baseValue, powers[i]) : baseValue;
            values.push(value);
        }
        
        var result = values[0];
        for (var i = 0; i < operators.length; i++) {
            if (operators[i] === '+') result += values[i + 1];
            else if (operators[i] === '-') result -= values[i + 1];
            else if (operators[i] === '×') result *= values[i + 1];
            else if (operators[i] === '÷') result /= values[i + 1];
        }
        
        result = roundToTwoDecimals(result);
        
        currentProblem = {
            operands: operands,
            powers: powers,
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
        
        var latex = '';
        
        for (var i = 0; i < currentProblem.operands.length; i++) {
            var operandLatex = formatOperandLatex(currentProblem.operands[i], false); // Don't add parentheses here
            
            // Add power if exists
            if (currentProblem.powers[i] > 0) {
                // Wrap in parentheses if complex or negative
                var needsParens = currentProblem.operands[i].type === 'composite' || 
                                  currentProblem.operands[i].type === 'fraction' ||
                                  currentProblem.operands[i].type === 'mixed' ||
                                  (currentProblem.operands[i].type === 'integer' && currentProblem.operands[i].value < 0) ||
                                  (currentProblem.operands[i].type === 'decimal' && currentProblem.operands[i].value < 0);
                
                if (needsParens && currentProblem.operands[i].type !== 'composite') {
                    operandLatex = '\\left(' + operandLatex + '\\right)';
                }
                
                operandLatex += '^{' + currentProblem.powers[i] + '}';
            } else if (i > 0) {
                // Add parentheses for negative numbers when NOT using power
                var operandValue = calculateOperand(currentProblem.operands[i]);
                if (operandValue < 0) {
                    // For fractions and mixed numbers, wrap in parentheses
                    if (currentProblem.operands[i].type === 'fraction' || currentProblem.operands[i].type === 'mixed') {
                        operandLatex = '\\left(' + operandLatex + '\\right)';
                    } else {
                        // For integers and decimals, simple parentheses
                        operandLatex = '(' + operandLatex + ')';
                    }
                }
            }
            
            if (i > 0) {
                latex += ' ' + currentProblem.operators[i - 1] + ' ';
            }
            
            latex += operandLatex;
        }
        
        latex += ' = ?';
        
        renderMath(latex, 'problem-display');
        
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
        
        // Normalize: replace comma with dot
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
            
            problemCount = problemCountManager.increment();
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
        
        // Format problem text for history
        var problemText = '';
        for (var i = 0; i < currentProblem.operands.length; i++) {
            var operandText = formatOperandText(currentProblem.operands[i]);
            
            if (currentProblem.powers[i] > 0) {
                operandText += '^' + currentProblem.powers[i];
            }
            
            if (i > 0) {
                problemText += ' ' + currentProblem.operators[i - 1] + ' ';
            }
            
            problemText += operandText;
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

