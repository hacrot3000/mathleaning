<?php
$page_title = 'Cộng Trừ Phân Số';
$config_type = 'phanso';
$extra_css = ['phanso.css'];
$use_katex = true;
$use_user = true;
$use_history = true;
$config_general = false;
include '../includes/header.php';
?>
        <div class="container">
            <!-- Header with home button and user info -->
            <div class="container-header">
                <div class="container-header-left">
                    <a href="../" class="home-btn">🏠 <?php echo $lang['home']; ?></a>
                </div>
                <div class="container-header-right">
                    <div id="user-info-display"></div>
                    <?php include '../includes/language-switcher.php'; ?>
                </div>
            </div>
            
            <h1><?php echo $lang['practice_add_subtract_fractions']; ?></h1>
            
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
            
            <?php include '../includes/history-section.php'; ?>
        </div>

        <script type="text/javascript">
            var currentProblem = null;
            var currentWrongAnswers = [];
            var problemHistory = [];
            var problemCount = 0;
            var historyManager = null;

            // Initialize sounds
            $(function () {
                // Check user logged in
                historyManager = initHistoryManager('phanso');
                if (!historyManager) return;
                
                // Display user info
                $('#user-info-display').html(displayUserInfo());
                
                initializeSounds("../lib/ion.sound-3.0.7/sounds/");

                // Load lịch sử từ server
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
                
                // Đảm bảo mẫu luôn dương (dấu âm chuyển lên tử)
                if (den < 0) {
                    num = -num;
                    den = -den;
                }
                
                return {num: num, den: den, normalized: true};
            }
            
            function createFractionVariant(num, den) {
                // Tạo biến thể phân số với dấu âm ở các vị trí khác nhau
                // normalized = false để giữ nguyên dạng không chuẩn hóa
                
                if (num === 0 || den === 1) {
                    return {num: num, den: den, normalized: true};
                }
                
                // 30% giữ dấu âm ở mẫu thay vì tử
                if (Math.random() < 0.3 && num < 0) {
                    return {num: -num, den: -den, normalized: false};
                }
                
                return {num: num, den: den, normalized: true};
            }

            function addFractions(f1, f2) {
                var num = f1.num * f2.den + f2.num * f1.den;
                var den = f1.den * f2.den;
                var result = simplifyFraction(num, den);
                result.normalized = true; // Kết quả luôn chuẩn hóa
                return result;
            }

            function subtractFractions(f1, f2) {
                var num = f1.num * f2.den - f2.num * f1.den;
                var den = f1.den * f2.den;
                var result = simplifyFraction(num, den);
                result.normalized = true; // Kết quả luôn chuẩn hóa
                return result;
            }

            function generateMixedNumber() {
                // Tạo hỗn số: whole + numerator/denominator
                var mixedConfig = CONFIG.mixed_number;
                
                var whole = getRndInteger(mixedConfig.whole_min, mixedConfig.whole_max);
                var numerator = getRndInteger(1, mixedConfig.numerator_max);
                var denominator = getRndInteger(mixedConfig.denominator_min, mixedConfig.denominator_max);
                
                // Đảm bảo tử < mẫu (phần phân số < 1)
                if (numerator >= denominator) {
                    numerator = getRndInteger(1, denominator - 1);
                }
                
                // Rút gọn phần phân số
                var g = gcd(numerator, denominator);
                numerator = numerator / g;
                denominator = denominator / g;
                
                // 30% là hỗn số âm
                if (Math.random() < 0.3) {
                    whole = -whole;
                }
                
                // Chuyển hỗn số thành phân số: whole * den + num / den
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
                // Kiểm tra có tạo hỗn số không
                var mixedConfig = CONFIG.mixed_number;
                if (problemCount >= mixedConfig.start_from && Math.random() < mixedConfig.probability) {
                    return generateMixedNumber();
                }
                
                var num, den;
                do {
                    num = getRndInteger(minVal, maxVal);
                    den = getRndInteger(minVal, maxVal);
                    
                    // Đảm bảo mẫu khác 0
                    if (den === 0) continue;
                    
                    // Không được cả tử và mẫu cùng âm
                    if (num < 0 && den < 0) continue;
                    
                    // Tránh tử = 0 quá nhiều
                    if (num === 0 && Math.random() < 0.7) continue;
                    
                    break;
                } while (true);
                
                // Rút gọn phân số
                var simplified = simplifyFraction(num, den);
                
                // Tạo biến thể ngẫu nhiên (đôi khi giữ dấu ở mẫu)
                return createFractionVariant(simplified.num, simplified.den);
            }

            function generateNewProblem() {
                var numOperands;
                var minVal, maxVal;
                var requireNegative = false;
                var difficultyLevel = '';
                
                // Xác định độ khó dựa trên config
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
                
                // Phát sinh các phân số
                for (var i = 0; i < numOperands; i++) {
                    fractions.push(generateRandomFraction(minVal, maxVal));
                    if (i < numOperands - 1) {
                        operators.push(Math.random() < 0.5 ? '+' : '-');
                    }
                }
                
                // Nếu yêu cầu có số âm, đảm bảo có ít nhất 1 phân số âm
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
                
                // Tính toán kết quả đúng
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
                // Tạo LaTeX syntax cho KaTeX
                // addParentheses = true để thêm dấu ngoặc CHỈ KHI dấu âm ở ngoài phân số
                
                // Trường hợp số nguyên (mẫu = 1)
                if (frac.den === 1) {
                    if (addParentheses && frac.num < 0) {
                        return '(' + frac.num + ')';
                    }
                    return frac.num.toString();
                }
                
                // Trường hợp hỗn số - hiển thị dạng a b/c
                if (frac.isMixed && frac.mixedWhole !== undefined) {
                    var whole = frac.mixedWhole;
                    var num = frac.mixedNumerator;
                    var den = frac.mixedDenominator;
                    
                    var mixedLatex;
                    if (whole < 0) {
                        // Hỗn số âm: -a b/c
                        mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
                        if (addParentheses) {
                            mixedLatex = '\\left(' + mixedLatex + '\\right)';
                        }
                    } else {
                        // Hỗn số dương: a b/c
                        mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
                    }
                    return mixedLatex;
                }
                
                var fractionLatex;
                var hasExternalNegativeSign = false; // Dấu âm có nằm ngoài phân số không?
                
                // Kiểm tra phân số có được chuẩn hóa chưa
                if (frac.normalized === false && frac.den < 0) {
                    // Trường hợp dấu âm ở mẫu: a/(-b) hoặc (-a)/(-b)
                    if (frac.num < 0) {
                        // (-a)/(-b) - dấu âm ở cả tử và mẫu (trong phân số)
                        fractionLatex = '\\dfrac{(' + frac.num + ')}{(' + frac.den + ')}';
                        hasExternalNegativeSign = false; // Dấu đã trong phân số
                    } else {
                        // a/(-b) - dấu âm ở mẫu (trong phân số)
                        fractionLatex = '\\dfrac{' + frac.num + '}{(' + frac.den + ')}';
                        hasExternalNegativeSign = false; // Dấu đã trong phân số
                    }
                } else {
                    // Trường hợp chuẩn (mẫu dương)
                    var isNegative = frac.num < 0;
                    var absNum = Math.abs(frac.num);
                    
                    if (isNegative) {
                        // Đôi khi hiển thị dạng (-a)/b (dấu trong tử)
                        if (Math.random() < 0.3) {
                            fractionLatex = '\\dfrac{(' + frac.num + ')}{' + frac.den + '}';
                            hasExternalNegativeSign = false; // Dấu đã trong tử
                        } else {
                            // Dạng -a/b (dấu ở ngoài phân số)
                            fractionLatex = '-\\dfrac{' + absNum + '}{' + frac.den + '}';
                            hasExternalNegativeSign = true; // Dấu nằm ngoài
                        }
                    } else {
                        fractionLatex = '\\dfrac{' + absNum + '}{' + frac.den + '}';
                        hasExternalNegativeSign = false;
                    }
                }
                
                // CHỈ thêm ngoặc khi:
                // 1. addParentheses = true (không phải phân số đầu)
                // 2. hasExternalNegativeSign = true (dấu âm nằm ngoài phân số)
                if (addParentheses && hasExternalNegativeSign) {
                    fractionLatex = '\\left(' + fractionLatex + '\\right)';
                }
                
                return fractionLatex;
            }
            
            function formatFractionText(frac) {
                // Version văn bản cho lịch sử
                if (frac.den === 1) {
                    return frac.num.toString();
                }
                
                // Trường hợp hỗn số
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
                // Render LaTeX using KaTeX
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
                
                // Tạo LaTeX expression
                // Phân số đầu tiên không cần dấu ngoặc
                var latex = formatFractionLatex(currentProblem.fractions[0], false);
                
                for (var i = 0; i < currentProblem.operators.length; i++) {
                    latex += ' ' + currentProblem.operators[i] + ' ';
                    // Các phân số sau: luôn cho phép thêm ngoặc
                    // Logic bên trong formatFractionLatex sẽ quyết định có thêm ngoặc hay không
                    latex += formatFractionLatex(currentProblem.fractions[i + 1], true);
                }
                
                latex += ' = ?';
                
                // Render using KaTeX
                renderMath(latex, 'problem-display');
                
                clearAnswerInput('#answer-numerator');
                clearAnswerInput('#answer-denominator');
                focusAnswerInput('#answer-numerator');
                hideFeedback();
                
                // Hiển thị độ khó
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
                
                // Rút gọn câu trả lời người dùng
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
                
                var historyItem = {
                    problem: problemText,
                    correctAnswer: correctAnswerText,
                    wrongAnswers: currentWrongAnswers.slice(),
                    skipped: skipped || false
                };
                
                problemHistory.push(historyItem);
                
                // Save to server
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
                // Chỉ lưu bài toán hiện tại (để F5)
                // KHÔNG lưu problemHistory nữa (đã chuyển sang server)
                saveToStorage('currentProblemFraction', currentProblem);
                saveToStorage('currentWrongAnswersFraction', currentWrongAnswers);
            }

            function loadFromLocalStorage() {
                // Load bài toán hiện tại
                // problemHistory sẽ load từ server
                currentProblem = loadFromStorage('currentProblemFraction');
                currentWrongAnswers = loadFromStorage('currentWrongAnswersFraction') || [];
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
        </div> <!-- End container -->
        
        <?php include '../includes/footer.php'; ?>

