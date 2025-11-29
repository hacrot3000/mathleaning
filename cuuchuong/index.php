<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <style type="text/css">
            body{
                font-size: 200%;
                text-align: center;
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
            Chọn giới hạn bảng cửu chương:<br />
            <form method="GET" action="">
                <input type="checkbox" name="cuuchuong[]" value="2" id="cc2"/> <label for="cc2">Cửu chương 2</label><br />
                <input type="checkbox" name="cuuchuong[]" value="3" id="cc3"/> <label for="cc3">Cửu chương 3</label><br />
                <input type="checkbox" name="cuuchuong[]" value="4" id="cc4"/> <label for="cc4">Cửu chương 4</label><br />
                <input type="checkbox" name="cuuchuong[]" value="5" id="cc5"/> <label for="cc5">Cửu chương 5</label><br />
                <input type="checkbox" name="cuuchuong[]" value="6" id="cc6"/> <label for="cc6">Cửu chương 6</label><br />
                <input type="checkbox" name="cuuchuong[]" value="7" id="cc7"/> <label for="cc7">Cửu chương 7</label><br />
                <input type="checkbox" name="cuuchuong[]" value="8" id="cc8"/> <label for="cc8">Cửu chương 8</label><br />
                <input type="checkbox" name="cuuchuong[]" value="9" id="cc9"/> <label for="cc9">Cửu chương 9</label><br />
                <button type="submit">Bắt đầu</button>
            </form>
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

            <div style="float: none; margin: 0 auto; width: 70%; font-size: 300%; padding-top: 5%">
                <div style="float: left; width: 100%">
                    <div style="float: left; width: 33%">
                        <span id="first">2</span>
                    </div>
                    <div style="float: left; width: 33%">
                        <span id="">X</span>
                    </div>
                    <div style="float: left; width: 33%">
                        <span id="second">5</span>
                    </div>
                </div>
                <div style="float: left; width: 100%">
                    <span id="">=</span>
                </div>
                <div style="float: left; width: 100%">
                    <div style="float: left; width: 25%;">
                        <button type="button" id="ret1" style="font-size: 150%; width: 90%; background-color: lightblue;" value="81">81</button>
                    </div>
                    <div style="float: left; width: 25%">
                        <button type="button" id="ret2" style="font-size: 150%; width: 90%; background-color: lightblue;" value="81">81</button>
                    </div>
                    <div style="float: left; width: 25%">
                        <button type="button" id="ret3" style="font-size: 150%; width: 90%; background-color: lightblue;" value="81">81</button>
                    </div>
                    <div style="float: left; width: 25%">
                        <button type="button" id="ret4" style="font-size: 150%; width: 90%; background-color: lightblue;" value="81">81</button>
                    </div>
                </div>
                <div style="float: left; width: 100%; font-size: 50%">
                    <p id="status"></p>
                    <!--<p><a href="javascript:window.history.back();">Trở lại lựa chọn bảng cửu chương</a></p>-->
                </div>
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
                        $('button').removeAttr('disabled');
                        $('button').css('background-color', 'lightblue');
                        getNextTest();
                        genRandom();
                    } else
                    {
                        ion.sound.play("light_bulb_breaking");
                        $(this).attr('disabled', 'disabled');
                        $(this).css('background-color', 'red');
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