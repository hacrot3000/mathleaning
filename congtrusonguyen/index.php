<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Cộng Trừ Số Nguyên</title>
        <style type="text/css">
            body {
                font-size: 150%;
                text-align: center;
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                padding: 20px;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .problem {
                font-size: 250%;
                margin: 30px 0;
                color: #333;
            }
            #answer-input {
                font-size: 200%;
                padding: 10px 20px;
                width: 200px;
                text-align: center;
                border: 3px solid #4CAF50;
                border-radius: 5px;
            }
            .submit-btn {
                font-size: 150%;
                padding: 12px 30px;
                margin: 20px 10px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .submit-btn:hover {
                background-color: #45a049;
            }
            .feedback {
                font-size: 180%;
                margin: 20px 0;
                padding: 15px;
                border-radius: 5px;
                font-weight: bold;
            }
            .correct {
                background-color: #d4edda;
                color: #155724;
            }
            .incorrect {
                background-color: #f8d7da;
                color: #721c24;
            }
            .history {
                margin-top: 40px;
                text-align: left;
            }
            .history h3 {
                color: #333;
                border-bottom: 2px solid #4CAF50;
                padding-bottom: 10px;
            }
            .history-item {
                padding: 10px;
                margin: 8px 0;
                border-radius: 5px;
                background-color: #f9f9f9;
                border-left: 4px solid #4CAF50;
            }
            .history-problem {
                font-weight: bold;
                color: #2196F3;
                font-size: 110%;
            }
            .history-correct {
                color: #4CAF50;
                font-weight: bold;
            }
            .history-wrong {
                color: #f44336;
                font-style: italic;
            }
            .clear-history-btn {
                font-size: 100%;
                padding: 8px 20px;
                margin: 10px 0;
                background-color: #f44336;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .clear-history-btn:hover {
                background-color: #da190b;
            }
        </style>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>Luyện Tập Cộng Trừ Số Nguyên</h1>
            
            <div style="font-size: 100%; color: #666; margin-bottom: 20px;">
                <strong>Độ khó:</strong> <span id="difficulty-level"></span> | 
                <strong>Câu hỏi:</strong> <span id="question-number"></span>
            </div>
            
            <div class="problem" id="problem-display"></div>
            
            <div>
                <input type="number" id="answer-input" placeholder="Nhập kết quả" autocomplete="off">
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

            function generateNewProblem() {
                var numOperands;
                var minNum, maxNum;
                var requireNegative = false;
                
                // Xác định độ khó dựa trên số câu đã làm
                if (problemCount < 5) {
                    // 5 câu đầu: số 1-2 chữ số, chỉ có 1 toán tử (2 số hạng)
                    numOperands = 2;
                    minNum = -99;
                    maxNum = 99;
                    requireNegative = false;
                } else if (problemCount < 15) {
                    // 10 câu tiếp theo (câu 6-15): luôn có ít nhất 1 số âm, số 1-2 chữ số, 2-3 toán tử
                    numOperands = getRndInteger(2, 3);
                    minNum = -99;
                    maxNum = 99;
                    requireNegative = true;
                } else {
                    // Các câu sau: luôn có ít nhất 1 số âm, 2-3 toán tử, số -1000 đến 1000
                    numOperands = getRndInteger(2, 3);
                    minNum = -1000;
                    maxNum = 1000;
                    requireNegative = true;
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
                    correctAnswer: result
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
                
                problemText += ' = ???';
                
                $('#problem-display').html(problemText);
                $('#answer-input').val('');
                $('#answer-input').focus();
                $('#feedback').hide();
                
                // Hiển thị độ khó và số câu hỏi
                var difficultyText = '';
                if (problemCount < 5) {
                    difficultyText = 'Dễ (số 1-2 chữ số, 1 toán tử)';
                } else if (problemCount < 15) {
                    difficultyText = 'Trung bình (có số âm, 1-2 chữ số, 2-3 toán tử)';
                } else {
                    difficultyText = 'Khó (có số âm, -1000 đến 1000, 2-3 toán tử)';
                }
                
                $('#difficulty-level').html(difficultyText);
                $('#question-number').html((problemCount + 1));
            }

            function checkAnswer() {
                var userAnswer = parseInt($('#answer-input').val());
                
                if (isNaN(userAnswer)) {
                    alert('Vui lòng nhập một số hợp lệ!');
                    return;
                }
                
                if (userAnswer === currentProblem.correctAnswer) {
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
                if (!currentProblem || !currentProblem.numbers || !currentProblem.operators) {
                    return;
                }
                
                var problemText = formatNumber(currentProblem.numbers[0]);
                
                for (var i = 0; i < currentProblem.operators.length; i++) {
                    problemText += ' ' + currentProblem.operators[i] + ' ' + formatNumber(currentProblem.numbers[i + 1]);
                }
                
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
                localStorage.setItem('currentProblem', JSON.stringify(currentProblem));
                localStorage.setItem('currentWrongAnswers', JSON.stringify(currentWrongAnswers));
                localStorage.setItem('problemHistory', JSON.stringify(problemHistory));
            }

            function loadFromLocalStorage() {
                // Load cả bài toán hiện tại, câu trả lời sai, và lịch sử
                var savedProblem = localStorage.getItem('currentProblem');
                var savedWrongAnswers = localStorage.getItem('currentWrongAnswers');
                var savedHistory = localStorage.getItem('problemHistory');
                
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
                    localStorage.removeItem('problemHistory');
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

