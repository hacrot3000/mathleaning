<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Nhân Chia Số Nguyên</title>
        <link rel="stylesheet" href="../css/common.css">
        <style type="text/css">
            /* Override colors for multiplication/division */
            #answer-input {
                border-color: #9C27B0;
            }
            .submit-btn {
                background-color: #9C27B0;
            }
            .submit-btn:hover {
                background-color: #7B1FA2;
            }
            .history h3 {
                border-bottom-color: #9C27B0;
            }
            .history-item {
                border-left-color: #9C27B0;
            }
            .history-problem {
                color: #9C27B0;
            }
        </style>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js"></script>
        <?php require_once '../config.php'; ?>
        <script type="text/javascript">
            // Load config from PHP
            var CONFIG = <?php echo getConfigAsJSON('nhanchia'); ?>;
            var CONFIG_GENERAL = <?php echo getConfigAsJSON('general'); ?>;
        </script>
    </head>
    <body class="with-padding">
        <a href="../" class="home-btn">🏠 Trang chủ</a>
        
        <div class="container">
            <h1>Luyện Tập Nhân Chia Số Nguyên</h1>
            
            <div style="font-size: 100%; color: #666; margin-bottom: 20px;">
                <strong>Độ khó:</strong> <span id="difficulty-level"></span> | 
                <strong>Câu hỏi:</strong> <span id="question-number"></span>
            </div>
            
            <div class="problem" id="problem-display"></div>
            
            <div>
                <input type="text" id="answer-input" placeholder="Kết quả" autocomplete="off">
                <p style="font-size: 70%; color: #999; margin-top: 5px;">
                    <em>* Kết quả làm tròn đến phần trăm (2 chữ số thập phân)</em>
                </p>
            </div>
            
            <div>
                <button class="submit-btn" id="submit-btn">Kiểm tra</button>
                <button class="submit-btn" id="skip-btn" style="background-color: #ff9800;">Bỏ qua</button>
            </div>
            
            <div id="feedback" class="feedback" style="display: none;"></div>
            
            <div class="history">
                <h3>Lịch sử các bài đã làm</h3>
                <button class="clear-history-btn" id="clear-history-btn">Xóa lịch sử</button>
                <div id="history-list"></div>
            </div>
        </div>

        <script type="text/javascript">
            var currentProblem = null;
            var currentWrongAnswers = [];
            var problemHistory = [];
            var problemCount = 0; // Đếm số câu đã làm (reset mỗi lần load trang)

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

                // Load lịch sử và bài toán hiện tại từ localStorage
                loadFromLocalStorage();
                
                // Reset độ khó về 0 mỗi lần load trang
                problemCount = 0;
                
                // Nếu có bài toán đang làm dở, hiển thị lại
                // Nếu không có, tạo bài toán mới
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

            function roundToTwoDecimals(num) {
                return Math.round(num * 100) / 100;
            }

            function generateNewProblem() {
                var minNum, maxNum;
                var requireNegative = false;
                var operator;
                var operators;
                var difficultyLevel = '';
                
                // Xác định độ khó dựa trên số câu đã làm (sử dụng config)
                if (problemCount < CONFIG.easy.threshold) {
                    // Độ khó Dễ
                    minNum = CONFIG.easy.min;
                    maxNum = CONFIG.easy.max;
                    requireNegative = CONFIG.easy.require_negative;
                    operators = CONFIG.easy.operators;
                    difficultyLevel = 'easy';
                } else if (problemCount < CONFIG.medium.threshold) {
                    // Độ khó Trung bình
                    minNum = CONFIG.medium.min;
                    maxNum = CONFIG.medium.max;
                    requireNegative = CONFIG.medium.require_negative;
                    operators = CONFIG.medium.operators;
                    difficultyLevel = 'medium';
                } else {
                    // Độ khó Khó
                    minNum = CONFIG.hard.min;
                    maxNum = CONFIG.hard.max;
                    requireNegative = CONFIG.hard.require_negative;
                    operators = CONFIG.hard.operators;
                    difficultyLevel = 'hard';
                }
                
                // Chọn toán tử ngẫu nhiên từ danh sách
                operator = operators[Math.floor(Math.random() * operators.length)];
                
                var num1, num2, result;
                
                if (operator === '×') {
                    // Phép nhân
                    num1 = getRndInteger(minNum, maxNum);
                    num2 = getRndInteger(minNum, maxNum);
                    
                    // Tránh nhân với 0 hoặc 1
                    while (num1 === 0 || num1 === 1 || num1 === -1) {
                        num1 = getRndInteger(minNum, maxNum);
                    }
                    while (num2 === 0 || num2 === 1 || num2 === -1) {
                        num2 = getRndInteger(minNum, maxNum);
                    }
                    
                    result = num1 * num2;
                } else {
                    // Phép chia - đảm bảo kết quả là số nguyên hoặc thập phân tối đa n chữ số
                    var attempts = 0;
                    var decimalPlaces = CONFIG_GENERAL.decimal_places;
                    var integerRatio = CONFIG_GENERAL.division_integer_ratio;
                    
                    do {
                        num2 = getRndInteger(minNum < 0 ? 2 : minNum, maxNum);
                        // Tránh chia cho 0, 1, -1
                        while (num2 === 0 || num2 === 1 || num2 === -1) {
                            num2 = getRndInteger(minNum < 0 ? 2 : minNum, maxNum);
                        }
                        
                        // Tạo kết quả trước (số nguyên hoặc thập phân có tối đa n chữ số)
                        if (Math.random() < integerRatio) {
                            // Phần trăm integerRatio là số nguyên
                            result = getRndInteger(minNum, maxNum);
                            while (result === 0) {
                                result = getRndInteger(minNum, maxNum);
                            }
                            num1 = result * num2;
                        } else {
                            // Còn lại là thập phân (tạo từ phép chia đơn giản)
                            var tempInt = getRndInteger(minNum, maxNum);
                            while (tempInt === 0) {
                                tempInt = getRndInteger(minNum, maxNum);
                            }
                            num1 = tempInt;
                            result = roundToTwoDecimals(num1 / num2);
                            
                            // Kiểm tra kết quả có đúng tối đa n chữ số thập phân không
                            var resultStr = result.toString();
                            var decimalPart = resultStr.split('.')[1];
                            if (decimalPart && decimalPart.length > decimalPlaces) {
                                continue; // Thử lại
                            }
                        }
                        
                        attempts++;
                    } while (attempts < 100 && (num1 === 0 || Math.abs(num1) > Math.abs(maxNum * maxNum)));
                    
                    // Verify result
                    result = roundToTwoDecimals(num1 / num2);
                }
                
                // Nếu yêu cầu có số âm, đảm bảo có ít nhất 1 số âm
                if (requireNegative && num1 > 0 && num2 > 0) {
                    if (Math.random() < 0.5) {
                        num1 = -num1;
                    } else {
                        num2 = -num2;
                    }
                    // Tính lại result
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

            function formatNumber(num) {
                if (num < 0) {
                    return '(' + num + ')';
                }
                return num;
            }

            function displayProblem() {
                if (currentProblem === null) return;
                
                var problemText = formatNumber(currentProblem.num1) + ' ' + 
                                  currentProblem.operator + ' ' + 
                                  formatNumber(currentProblem.num2) + ' = ???';
                
                $('#problem-display').html(problemText);
                $('#answer-input').val('');
                $('#answer-input').focus();
                $('#feedback').hide();
                
                // Hiển thị độ khó và số câu hỏi
                var difficultyText = '';
                var operatorNames = '';
                if (problemCount < CONFIG.easy.threshold) {
                    operatorNames = CONFIG.easy.operators.length > 1 ? 'nhân/chia' : (CONFIG.easy.operators[0] === '×' ? 'chỉ nhân' : 'chỉ chia');
                    difficultyText = 'Dễ (' + operatorNames + ', số ' + CONFIG.easy.min + '-' + CONFIG.easy.max + ')';
                } else if (problemCount < CONFIG.medium.threshold) {
                    operatorNames = CONFIG.medium.operators.length > 1 ? 'nhân/chia' : (CONFIG.medium.operators[0] === '×' ? 'chỉ nhân' : 'chỉ chia');
                    difficultyText = 'Trung bình (' + operatorNames + ', có số âm, ' + CONFIG.medium.min + ' đến ' + CONFIG.medium.max + ')';
                } else {
                    operatorNames = CONFIG.hard.operators.length > 1 ? 'nhân/chia' : (CONFIG.hard.operators[0] === '×' ? 'chỉ nhân' : 'chỉ chia');
                    difficultyText = 'Khó (' + operatorNames + ', có số âm, ' + CONFIG.hard.min + ' đến ' + CONFIG.hard.max + ')';
                }
                
                $('#difficulty-level').html(difficultyText);
                $('#question-number').html((problemCount + 1));
            }

            function checkAnswer() {
                var userAnswerStr = $('#answer-input').val().trim();
                
                if (userAnswerStr === '') {
                    alert('Vui lòng nhập một số hợp lệ!');
                    return;
                }
                
                var userAnswer = parseFloat(userAnswerStr);
                
                if (isNaN(userAnswer)) {
                    alert('Vui lòng nhập một số hợp lệ!');
                    return;
                }
                
                // Round user answer to 2 decimals for comparison
                userAnswer = roundToTwoDecimals(userAnswer);
                var correctAnswer = roundToTwoDecimals(currentProblem.correctAnswer);
                
                if (Math.abs(userAnswer - correctAnswer) < 0.01) {
                    $('#feedback').removeClass('incorrect').addClass('correct');
                    $('#feedback').html('✓ Chính xác!');
                    $('#feedback').show();
                    ion.sound.play("bell_ring");
                    
                    // Tăng số câu đã làm
                    problemCount++;
                    
                    // Save to history
                    saveProblemToHistory();
                    
                    // Generate new problem after delay
                    setTimeout(function() {
                        generateNewProblem();
                    }, 1500);
                } else {
                    $('#feedback').removeClass('correct').addClass('incorrect');
                    $('#feedback').html('✗ Sai rồi! Thử lại.');
                    $('#feedback').show();
                    ion.sound.play("light_bulb_breaking");
                    
                    // Track wrong answer
                    currentWrongAnswers.push(userAnswer);
                    saveToLocalStorage();
                    
                    $('#answer-input').select();
                }
            }

            function skipProblem() {
                // Tăng số câu đã làm
                problemCount++;
                saveProblemToHistory();
                generateNewProblem();
            }

            function saveProblemToHistory() {
                // Don't save if there's no current problem
                if (!currentProblem) {
                    return;
                }
                
                var problemText = formatNumber(currentProblem.num1) + ' ' + 
                                  currentProblem.operator + ' ' + 
                                  formatNumber(currentProblem.num2);
                
                problemHistory.push({
                    problem: problemText,
                    correctAnswer: currentProblem.correctAnswer,
                    wrongAnswers: currentWrongAnswers.slice()
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
                        
                        // Skip invalid items
                        if (!item || typeof item.problem === 'undefined') {
                            continue;
                        }
                        
                        html += '<div class="history-item">';
                        html += '<span class="history-problem">' + item.problem + '</span> = ';
                        html += '<span class="history-correct">' + item.correctAnswer + '</span>';
                        
                        if (item.wrongAnswers && item.wrongAnswers.length > 0) {
                            html += '; <span class="history-wrong">(' + item.wrongAnswers.join(', ') + ')</span>';
                        }
                        
                        html += '</div>';
                    }
                    
                    // If no valid items were rendered, show empty message
                    if (html === '') {
                        html = '<p style="color: #999;">Chưa có lịch sử</p>';
                    }
                }
                
                $('#history-list').html(html);
            }

            function saveToLocalStorage() {
                // Lưu cả bài toán hiện tại, câu trả lời sai, và lịch sử
                localStorage.setItem('currentProblemMultDiv', JSON.stringify(currentProblem));
                localStorage.setItem('currentWrongAnswersMultDiv', JSON.stringify(currentWrongAnswers));
                localStorage.setItem('problemHistoryMultDiv', JSON.stringify(problemHistory));
            }

            function loadFromLocalStorage() {
                // Load cả bài toán hiện tại, câu trả lời sai, và lịch sử
                var savedProblem = localStorage.getItem('currentProblemMultDiv');
                var savedWrongAnswers = localStorage.getItem('currentWrongAnswersMultDiv');
                var savedHistory = localStorage.getItem('problemHistoryMultDiv');
                
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
                    localStorage.removeItem('problemHistoryMultDiv');
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

            $('#answer-input').keypress(function(e) {
                if (e.which === 13) { // Enter key
                    checkAnswer();
                }
            });

            $('#clear-history-btn').click(function() {
                clearHistory();
            });
        </script>
    </body>
</html>

