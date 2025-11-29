<?php
$page_title = 'Cộng Trừ Số Nguyên';
$config_type = 'congtru';
$extra_css = [];
$use_katex = false;
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
            
            <!-- <h1>Luyện Tập Cộng Trừ Số Nguyên</h1> -->
            
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
            
            <?php include '../includes/history-section.php'; ?>
        </div>

        <script type="text/javascript">
            var currentProblem = null;
            var currentWrongAnswers = [];
            var problemHistory = [];
            var problemCount = 0; // Đếm số câu đã làm (reset mỗi lần load trang)
            var historyManager = null; // Manager for server history

            // Initialize sounds
            $(function () {
                // Check user logged in and init history manager
                historyManager = initHistoryManager('congtrusonguyen');
                if (!historyManager) return; // Will redirect to home
                
                // Display user info
                $('#user-info-display').html(displayUserInfo());
                
                initializeSounds("../lib/ion.sound-3.0.7/sounds/");

                // Load lịch sử từ server
                loadHistoryFromServer(historyManager, function(err, serverHistory) {
                    problemHistory = serverHistory || [];
                    displayHistory();
                });
                
                // Load bài toán hiện tại từ localStorage (chỉ để F5)
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
                var numOperands;
                var minNum, maxNum;
                var requireNegative = false;
                var difficultyLevel = '';
                
                // Xác định độ khó dựa trên số câu đã làm (sử dụng config)
                if (problemCount < CONFIG.easy.threshold) {
                    // Độ khó Dễ
                    numOperands = CONFIG.easy.num_operands;
                    minNum = CONFIG.easy.min;
                    maxNum = CONFIG.easy.max;
                    requireNegative = CONFIG.easy.require_negative;
                    difficultyLevel = 'easy';
                } else if (problemCount < CONFIG.medium.threshold) {
                    // Độ khó Trung bình
                    numOperands = getRndInteger(CONFIG.medium.num_operands_min, CONFIG.medium.num_operands_max);
                    minNum = CONFIG.medium.min;
                    maxNum = CONFIG.medium.max;
                    requireNegative = CONFIG.medium.require_negative;
                    difficultyLevel = 'medium';
                } else {
                    // Độ khó Khó
                    numOperands = getRndInteger(CONFIG.hard.num_operands_min, CONFIG.hard.num_operands_max);
                    minNum = CONFIG.hard.min;
                    maxNum = CONFIG.hard.max;
                    requireNegative = CONFIG.hard.require_negative;
                    difficultyLevel = 'hard';
                }
                
                var numbers = [];
                var operators = [];
                
                // Phát sinh các số
                for (var i = 0; i < numOperands; i++) {
                    numbers.push(getRndInteger(minNum, maxNum));
                    if (i < numOperands - 1) {
                        operators.push(Math.random() < 0.5 ? '+' : '-');
                    }
                }
                
                // Nếu yêu cầu có số âm, đảm bảo có ít nhất 1 số âm
                if (requireNegative) {
                    var hasNegative = false;
                    for (var i = 0; i < numbers.length; i++) {
                        if (numbers[i] < 0) {
                            hasNegative = true;
                            break;
                        }
                    }
                    
                    // Nếu chưa có số âm, chọn ngẫu nhiên một vị trí để đổi thành số âm
                    if (!hasNegative) {
                        var randomIndex = getRndInteger(0, numbers.length - 1);
                        numbers[randomIndex] = -Math.abs(numbers[randomIndex]);
                        // Nếu số đó là 0, đổi thành -1
                        if (numbers[randomIndex] === 0) {
                            numbers[randomIndex] = -1;
                        }
                    }
                }
                
                // Tính toán kết quả đúng
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

            function formatNumber(num) {
                if (num < 0) {
                    return '(' + num + ')';
                }
                return num;
            }

            function displayProblem() {
                if (currentProblem === null) return;
                
                var problemText = formatNumber(currentProblem.numbers[0]);
                
                for (var i = 0; i < currentProblem.operators.length; i++) {
                    problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
                }
                
                // problemText += ' = ???';
                
                $('#problem-display').html(problemText);
                clearAnswerInput();
                focusAnswerInput();
                hideFeedback();
                
                // Hiển thị độ khó và số câu hỏi
                var difficultyText = '';
                var easyText = t('difficulty_easy', 'Dễ');
                var mediumText = t('difficulty_medium', 'Trung bình');
                var hardText = t('difficulty_hard', 'Khó');
                
                if (problemCount < CONFIG.easy.threshold) {
                    difficultyText = easyText + ' (số ' + CONFIG.easy.min + ' đến ' + CONFIG.easy.max + ', ' + (CONFIG.easy.num_operands - 1) + ' toán tử)';
                } else if (problemCount < CONFIG.medium.threshold) {
                    difficultyText = mediumText + ' (có số âm, ' + CONFIG.medium.min + ' đến ' + CONFIG.medium.max + ', ' + (CONFIG.medium.num_operands_min - 1) + '-' + (CONFIG.medium.num_operands_max - 1) + ' toán tử)';
                } else {
                    difficultyText = hardText + ' (có số âm, ' + CONFIG.hard.min + ' đến ' + CONFIG.hard.max + ', ' + (CONFIG.hard.num_operands_min - 1) + '-' + (CONFIG.hard.num_operands_max - 1) + ' toán tử)';
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
                if (!currentProblem || !currentProblem.numbers || !currentProblem.operators) {
                    return;
                }
                
                var problemText = formatNumber(currentProblem.numbers[0]);
                
                for (var i = 0; i < currentProblem.operators.length; i++) {
                    problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
                }
                
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
                        if (err) console.error('Failed to save history to server');
                    }
                );
                
                displayHistory();
            }


            function saveToLocalStorage() {
                // Chỉ lưu bài toán hiện tại và câu trả lời sai (để F5)
                // KHÔNG lưu problemHistory nữa (đã chuyển sang server)
                saveToStorage('currentProblem_congtru', currentProblem);
                saveToStorage('currentWrongAnswers_congtru', currentWrongAnswers);
            }

            function loadFromLocalStorage() {
                // Load bài toán hiện tại và câu trả lời sai
                // problemHistory sẽ load từ server
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
        </div> <!-- End container -->
        
        <?php include '../includes/footer.php'; ?>

