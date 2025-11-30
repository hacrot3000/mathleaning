<?php
// Mode will be set by index.php
$FORCE_MIXED_MODE = ($mode === 'mixed');
?>
<!-- Exercise content: <?php echo $mode === 'mixed' ? 'Cộng Trừ Hỗn Số' : 'Cộng Trừ Phân Số'; ?> -->
<h1><?php echo $mode === 'mixed' ? $lang['practice_add_subtract_mixed'] : $lang['practice_add_subtract_fractions']; ?></h1>

<div style="font-size: 100%; color: #666; margin-bottom: 20px;">
    <strong><?php echo $lang['difficulty']; ?>:</strong> <span id="difficulty-level"></span>
    <strong><?php echo $lang['question']; ?>:</strong> <span id="question-number"></span>
</div>

<div class="problem" id="problem-display"></div>

<div style="margin: 30px 0;">
    <span class="fraction-label"><?php echo $lang['result']; ?>:</span>
    <div class="fraction-input-group">
        <input type="number" id="answer-numerator" placeholder="<?php echo $lang['numerator']; ?>" autocomplete="off">
        <div class="fraction-line"></div>
        <input type="number" id="answer-denominator" placeholder="<?php echo $lang['denominator']; ?>" autocomplete="off">
    </div>
    <p style="font-size: 70%; color: #999; margin-top: 10px;">
        <em><?php echo $lang['simplified_fraction_note']; ?></em>
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
    var FORCE_MIXED_MODE = <?php echo $FORCE_MIXED_MODE ? 'true' : 'false'; ?>;
    var storageKey = FORCE_MIXED_MODE ? 'phanso_mixed' : 'phanso';
    var storage = createLocalStorageManager(storageKey);
    var problemCountManager = createProblemCountManager(storageKey);

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
            inputSelector: '#answer-numerator, #answer-denominator',
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
        var minVal = config.min;
        var maxVal = config.max;
        var requireNegative = config.require_negative;
        
        var fractions = [];
        var operators = [];
        
        for (var i = 0; i < numOperands; i++) {
            // Generate fraction with mixed number logic
            var frac;
            if (FORCE_MIXED_MODE) {
                frac = generateMixedNumber(CONFIG.mixed_number);
            } else if (problemCount >= CONFIG.mixed_number.start_from && Math.random() < CONFIG.mixed_number.probability) {
                frac = generateMixedNumber(CONFIG.mixed_number);
            } else {
                frac = generateRandomFraction(minVal, maxVal);
            }
            
            fractions.push(frac);
            
            if (i < numOperands - 1) {
                operators.push(Math.random() < 0.5 ? '+' : '-');
            }
        }
        
        if (FORCE_MIXED_MODE) {
            var mixedCount = 0;
            for (var i = 0; i < fractions.length; i++) {
                if (fractions[i].isMixed) mixedCount++;
            }
            
            if (mixedCount === 0) {
                var randomIndex = getRndInteger(0, fractions.length - 1);
                fractions[randomIndex] = generateMixedNumber(CONFIG.mixed_number);
                mixedCount++;
            }
            
            if (mixedCount === 1 && fractions.length > 1 && Math.random() < 0.5) {
                var randomIndex;
                do {
                    randomIndex = getRndInteger(0, fractions.length - 1);
                } while (fractions[randomIndex].isMixed);
                fractions[randomIndex] = generateMixedNumber(CONFIG.mixed_number);
            }
        }
        
        if (requireNegative) {
            var hasNegative = fractions.some(function(f) { return f.num < 0; });
            if (!hasNegative) {
                var randomIndex = getRndInteger(0, fractions.length - 1);
                fractions[randomIndex].num = -Math.abs(fractions[randomIndex].num);
                if (fractions[randomIndex].num === 0) {
                    fractions[randomIndex].num = -1;
                }
            }
        }
        
        // Ensure at most 1 integer (den = 1), rest must be proper fractions
        var integerCount = 0;
        for (var i = 0; i < fractions.length; i++) {
            if (fractions[i].den === 1) {
                integerCount++;
            }
        }
        
        // If all are integers, convert some to fractions
        if (integerCount > 1) {
            var indicesToConvert = [];
            for (var i = 0; i < fractions.length; i++) {
                if (fractions[i].den === 1) {
                    indicesToConvert.push(i);
                }
            }
            
            // Shuffle and convert all but one to fractions
            for (var i = 0; i < indicesToConvert.length - 1; i++) {
                var idx = indicesToConvert[i];
                var num = fractions[idx].num;
                var den = getRndInteger(2, Math.max(2, Math.abs(maxVal)));
                fractions[idx] = {
                    num: num * den + getRndInteger(1, den - 1),
                    den: den,
                    normalized: true
                };
            }
        }
        
        var result = fractions[0];
        for (var i = 0; i < operators.length; i++) {
            if (operators[i] === '+') {
                result = addFractions(result, fractions[i + 1]);
            } else {
                result = subtractFractions(result, fractions[i + 1]);
            }
        }
        
        currentProblem = {
            fractions: fractions,
            operators: operators,
            correctAnswer: result,
            difficulty: difficultyLevel
        };
        
        currentWrongAnswers = [];
        
        displayProblem();
        saveToLocalStorage();
    }

    function formatFractionLatex(frac, addParentheses) {
        if (frac.den === 1) {
            if (addParentheses && frac.num < 0) {
                return '(' + frac.num + ')';
            }
            return frac.num.toString();
        }
        
        if (frac.isMixed && frac.mixedWhole !== undefined) {
            var whole = frac.mixedWhole;
            var num = frac.mixedNumerator;
            var den = frac.mixedDenominator;
            
            var mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
            
            // Calculate total value to check if negative
            var sign = whole >= 0 ? 1 : -1;
            var totalValue = whole + sign * (num / den);
            
            if (addParentheses && totalValue < 0) {
                mixedLatex = '\\left(' + mixedLatex + '\\right)';
            }
            
            return mixedLatex;
        }
        
        var fractionLatex;
        var hasExternalNegativeSign = false;
        
        if (frac.normalized === false && frac.den < 0) {
            if (frac.num < 0) {
                fractionLatex = '\\dfrac{(' + frac.num + ')}{(' + frac.den + ')}';
                hasExternalNegativeSign = false;
            } else {
                fractionLatex = '\\dfrac{' + frac.num + '}{(' + frac.den + ')}';
                hasExternalNegativeSign = false;
            }
        } else {
            var isNegative = frac.num < 0;
            var absNum = Math.abs(frac.num);
            
            if (isNegative) {
                if (Math.random() < 0.3) {
                    fractionLatex = '\\dfrac{(' + frac.num + ')}{' + frac.den + '}';
                    hasExternalNegativeSign = false;
                } else {
                    fractionLatex = '-\\dfrac{' + absNum + '}{' + frac.den + '}';
                    hasExternalNegativeSign = true;
                }
            } else {
                fractionLatex = '\\dfrac{' + absNum + '}{' + frac.den + '}';
                hasExternalNegativeSign = false;
            }
        }
        
        // Add parentheses if requested and fraction is negative
        if (addParentheses) {
            var fractionValue = frac.num / frac.den;
            if (fractionValue < 0) {
                // Always wrap negative fractions in parentheses when addParentheses is true
                fractionLatex = '\\left(' + fractionLatex + '\\right)';
            }
        }
        
        return fractionLatex;
    }
    
    function formatFractionText(frac) {
        if (frac.den === 1) {
            return frac.num.toString();
        }
        
        if (frac.isMixed && frac.mixedWhole !== undefined) {
            var whole = frac.mixedWhole;
            var num = frac.mixedNumerator;
            var den = frac.mixedDenominator;
            
            if (whole < 0) {
                return '(' + whole + ' ' + num + '/' + den + ')';
            } else {
                return whole + ' ' + num + '/' + den;
            }
        }
        
        if (frac.num < 0) {
            return '(-' + Math.abs(frac.num) + '/' + frac.den + ')';
        } else {
            return frac.num + '/' + frac.den;
        }
    }
    
    function renderMath(latex, elementId) {
        try {
            katex.render(latex, document.getElementById(elementId), {
                displayMode: true,
                throwOnError: false
            });
        } catch (e) {
            console.error('KaTeX render error:', e);
            $('#' + elementId).html(latex);
        }
    }

    function displayProblem() {
        if (currentProblem === null) return;
        
        var latex = formatFractionLatex(currentProblem.fractions[0], false);
        
        for (var i = 0; i < currentProblem.operators.length; i++) {
            latex += ' ' + currentProblem.operators[i] + ' ';
            latex += formatFractionLatex(currentProblem.fractions[i + 1], true);
        }
        
        latex += ' = ?';
        
        renderMath(latex, 'problem-display');
        
        clearAnswerInput('#answer-numerator');
        clearAnswerInput('#answer-denominator');
        focusAnswerInput('#answer-numerator');
        hideFeedback();
        
        var difficultyText = '';
        var easyText = t('difficulty_easy', 'Dễ');
        var mediumText = t('difficulty_medium', 'Trung bình');
        var hardText = t('difficulty_hard', 'Khó');
        var numeratorDenominatorText = t('numerator_denominator', 'tử/mẫu');
        var hasNegativeFractionText = t('has_negative_fraction', 'có phân số âm');
        var toText = t('to', 'đến');
        var operatorText = t('operator', 'toán tử');
        
        if (problemCount < CONFIG.easy.threshold) {
            difficultyText = easyText + ' (' + numeratorDenominatorText + ' ' + CONFIG.easy.min + ' ' + toText + ' ' + CONFIG.easy.max + ', ' + (CONFIG.easy.num_operands - 1) + ' ' + operatorText + ')';
        } else if (problemCount < CONFIG.medium.threshold) {
            difficultyText = mediumText + ' (' + hasNegativeFractionText + ', ' + CONFIG.medium.min + ' ' + toText + ' ' + CONFIG.medium.max + ', ' + (CONFIG.medium.num_operands_min - 1) + '-' + (CONFIG.medium.num_operands_max - 1) + ' ' + operatorText + ')';
        } else {
            difficultyText = hardText + ' (' + hasNegativeFractionText + ', ' + CONFIG.hard.min + ' ' + toText + ' ' + CONFIG.hard.max + ', ' + (CONFIG.hard.num_operands_min - 1) + '-' + (CONFIG.hard.num_operands_max - 1) + ' ' + operatorText + ')';
        }
        
        $('#difficulty-level').html(difficultyText);
        $('#question-number').html((problemCount + 1));
    }

    function checkAnswer() {
        var userNum = parseInt($('#answer-numerator').val());
        var userDen = parseInt($('#answer-denominator').val());
        
        if (isNaN(userNum) || isNaN(userDen)) {
            alert(t('enter_numerator_denominator', 'Vui lòng nhập tử số và mẫu số hợp lệ!'));
            return;
        }
        
        if (userDen === 0) {
            alert(t('denominator_not_zero', 'Mẫu số không được bằng 0!'));
            return;
        }
        
        var userAnswer = simplifyFraction(userNum, userDen);
        var correctAnswer = currentProblem.correctAnswer;
        
        if (userAnswer.num === correctAnswer.num && userAnswer.den === correctAnswer.den) {
            showFeedback(true);
            
            problemCount = problemCountManager.increment();
            saveProblemToHistoryLocal(false);
            
            setTimeout(function() {
                generateNewProblem();
            }, 1500);
        } else {
            showFeedback(false);
            
            currentWrongAnswers.push(formatFractionText(userAnswer));
            saveToLocalStorage();
            
            selectAnswerInput('#answer-numerator');
        }
    }

    function skipProblem() {
        standardSkipProblem(saveProblemToHistoryLocal, generateNewProblem);
    }

    function saveProblemToHistoryLocal(skipped) {
        if (!currentProblem || !currentProblem.fractions || !currentProblem.operators) {
            return;
        }
        
        var problemText = formatFractionText(currentProblem.fractions[0]);
        for (var i = 0; i < currentProblem.operators.length; i++) {
            problemText += ' ' + currentProblem.operators[i] + ' ' + formatFractionText(currentProblem.fractions[i + 1]);
        }
        
        var correctAnswerText = formatFractionText(currentProblem.correctAnswer);
        
        saveProblemToHistory({
            problemState: {
                currentProblem: currentProblem,
                currentWrongAnswers: currentWrongAnswers,
                problemHistory: problemHistory,
                historyManager: historyManager
            },
            problemText: problemText,
            correctAnswerText: correctAnswerText,
            skipped: skipped
        });
        
        saveToLocalStorage();
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
