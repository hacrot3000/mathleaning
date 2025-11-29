<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Cộng Trừ Phân Số</title>
        <link rel="stylesheet" href="../css/common.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
        <style type="text/css">
            /* Override colors for fraction operations */
            .fraction-input-group {
                display: inline-block;
                vertical-align: middle;
                margin: 0 10px;
            }
            .fraction-input-group input {
                font-size: 150%;
                padding: 8px 15px;
                width: 100px;
                text-align: center;
                border: 2px solid #E91E63;
                border-radius: 5px;
            }
            .fraction-line {
                display: block;
                width: 100%;
                height: 2px;
                background-color: #E91E63;
                margin: 5px 0;
            }
            .fraction-label {
                font-size: 90%;
                color: #666;
            }
            .submit-btn {
                background-color: #E91E63;
            }
            .submit-btn:hover {
                background-color: #C2185B;
            }
            .history h3 {
                border-bottom-color: #E91E63;
            }
            .history-item {
                border-left-color: #E91E63;
            }
            .history-problem {
                color: #E91E63;
            }
            .problem {
                font-size: 200%;
                min-height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            /* KaTeX custom styling */
            .katex {
                font-size: 1.5em;
            }
            .katex-display {
                margin: 20px 0;
            }
        </style>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js"></script>
        <?php require_once '../config.php'; ?>
        <script type="text/javascript">
            // Load config from PHP
            var CONFIG = <?php echo getConfigAsJSON('phanso'); ?>;
        </script>
    </head>
    <body class="with-padding">
        <a href="../" class="home-btn">🏠 Trang chủ</a>
        
        <div class="container">
            <h1>Luyện Tập Cộng Trừ Phân Số</h1>
            
            <div style="font-size: 100%; color: #666; margin-bottom: 20px;">
                <strong>Độ khó:</strong> <span id="difficulty-level"></span>
                <strong>Câu hỏi:</strong> <span id="question-number"></span>
            </div>
            
            <div class="problem" id="problem-display"></div>
            
            <div style="margin: 30px 0;">
                <span class="fraction-label">Kết quả:</span>
                <div class="fraction-input-group">
                    <input type="number" id="answer-numerator" placeholder="Tử số" autocomplete="off">
                    <div class="fraction-line"></div>
                    <input type="number" id="answer-denominator" placeholder="Mẫu số" autocomplete="off">
                </div>
                <p style="font-size: 70%; color: #999; margin-top: 10px;">
                    <em>* Nhập phân số tối giản (rút gọn đến dạng đơn giản nhất)</em>
                </p>
            </div>
            
            <div>
                <button class="submit-btn" id="submit-btn">Kiểm tra</button>
                <button class="submit-btn" id="skip-btn" style="background-color: #ff9800;">Bỏ qua</button>
            </div>
            
            <div id="feedback" class="feedback" style="display: none;"></div>
            
            <div class="history">
                <h3>Lịch sử các bài đã làm</h3>
                <div id="history-list"></div>
                <button class="clear-history-btn" id="clear-history-btn">Xóa lịch sử</button>
            </div>
        </div>

        <script type="text/javascript">
            var currentProblem = null;
            var currentWrongAnswers = [];
            var problemHistory = [];
            var problemCount = 0;

            // Initialize sounds
            $(function () {
                ion.sound({
                    sounds: [
                        {name: "light_bulb_breaking"},
                        {name: "bell_ring"},
                    ],
                    path: "../lib/ion.sound-3.0.7/sounds/",
                    preload: true,
                    multiplay: true,
                    volume: 1
                });

                loadFromLocalStorage();
                problemCount = 0;
                
                if (currentProblem === null) {
                    generateNewProblem();
                } else {
                    displayProblem();
                }
                
                displayHistory();
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

            function generateRandomFraction(minVal, maxVal) {
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
                
                $('#answer-numerator').val('');
                $('#answer-denominator').val('');
                $('#answer-numerator').focus();
                $('#feedback').hide();
                
                // Hiển thị độ khó
                var difficultyText = '';
                if (problemCount < CONFIG.easy.threshold) {
                    difficultyText = 'Dễ (tử/mẫu ' + CONFIG.easy.min + ' đến ' + CONFIG.easy.max + ', ' + (CONFIG.easy.num_operands - 1) + ' toán tử)';
                } else if (problemCount < CONFIG.medium.threshold) {
                    difficultyText = 'Trung bình (có phân số âm, ' + CONFIG.medium.min + ' đến ' + CONFIG.medium.max + ', ' + (CONFIG.medium.num_operands_min - 1) + '-' + (CONFIG.medium.num_operands_max - 1) + ' toán tử)';
                } else {
                    difficultyText = 'Khó (có phân số âm, ' + CONFIG.hard.min + ' đến ' + CONFIG.hard.max + ', ' + (CONFIG.hard.num_operands_min - 1) + '-' + (CONFIG.hard.num_operands_max - 1) + ' toán tử)';
                }
                
                $('#difficulty-level').html(difficultyText);
                $('#question-number').html((problemCount + 1));
            }

            function checkAnswer() {
                var userNum = parseInt($('#answer-numerator').val());
                var userDen = parseInt($('#answer-denominator').val());
                
                if (isNaN(userNum) || isNaN(userDen)) {
                    alert('Vui lòng nhập tử số và mẫu số hợp lệ!');
                    return;
                }
                
                if (userDen === 0) {
                    alert('Mẫu số không được bằng 0!');
                    return;
                }
                
                // Rút gọn câu trả lời người dùng
                var userAnswer = simplifyFraction(userNum, userDen);
                var correctAnswer = currentProblem.correctAnswer;
                
                if (userAnswer.num === correctAnswer.num && userAnswer.den === correctAnswer.den) {
                    $('#feedback').removeClass('incorrect').addClass('correct');
                    $('#feedback').html('✓ Chính xác!');
                    $('#feedback').show();
                    ion.sound.play("bell_ring");
                    
                    problemCount++;
                    saveProblemToHistory(false);
                    
                    setTimeout(function() {
                        generateNewProblem();
                    }, 1500);
                } else {
                    $('#feedback').removeClass('correct').addClass('incorrect');
                    $('#feedback').html('✗ Sai rồi! Thử lại. (Đáp án đúng: ' + formatFractionText(correctAnswer) + ')');
                    $('#feedback').show();
                    ion.sound.play("light_bulb_breaking");
                    
                    currentWrongAnswers.push(formatFractionText(userAnswer));
                    saveToLocalStorage();
                    
                    $('#answer-numerator').select();
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
                
                problemHistory.push({
                    problem: problemText,
                    correctAnswer: formatFractionText(currentProblem.correctAnswer),
                    wrongAnswers: currentWrongAnswers.slice(),
                    skipped: skipped || false
                });
                
                saveToLocalStorage();
                displayHistory();
            }

            function displayHistory() {
                var html = '';
                
                if (problemHistory.length === 0) {
                    html = '<p style="color: #999;">Chưa có lịch sử</p>';
                } else {
                    for (var i = problemHistory.length - 1; i >= 0; i--) {
                        var item = problemHistory[i];
                        
                        if (!item || typeof item.problem === 'undefined') {
                            continue;
                        }
                        
                        var itemClass = item.skipped ? 'history-item history-item-skipped' : 'history-item';
                        var skippedLabel = item.skipped ? '<span style="background-color: #ff9800; color: white; padding: 2px 8px; border-radius: 3px; font-size: 80%; margin-right: 5px; font-weight: bold;">BỎ QUA</span>' : '';
                        
                        html += '<div class="' + itemClass + '">';
                        html += skippedLabel;
                        html += '<span class="history-problem">' + item.problem + '</span> = ';
                        html += '<span class="history-correct">' + item.correctAnswer + '</span>';
                        
                        if (item.wrongAnswers && item.wrongAnswers.length > 0) {
                            html += '; <span class="history-wrong">(' + item.wrongAnswers.join(', ') + ')</span>';
                        }
                        
                        html += '</div>';
                    }
                    
                    if (html === '') {
                        html = '<p style="color: #999;">Chưa có lịch sử</p>';
                    }
                }
                
                $('#history-list').html(html);
            }

            function saveToLocalStorage() {
                localStorage.setItem('currentProblemFraction', JSON.stringify(currentProblem));
                localStorage.setItem('currentWrongAnswersFraction', JSON.stringify(currentWrongAnswers));
                localStorage.setItem('problemHistoryFraction', JSON.stringify(problemHistory));
            }

            function loadFromLocalStorage() {
                var savedProblem = localStorage.getItem('currentProblemFraction');
                var savedWrongAnswers = localStorage.getItem('currentWrongAnswersFraction');
                var savedHistory = localStorage.getItem('problemHistoryFraction');
                
                if (savedProblem) {
                    currentProblem = JSON.parse(savedProblem);
                }
                
                if (savedWrongAnswers) {
                    currentWrongAnswers = JSON.parse(savedWrongAnswers);
                }
                
                if (savedHistory) {
                    problemHistory = JSON.parse(savedHistory);
                }
            }

            function clearHistory() {
                if (confirm('Bạn có chắc muốn xóa toàn bộ lịch sử?')) {
                    problemHistory = [];
                    localStorage.removeItem('problemHistoryFraction');
                    displayHistory();
                }
            }

            // Event handlers
            $('#submit-btn').click(function() {
                checkAnswer();
            });

            $('#skip-btn').click(function() {
                skipProblem();
            });

            $('#answer-numerator, #answer-denominator').keypress(function(e) {
                if (e.which === 13) {
                    checkAnswer();
                }
            });

            $('#clear-history-btn').click(function() {
                clearHistory();
            });
        </script>
    </body>
</html>

