<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Bảng Cửu Chương</title>
        <link rel="stylesheet" href="../css/common.css">
        <link rel="stylesheet" href="../css/cuuchuong.css">
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js" ></script>

    </head>
    <body class="with-padding">
        <a href="../" class="home-btn">🏠 Trang chủ</a>
        
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
                    $('#status').html(passed + "/" + alltest.length + "(Sai:" + failed + ' Vòng:' + currentTest + ')');
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