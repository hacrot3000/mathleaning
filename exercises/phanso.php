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

    function getRndInteger(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function gcd(a, b) {
        a = Math.abs(a);
        b = Math.abs(b);
        while (b !== 0) {
            var t = b;
            b = a % b;
            a = t;
        }
        return a;
    }

    function simplifyFraction(num, den) {
        if (den === 0) return {num: 0, den: 1, normalized: true};
        
        var g = gcd(num, den);
        num = num / g;
        den = den / g;
        
        if (den < 0) {
            num = -num;
            den = -den;
        }
        
        return {num: num, den: den, normalized: true};
    }
    
    function createFractionVariant(num, den) {
        if (num === 0 || den === 1) {
            return {num: num, den: den, normalized: true};
        }
        
        if (Math.random() < 0.3 && num < 0) {
            return {num: -num, den: -den, normalized: false};
        }
        
        return {num: num, den: den, normalized: true};
    }

    function addFractions(f1, f2) {
        var num = f1.num * f2.den + f2.num * f1.den;
        var den = f1.den * f2.den;
        var result = simplifyFraction(num, den);
        result.normalized = true;
        return result;
    }

    function subtractFractions(f1, f2) {
        var num = f1.num * f2.den - f2.num * f1.den;
        var den = f1.den * f2.den;
        var result = simplifyFraction(num, den);
        result.normalized = true;
        return result;
    }

    function generateMixedNumber() {
        var mixedConfig = CONFIG.mixed_number;
        
        var whole = getRndInteger(mixedConfig.whole_min, mixedConfig.whole_max);
        var numerator = getRndInteger(1, mixedConfig.numerator_max);
        var denominator = getRndInteger(mixedConfig.denominator_min, mixedConfig.denominator_max);
        
        if (numerator >= denominator) {
            numerator = getRndInteger(1, denominator - 1);
        }
        
        var g = gcd(numerator, denominator);
        numerator = numerator / g;
        denominator = denominator / g;
        
        if (Math.random() < 0.3) {
            whole = -whole;
        }
        
        var improperNum = whole * denominator + (whole >= 0 ? numerator : -numerator);
        
        return {
            num: improperNum,
            den: denominator,
            normalized: true,
            isMixed: true,
            mixedWhole: whole,
            mixedNumerator: numerator,
            mixedDenominator: denominator
        };
    }
    
    function generateRandomFraction(minVal, maxVal) {
        var mixedConfig = CONFIG.mixed_number;
        
        if (FORCE_MIXED_MODE) {
            return generateMixedNumber();
        }
        
        if (problemCount >= mixedConfig.start_from && Math.random() < mixedConfig.probability) {
            return generateMixedNumber();
        }
        
        var num, den;
        do {
            num = getRndInteger(minVal, maxVal);
            den = getRndInteger(minVal, maxVal);
            
            if (den === 0) continue;
            if (num < 0 && den < 0) continue;
            if (num === 0 && Math.random() < 0.7) continue;
            
            break;
        } while (true);
        
        var simplified = simplifyFraction(num, den);
        return createFractionVariant(simplified.num, simplified.den);
    }

    function generateNewProblem() {
        var numOperands;
        var minVal, maxVal;
        var requireNegative = false;
        var difficultyLevel = '';
        
        if (problemCount < CONFIG.easy.threshold) {
            numOperands = CONFIG.easy.num_operands;
            minVal = CONFIG.easy.min;
            maxVal = CONFIG.easy.max;
            requireNegative = CONFIG.easy.require_negative;
            difficultyLevel = 'easy';
        } else if (problemCount < CONFIG.medium.threshold) {
            numOperands = getRndInteger(CONFIG.medium.num_operands_min, CONFIG.medium.num_operands_max);
            minVal = CONFIG.medium.min;
            maxVal = CONFIG.medium.max;
            requireNegative = CONFIG.medium.require_negative;
            difficultyLevel = 'medium';
        } else {
            numOperands = getRndInteger(CONFIG.hard.num_operands_min, CONFIG.hard.num_operands_max);
            minVal = CONFIG.hard.min;
            maxVal = CONFIG.hard.max;
            requireNegative = CONFIG.hard.require_negative;
            difficultyLevel = 'hard';
        }
        
        var fractions = [];
        var operators = [];
        
        for (var i = 0; i < numOperands; i++) {
            fractions.push(generateRandomFraction(minVal, maxVal));
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
                fractions[randomIndex] = generateMixedNumber();
                mixedCount++;
            }
            
            if (mixedCount === 1 && fractions.length > 1 && Math.random() < 0.5) {
                var randomIndex;
                do {
                    randomIndex = getRndInteger(0, fractions.length - 1);
                } while (fractions[randomIndex].isMixed);
                fractions[randomIndex] = generateMixedNumber();
            }
        }
        
        if (requireNegative) {
            var hasNegative = false;
            for (var i = 0; i < fractions.length; i++) {
                if (fractions[i].num < 0) {
                    hasNegative = true;
                    break;
                }
            }
            
            if (!hasNegative) {
                var randomIndex = getRndInteger(0, fractions.length - 1);
                fractions[randomIndex].num = -Math.abs(fractions[randomIndex].num);
                if (fractions[randomIndex].num === 0) {
                    fractions[randomIndex].num = -1;
                }
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
            
            var mixedLatex;
            if (whole < 0) {
                mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
                if (addParentheses) {
                    mixedLatex = '\\left(' + mixedLatex + '\\right)';
                }
            } else {
                mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
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
        
        if (addParentheses && hasExternalNegativeSign) {
            fractionLatex = '\\left(' + fractionLatex + '\\right)';
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
            
            problemCount++;
            saveProblemToHistory(false);
            
            setTimeout(function() {
                generateNewProblem();
            }, 1500);
        } else {
            var errorMsg = t('incorrect', 'Sai') + '! ' + t('try_again', 'Thử lại') + '. (' + (typeof LANG !== 'undefined' ? LANG.correct : 'Đúng') + ': ' + formatFractionText(correctAnswer) + ')';
            showFeedback(false, '✗ ' + errorMsg);
            
            currentWrongAnswers.push(formatFractionText(userAnswer));
            saveToLocalStorage();
            
            selectAnswerInput('#answer-numerator');
        }
    }

    function skipProblem() {
        problemCount++;
        saveProblemToHistory(true);
        generateNewProblem();
    }

    function saveProblemToHistory(skipped) {
        if (!currentProblem || !currentProblem.fractions || !currentProblem.operators) {
            return;
        }
        
        var problemText = formatFractionText(currentProblem.fractions[0]);
        
        for (var i = 0; i < currentProblem.operators.length; i++) {
            problemText += ' ' + currentProblem.operators[i] + ' ' + formatFractionText(currentProblem.fractions[i + 1]);
        }
        
        var correctAnswerText = formatFractionText(currentProblem.correctAnswer);
        
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
            correctAnswer: correctAnswerText,
            wrongAnswers: currentWrongAnswers.slice(),
            skipped: skipped || false,
            createdAt: createdAt
        };
        
        problemHistory.unshift(historyItem);
        
        saveHistoryToServer(
            historyManager,
            problemText,
            correctAnswerText,
            currentWrongAnswers,
            skipped,
            function(err) {
                if (err) console.error('Failed to save history to server');
            }
        );
        
        saveToLocalStorage();
        displayHistory();
    }

    function saveToLocalStorage() {
        var storageKey = FORCE_MIXED_MODE ? 'currentProblemFractionMixed' : 'currentProblemFraction';
        var wrongAnswersKey = FORCE_MIXED_MODE ? 'currentWrongAnswersFractionMixed' : 'currentWrongAnswersFraction';
        saveToStorage(storageKey, currentProblem);
        saveToStorage(wrongAnswersKey, currentWrongAnswers);
    }

    function loadFromLocalStorage() {
        var storageKey = FORCE_MIXED_MODE ? 'currentProblemFractionMixed' : 'currentProblemFraction';
        var wrongAnswersKey = FORCE_MIXED_MODE ? 'currentWrongAnswersFractionMixed' : 'currentWrongAnswersFraction';
        currentProblem = loadFromStorage(storageKey);
        currentWrongAnswers = loadFromStorage(wrongAnswersKey) || [];
    }

    // Event handlers
    $('#submit-btn').click(function() {
        checkAnswer();
    });

    $('#skip-btn').click(function() {
        skipProblem();
    });

    setupEnterKeyHandler('#answer-numerator, #answer-denominator', checkAnswer);
</script>
