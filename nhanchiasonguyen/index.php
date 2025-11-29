<?php
$page_title = 'Nhân Chia Số Nguyên';
$config_type = 'nhanchia';
$extra_css = ['nhanchiasonguyen.css'];
$use_katex = false;
$use_user = true;
$use_history = true;
$config_general = true;
include '../includes/header.php';
?>
        <div class="container">
            <!-- Header with home button and user info -->
            <div class="container-header">
                <div class="container-header-left">
                    <a href="../" class="home-btn">🏠 Trang chủ</a>
                </div>
                <div class="container-header-right" id="user-info-display"></div>
            </div>
            
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
            
            <?php include '../includes/history-section.php'; ?>
        </div>

        <script type="text/javascript">
            var currentProblem = null;
            var currentWrongAnswers = [];
            var problemHistory = [];
            var problemCount = 0; // Đếm số câu đã làm (reset mỗi lần load trang)
            var historyManager = null;

            // Initialize sounds
            $(function () {
                // Check user logged in
                historyManager = initHistoryManager('nhanchiasonguyen');
                if (!historyManager) return;
                
                // Display user info
                $('#user-info-display').html(displayUserInfo());
                
                initializeSounds("../lib/ion.sound-3.0.7/sounds/");

                // Load lịch sử từ server
                loadHistoryFromServer(historyManager, function(err, serverHistory) {
                    problemHistory = serverHistory || [];
                    displayHistory();
                });
                
                // Load bài toán hiện tại từ localStorage
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
                clearAnswerInput();
                focusAnswerInput();
                hideFeedback();
                
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
                    showFeedback(true);
                    
                    // Tăng số câu đã làm
                    problemCount++;
                    
                    // Save to history
                    saveProblemToHistory(false); // false = not skipped
                    
                    // Generate new problem after delay
                    setTimeout(function() {
                        generateNewProblem();
                    }, 1500);
                } else {
                    showFeedback(false);
                    
                    // Track wrong answer
                    currentWrongAnswers.push(userAnswer);
                    saveToLocalStorage();
                    
                    selectAnswerInput();
                }
            }

            function skipProblem() {
                // Tăng số câu đã làm
                problemCount++;
                saveProblemToHistory(true); // true = skipped
                generateNewProblem();
            }

            function saveProblemToHistory(skipped) {
                // Don't save if there's no current problem
                if (!currentProblem) {
                    return;
                }
                
                var problemText = formatNumber(currentProblem.num1) + ' ' + 
                                  currentProblem.operator + ' ' + 
                                  formatNumber(currentProblem.num2);
                
                var historyItem = {
                    problem: problemText,
                    correctAnswer: currentProblem.correctAnswer,
                    wrongAnswers: currentWrongAnswers.slice(),
                    skipped: skipped || false
                };
                
                problemHistory.push(historyItem);
                
                // Save to server
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
                // Chỉ lưu bài toán hiện tại (để F5)
                saveToStorage('currentProblemMultDiv', currentProblem);
                saveToStorage('currentWrongAnswersMultDiv', currentWrongAnswers);
            }

            function loadFromLocalStorage() {
                // Load bài toán hiện tại
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
        </div> <!-- End container -->
        
        <?php include '../includes/footer.php'; ?>

