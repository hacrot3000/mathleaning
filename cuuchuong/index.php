<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Bảng Cửu Chương</title>
        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
            
            body {
                font-family: Arial, sans-serif;
                background: #43e97b;
                background: -webkit-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -moz-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -o-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                min-height: 100vh;
                padding: 20px;
                text-align: center;
            }
            
            .container {
                max-width: 900px;
                margin: 0 auto;
                background-color: white;
                padding: 30px;
                -webkit-border-radius: 15px;
                -moz-border-radius: 15px;
                border-radius: 15px;
                -webkit-box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                -moz-box-shadow: 0 5px 20px rgba(0,0,0,0.2);
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            }
            
            h1 {
                color: #333;
                margin-bottom: 20px;
                font-size: 2em;
            }
            
            .selection-form {
                font-size: 1.2em;
                line-height: 2.5;
            }
            
            .selection-form label {
                display: inline-block;
                margin: 5px 15px;
                cursor: pointer;
                padding: 5px 10px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-transition: background-color 0.2s;
                -moz-transition: background-color 0.2s;
                -o-transition: background-color 0.2s;
                transition: background-color 0.2s;
            }
            
            .selection-form label:hover {
                background-color: #f0f0f0;
            }
            
            .selection-form input[type="checkbox"] {
                width: 20px;
                height: 20px;
                vertical-align: middle;
                margin-right: 8px;
            }
            
            .selection-form button {
                font-size: 1.3em;
                padding: 15px 50px;
                margin-top: 30px;
                background: #43e97b;
                background: -webkit-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -moz-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -o-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                color: white;
                border: none;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                cursor: pointer;
                font-weight: bold;
                -webkit-box-shadow: 0 4px 15px rgba(67, 233, 123, 0.4);
                -moz-box-shadow: 0 4px 15px rgba(67, 233, 123, 0.4);
                box-shadow: 0 4px 15px rgba(67, 233, 123, 0.4);
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                -o-transition: all 0.3s;
                transition: all 0.3s;
            }
            
            .selection-form button:hover {
                -webkit-transform: translateY(-2px);
                -moz-transform: translateY(-2px);
                -ms-transform: translateY(-2px);
                -o-transform: translateY(-2px);
                transform: translateY(-2px);
                -webkit-box-shadow: 0 6px 20px rgba(67, 233, 123, 0.6);
                -moz-box-shadow: 0 6px 20px rgba(67, 233, 123, 0.6);
                box-shadow: 0 6px 20px rgba(67, 233, 123, 0.6);
            }
            
            .game-container {
                padding: 20px 0;
            }
            
            .problem-display {
                margin: 30px 0;
            }
            
            .problem-row {
                margin-bottom: 20px;
                overflow: hidden;
            }
            
            .problem-part {
                display: inline-block;
                font-size: 4em;
                font-weight: bold;
                color: #333;
                margin: 0 15px;
                vertical-align: middle;
            }
            
            .answers-row {
                margin: 40px 0;
            }
            
            .answer-btn {
                display: inline-block;
                width: 22%;
                max-width: 150px;
                margin: 10px 1%;
                padding: 20px 10px;
                font-size: 2em;
                font-weight: bold;
                background-color: #4facfe;
                background: -webkit-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: -moz-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: -o-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                color: white;
                border: none;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
                border-radius: 10px;
                cursor: pointer;
                -webkit-box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                -moz-box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                -o-transition: all 0.3s;
                transition: all 0.3s;
            }
            
            .answer-btn:hover:not(:disabled) {
                -webkit-transform: translateY(-5px);
                -moz-transform: translateY(-5px);
                -ms-transform: translateY(-5px);
                -o-transform: translateY(-5px);
                transform: translateY(-5px);
                -webkit-box-shadow: 0 6px 15px rgba(0,0,0,0.3);
                -moz-box-shadow: 0 6px 15px rgba(0,0,0,0.3);
                box-shadow: 0 6px 15px rgba(0,0,0,0.3);
            }
            
            .answer-btn:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            
            .status {
                font-size: 1.5em;
                margin: 30px 0;
                color: #666;
                font-weight: bold;
            }
            
            .back-link {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 30px;
                background-color: #f44336;
                color: white;
                text-decoration: none;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                -o-transition: all 0.3s;
                transition: all 0.3s;
            }
            
            .back-link:hover {
                background-color: #da190b;
                -webkit-transform: translateY(-2px);
                -moz-transform: translateY(-2px);
                -ms-transform: translateY(-2px);
                -o-transform: translateY(-2px);
                transform: translateY(-2px);
            }
            
            @media (max-width: 768px) {
                .container {
                    padding: 20px 15px;
                }
                
                h1 {
                    font-size: 1.5em;
                }
                
                .selection-form {
                    font-size: 1em;
                }
                
                .selection-form label {
                    display: block;
                    margin: 10px auto;
                    max-width: 250px;
                }
                
                .selection-form button {
                    font-size: 1.1em;
                    padding: 12px 40px;
                }
                
                .problem-part {
                    font-size: 2.5em;
                    margin: 0 5px;
                }
                
                .answer-btn {
                    width: 45%;
                    max-width: none;
                    margin: 5px 2%;
                    font-size: 1.5em;
                    padding: 15px 5px;
                }
                
                .status {
                    font-size: 1.2em;
                }
            }
            
            @media (max-width: 480px) {
                .problem-part {
                    font-size: 2em;
                }
                
                .answer-btn {
                    width: 90%;
                    display: block;
                    margin: 10px auto;
                }
            }
        </style>
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js" ></script>

    </head>
    <body>
        <?php
        if (empty($_GET['cuuchuong']))
        {
            ?>
            <div class="container">
                <h1>🎯 Bảng Cửu Chương</h1>
                <p style="color: #666; margin-bottom: 30px;">Chọn giới hạn bảng cửu chương:</p>
                <form method="GET" action="" class="selection-form">
                    <label for="ccall" style="background-color: #fff3cd; font-weight: bold;">
                        <input type="checkbox" id="ccall"/> Chọn tất cả
                    </label>
                    <br>
                    <label for="cc2"><input type="checkbox" name="cuuchuong[]" value="2" id="cc2" class="cc-checkbox"/> Cửu chương 2</label>
                    <label for="cc3"><input type="checkbox" name="cuuchuong[]" value="3" id="cc3" class="cc-checkbox"/> Cửu chương 3</label>
                    <label for="cc4"><input type="checkbox" name="cuuchuong[]" value="4" id="cc4" class="cc-checkbox"/> Cửu chương 4</label>
                    <label for="cc5"><input type="checkbox" name="cuuchuong[]" value="5" id="cc5" class="cc-checkbox"/> Cửu chương 5</label>
                    <label for="cc6"><input type="checkbox" name="cuuchuong[]" value="6" id="cc6" class="cc-checkbox"/> Cửu chương 6</label>
                    <label for="cc7"><input type="checkbox" name="cuuchuong[]" value="7" id="cc7" class="cc-checkbox"/> Cửu chương 7</label>
                    <label for="cc8"><input type="checkbox" name="cuuchuong[]" value="8" id="cc8" class="cc-checkbox"/> Cửu chương 8</label>
                    <label for="cc9"><input type="checkbox" name="cuuchuong[]" value="9" id="cc9" class="cc-checkbox"/> Cửu chương 9</label>
                    <br>
                    <button type="submit">Bắt đầu</button>
                </form>
                
                <script type="text/javascript">
                    // Xử lý checkbox "Chọn tất cả"
                    $('#ccall').change(function() {
                        if ($(this).is(':checked')) {
                            $('.cc-checkbox').prop('checked', true);
                        } else {
                            $('.cc-checkbox').prop('checked', false);
                        }
                    });
                    
                    // Cập nhật trạng thái checkbox "Chọn tất cả" khi các checkbox khác thay đổi
                    $('.cc-checkbox').change(function() {
                        var allChecked = $('.cc-checkbox').length === $('.cc-checkbox:checked').length;
                        $('#ccall').prop('checked', allChecked);
                    });
                </script>
            </div>
            <?php
        }
        else
        {
            $cc = $_GET['cuuchuong'];

            $allTest = array();

            for ($i = 1; $i <= 10; $i ++)
            {
                foreach ($cc as $c)
                {
                    $c         = intval($c);
                    $m         = $allTest[] = "alltest.push([$i, $c, " . ($i * $c) . ", 0])";
                }
            }

            shuffle($allTest);
            $allTest = implode(";", $allTest);
            ?>

            <div class="container game-container">
                <h1>🎯 Bảng Cửu Chương</h1>
                
                <div class="problem-display">
                    <div class="problem-row">
                        <span class="problem-part" id="first">2</span>
                        <span class="problem-part">✖️</span>
                        <span class="problem-part" id="second">5</span>
                        <span class="problem-part">=</span>
                    </div>
                </div>
                
                <div class="answers-row">
                    <button type="button" class="answer-btn" id="ret1" value="81">81</button>
                    <button type="button" class="answer-btn" id="ret2" value="81">81</button>
                    <button type="button" class="answer-btn" id="ret3" value="81">81</button>
                    <button type="button" class="answer-btn" id="ret4" value="81">81</button>
                </div>
                
                <div class="status" id="status"></div>
                
                <a href="?" class="back-link">← Chọn lại bảng cửu chương</a>
            </div>

            <script type="text/javascript">
                var alltest = [];
                var currentTest = 0;
                var currentMath = null;
                var passed = -1;
                var failed = 0;

    <?php echo $allTest; ?>

                function getNextTest()
                {
                    for (var i = 0; i < alltest.length; i++)
                    {
                        if (alltest[i][3] == currentTest)
                        {
                            alltest[i][3]++;
                            currentMath = alltest[i];
                            passed++;
                            return currentMath;
                        }
                    }

                    currentTest++;
                    passed = 0;
                    failed = 0;

                    getNextTest();

                    return currentMath;
                }

                function getRndInteger(min, max) {
                    return Math.floor(Math.random() * (max - min)) + min;
                }

                function genRandom()
                {
                    var r1 = getRndInteger(1, 10) * currentMath[1];
                    var r2 = getRndInteger(1, 10) * currentMath[1];
                    var r3 = getRndInteger(1, 10) * currentMath[1];
                    var r4 = getRndInteger(1, 10) * currentMath[1];
                    var rp = getRndInteger(1, 4);

                    while (r1 == currentMath[2])
                    {
                        r1 = getRndInteger(1, 10) * currentMath[1];
                    }

                    while (r1 == r2 || r2 == currentMath[2])
                    {
                        r2 = getRndInteger(1, 10) * currentMath[1];
                    }

                    while (r3 == r1 || r3 == r2 || r3 == currentMath[2])
                    {
                        r3 = getRndInteger(1, 10) * currentMath[1];
                    }

                    while (r4 == r1 || r4 == r2 || r4 == r3 || r4 == currentMath[2])
                    {
                        r4 = getRndInteger(1, 10) * currentMath[1];
                    }

                    $('#ret1').val(r1);
                    $('#ret1').html(r1);
                    $('#ret2').val(r2);
                    $('#ret2').html(r2);
                    $('#ret3').val(r3);
                    $('#ret3').html(r3);
                    $('#ret4').val(r4);
                    $('#ret4').html(r4);

                    $('#ret' + rp).val(currentMath[2]);
                    $('#ret' + rp).html(currentMath[2]);


                    $('#first').html(currentMath[1]);
                    $('#second').html(currentMath[0]);


                }

                function updateStatus()
                {
                    $('#status').html(passed + "/" + alltest.length + "(F:" + failed + ' R:' + currentTest + ')');
                }
                // 3 x 5 =

                $(function () {
                    // init bunch of sounds
                    ion.sound({
                        sounds: [
                            {name: "light_bulb_breaking"},
                            {name: "bell_ring"},
                        ],

                        // main config
                        path: "../lib/ion.sound-3.0.7/sounds/",
                        preload: true,
                        multiplay: true,
                        volume: 1
                    });

                    getNextTest();

                    genRandom();

                    updateStatus()
                });

                $('button').click(function () {
                    var sel = $(this).val();
                    if (sel == currentMath[2])
                    {
                        ion.sound.play("bell_ring");
                        $('.answer-btn').removeAttr('disabled');
                        $('.answer-btn').css('background', '#4facfe');
                        $('.answer-btn').css('background', '-webkit-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)');
                        $('.answer-btn').css('background', 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)');
                        getNextTest();
                        genRandom();
                    } else
                    {
                        ion.sound.play("light_bulb_breaking");
                        $(this).attr('disabled', 'disabled');
                        $(this).css('background', '#f44336');
                        $(this).css('opacity', '0.7');
                        failed++;
                    }

                    updateStatus();
                });
            </script>
            <?php
        }
        ?>
    </body>
</html>