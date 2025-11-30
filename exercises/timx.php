<!-- Exercise content: Tìm X -->
<?php
// Mode will be set by index.php
$QUADRATIC_MODE = ($mode === 'quadratic');
?>
<h1><?php echo $QUADRATIC_MODE ? $lang['practice_find_x2'] : $lang['practice_find_x']; ?></h1>

<div style="font-size: 100%; color: #666; margin-bottom: 20px;">
    <strong><?php echo $lang['difficulty']; ?>:</strong> <span id="difficulty-level"></span>
    <strong><?php echo $lang['question']; ?>:</strong> <span id="question-number"></span>
</div>

<div class="problem" id="problem-display"></div>

<div>
    <input type="text" id="answer-input-1" placeholder="<?php echo $lang['result']; ?> 1" autocomplete="off">
    <input type="text" id="answer-input-2" placeholder="<?php echo $lang['result']; ?> 2 (nếu có)" autocomplete="off" style="display: none;">
    <p style="font-size: 70%; color: #999; margin-top: 5px;">
        <em><?php echo $lang['rounding_note']; ?></em>
        <br>
        <em id="two-solutions-note" style="display: none;"><?php echo $lang['two_solutions_note']; ?></em>
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
    var QUADRATIC_MODE = <?php echo $QUADRATIC_MODE ? 'true' : 'false'; ?>;
    var storageKey = QUADRATIC_MODE ? 'timx2' : 'timx';
    var storage = createLocalStorageManager(storageKey);
    var problemCountManager = createProblemCountManager(storageKey);
    var hasTwoSolutions = false; // Có 2 nghiệm (khi có trị tuyệt đối hoặc phương trình bậc 2)

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
        problemCount = problemCountManager.get();
        
        if (currentProblem === null) {
            generateNewProblem();
        } else {
            displayProblem();
        }
        
        setupStandardEventHandlers({
            inputSelector: '#answer-input-1, #answer-input-2',
            checkAnswerFn: checkAnswer,
            skipProblemFn: skipProblem
        });
    });

    // Generate coefficient (hệ số)
    function generateCoefficient(config) {
        var types = config.coefficient_types.slice();
        var type = types[getRndInteger(0, types.length - 1)];
        
        if (type === 'integer') {
            var val = getRndInteger(config.coefficient_min, config.coefficient_max);
            // Tránh hệ số 0
            while (val === 0) {
                val = getRndInteger(config.coefficient_min, config.coefficient_max);
            }
            return {
                type: 'integer',
                value: val
            };
        } else if (type === 'fraction') {
            var frac = generateRandomFraction(config.fraction_min || -10, config.fraction_max || 10);
            // Tránh phân số bằng 0
            while (frac.num === 0) {
                frac = generateRandomFraction(config.fraction_min || -10, config.fraction_max || 10);
            }
            return {
                type: 'fraction',
                num: frac.num,
                den: frac.den
            };
        } else if (type === 'mixed') {
            var mixed = generateMixedNumber(config.mixed_number || {
                whole_min: 1,
                whole_max: 5,
                numerator_max: 15,
                denominator_min: 2,
                denominator_max: 10
            });
            // Tránh hỗn số bằng 0
            var mixedValue = mixed.mixedWhole + (mixed.mixedNumerator / mixed.mixedDenominator);
            while (Math.abs(mixedValue) < 0.01) {
                mixed = generateMixedNumber(config.mixed_number || {
                    whole_min: 1,
                    whole_max: 5,
                    numerator_max: 15,
                    denominator_min: 2,
                    denominator_max: 10
                });
                mixedValue = mixed.mixedWhole + (mixed.mixedNumerator / mixed.mixedDenominator);
            }
            return {
                type: 'mixed',
                whole: mixed.mixedWhole,
                num: mixed.mixedNumerator,
                den: mixed.mixedDenominator
            };
        }
        
        // Default: integer
        var val = getRndInteger(config.coefficient_min || -10, config.coefficient_max || 10);
        while (val === 0) {
            val = getRndInteger(config.coefficient_min || -10, config.coefficient_max || 10);
        }
        return {
            type: 'integer',
            value: val
        };
    }

    // Calculate coefficient value
    function calculateCoefficient(coeff) {
        if (coeff.type === 'integer') {
            return coeff.value;
        } else if (coeff.type === 'fraction') {
            return coeff.num / coeff.den;
        } else if (coeff.type === 'mixed') {
            var sign = coeff.whole >= 0 ? 1 : -1;
            return coeff.whole + sign * (coeff.num / coeff.den);
        }
        return 0;
    }

    // Format coefficient to LaTeX
    function formatCoefficientLatex(coeff) {
        if (coeff.type === 'integer') {
            if (coeff.value === 1) return '';
            if (coeff.value === -1) return '-';
            return coeff.value.toString();
        } else if (coeff.type === 'fraction') {
            if (coeff.den === 1) {
                if (coeff.num === 1) return '';
                if (coeff.num === -1) return '-';
                return coeff.num.toString();
            }
            var latex = '\\dfrac{' + Math.abs(coeff.num) + '}{' + coeff.den + '}';
            if (coeff.num < 0) {
                latex = '-' + latex;
            }
            return latex;
        } else if (coeff.type === 'mixed') {
            var whole = coeff.whole;
            var num = coeff.num;
            var den = coeff.den;
            var latex = whole + '\\dfrac{' + num + '}{' + den + '}';
            return latex;
        }
        return '';
    }

    // Format coefficient to text
    function formatCoefficientText(coeff) {
        if (coeff.type === 'integer') {
            return coeff.value.toString();
        } else if (coeff.type === 'fraction') {
            if (coeff.den === 1) return coeff.num.toString();
            return coeff.num + '/' + coeff.den;
        } else if (coeff.type === 'mixed') {
            return coeff.whole + ' ' + coeff.num + '/' + coeff.den;
        }
        return '';
    }

    // Generate a term (số hạng) - có thể chứa x hoặc không
    function generateTerm(config, hasX, xPower, xInFraction) {
        var term = {
            coefficient: null,
            hasX: hasX,
            xPower: xPower || 1,
            xInFraction: xInFraction || false,
            hasAbsolute: false,
            hasParentheses: false
        };
        
        // Generate coefficient
        term.coefficient = generateCoefficient(config);
        
        // Determine x appearance
        if (hasX) {
            // Check if x should be in absolute value
            if (config.allow_absolute_value && Math.random() < (config.absolute_probability || 0)) {
                term.hasAbsolute = true;
                hasTwoSolutions = true; // Có trị tuyệt đối thì có thể có 2 nghiệm
            }
            
            // Check if x should be in parentheses
            if (config.allow_parentheses && Math.random() < (config.parentheses_probability || 0)) {
                term.hasParentheses = true;
            }
            
            // Check if x should be in fraction (only if not xInFraction is false)
            if (!xInFraction && config.allow_x_in_fraction && Math.random() < (config.x_in_fraction_probability || 0)) {
                term.xInFraction = true;
            }
        }
        
        return term;
    }

    // Generate constant term (số hạng không chứa x)
    function generateConstantTerm(config) {
        var types = config.number_types.slice();
        var type = types[getRndInteger(0, types.length - 1)];
        
        if (type === 'integer') {
            return {
                type: 'integer',
                value: getRndInteger(config.integer_min, config.integer_max)
            };
        } else if (type === 'decimal') {
            return {
                type: 'decimal',
                value: getRndDecimal(config.integer_min, config.integer_max, config.decimal_places || 2)
            };
        } else if (type === 'fraction') {
            return generateRandomFraction(config.fraction_min || -10, config.fraction_max || 10);
        } else if (type === 'mixed') {
            return generateMixedNumber(config.mixed_number || {
                whole_min: 1,
                whole_max: 5,
                numerator_max: 15,
                denominator_min: 2,
                denominator_max: 10
            });
        }
        
        return {
            type: 'integer',
            value: getRndInteger(config.integer_min, config.integer_max)
        };
    }

    // Format fraction to LaTeX (helper function)
    function formatFractionLatexHelper(frac, addParentheses) {
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
            
            var mixedLatex = whole + '\\dfrac{' + num + '}{' + den + '}';
            
            var sign = whole >= 0 ? 1 : -1;
            var totalValue = whole + sign * (num / den);
            
            if (addParentheses && totalValue < 0) {
                mixedLatex = '\\left(' + mixedLatex + '\\right)';
            }
            
            return mixedLatex;
        }
        
        var fractionLatex;
        var isNegative = frac.num < 0;
        var absNum = Math.abs(frac.num);
        
        if (isNegative) {
            if (Math.random() < 0.3) {
                fractionLatex = '\\dfrac{(' + frac.num + ')}{' + frac.den + '}';
            } else {
                fractionLatex = '-\\dfrac{' + absNum + '}{' + frac.den + '}';
            }
        } else {
            fractionLatex = '\\dfrac{' + absNum + '}{' + frac.den + '}';
        }
        
        if (addParentheses && isNegative) {
            fractionLatex = '\\left(' + fractionLatex + '\\right)';
        }
        
        return fractionLatex;
    }
    
    // Format fraction to text (helper function)
    function formatFractionTextHelper(frac) {
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

    // Format constant term to LaTeX
    function formatConstantLatex(constant) {
        if (constant.type === 'integer' || constant.type === 'decimal') {
            return formatNumber(constant.value);
        } else if (constant.type === 'fraction') {
            return formatFractionLatexHelper(constant, false);
        } else if (constant.type === 'mixed') {
            var whole = constant.mixedWhole;
            var num = constant.mixedNumerator;
            var den = constant.mixedDenominator;
            return whole + '\\dfrac{' + num + '}{' + den + '}';
        }
        return '0';
    }

    // Format constant term to text
    function formatConstantText(constant) {
        if (constant.type === 'integer' || constant.type === 'decimal') {
            return constant.value.toString();
        } else if (constant.type === 'fraction') {
            return formatFractionTextHelper(constant);
        } else if (constant.type === 'mixed') {
            return constant.mixedWhole + ' ' + constant.mixedNumerator + '/' + constant.mixedDenominator;
        }
        return '0';
    }

    // Format term with x to LaTeX
    function formatTermWithXLatex(term) {
        var coeffLatex = formatCoefficientLatex(term.coefficient);
        var xLatex = '';
        
        // Build x expression
        if (term.xInFraction) {
            // x trong phân số: (ax)/b
            var numLatex = '';
            if (term.coefficient.type === 'integer') {
                if (Math.abs(term.coefficient.value) === 1) {
                    numLatex = term.coefficient.value === 1 ? 'x' : '-x';
                } else {
                    numLatex = term.coefficient.value + 'x';
                }
            } else {
                numLatex = formatCoefficientLatex(term.coefficient) + 'x';
            }
            xLatex = '\\dfrac{' + numLatex + '}{' + (term.coefficient.den || term.coefficient.value) + '}';
        } else {
            // x thông thường
            if (term.xPower > 1) {
                // Luỹ thừa (bao gồm cả chẵn và lẻ)
                xLatex = 'x^{' + term.xPower + '}';
            } else {
                xLatex = 'x';
            }
            
            // Combine coefficient and x
            if (coeffLatex === '') {
                xLatex = xLatex;
            } else if (coeffLatex === '-') {
                xLatex = '-' + xLatex;
            } else {
                xLatex = coeffLatex + xLatex;
            }
        }
        
        // Wrap in absolute value if needed
        if (term.hasAbsolute) {
            xLatex = '\\left|' + xLatex + '\\right|';
        }
        
        // Wrap in parentheses if needed
        if (term.hasParentheses) {
            xLatex = '\\left(' + xLatex + '\\right)';
        }
        
        return xLatex;
    }

    // Format term with x to text
    function formatTermWithXText(term) {
        var coeffText = formatCoefficientText(term.coefficient);
        var xText = '';
        
        if (term.xInFraction) {
            var numText = coeffText + 'x';
            if (term.coefficient.type === 'integer' && Math.abs(term.coefficient.value) === 1) {
                numText = term.coefficient.value === 1 ? 'x' : '-x';
            }
            xText = '(' + numText + ')/' + term.coefficient.den;
        } else {
            if (term.xPower > 1) {
                // Luỹ thừa (bao gồm cả chẵn và lẻ)
                xText = 'x^' + term.xPower;
            } else {
                xText = 'x';
            }
            
            if (coeffText === '1') {
                xText = xText;
            } else if (coeffText === '-1') {
                xText = '-' + xText;
            } else {
                xText = coeffText + xText;
            }
        }
        
        if (term.hasAbsolute) {
            xText = '|' + xText + '|';
        }
        
        if (term.hasParentheses) {
            xText = '(' + xText + ')';
        }
        
        return xText;
    }

    // Solve equation: collect all x terms and constants, then solve
    function solveEquation(leftTerms, rightTerms) {
        // Collect coefficients of x and constants
        var xCoeff = 0; // Tổng hệ số của x
        var constant = 0; // Tổng hằng số
        var hasAbsolute = false;
        
        // Process left side
        leftTerms.forEach(function(term) {
            if (term.hasX) {
                var coeffValue = calculateCoefficient(term.coefficient);
                if (term.xInFraction) {
                    // x trong phân số: (ax)/b, hệ số thực tế là a/b
                    coeffValue = coeffValue / term.coefficient.den;
                }
                if (term.hasAbsolute) {
                    hasAbsolute = true;
                    // Với trị tuyệt đối, hệ số luôn dương khi tính
                    xCoeff += Math.abs(coeffValue);
                } else {
                    xCoeff += coeffValue;
                }
            } else {
                constant += calculateConstantValue(term);
            }
        });
        
        // Process right side (subtract)
        rightTerms.forEach(function(term) {
            if (term.hasX) {
                var coeffValue = calculateCoefficient(term.coefficient);
                if (term.xInFraction) {
                    coeffValue = coeffValue / term.coefficient.den;
                }
                if (term.hasAbsolute) {
                    hasAbsolute = true;
                    xCoeff -= Math.abs(coeffValue);
                } else {
                    xCoeff -= coeffValue;
                }
            } else {
                constant -= calculateConstantValue(term);
            }
        });
        
        // Solve: xCoeff * x = -constant
        // x = -constant / xCoeff
        if (Math.abs(xCoeff) < 0.0001) {
            // Không có nghiệm hoặc vô số nghiệm
            return null;
        }
        
        var solution = -constant / xCoeff;
        solution = roundToTwoDecimals(solution);
        
        // Nếu có trị tuyệt đối, có thể có 2 nghiệm
        if (hasAbsolute) {
            // Với |ax| = b, có 2 nghiệm: x = b/a và x = -b/a
            // Nhưng trong phương trình tổng quát, cần giải riêng
            // Đơn giản hóa: trả về 2 nghiệm đối nhau
            return [solution, roundToTwoDecimals(-solution)];
        }
        
        return [solution];
    }

    // Calculate constant value
    function calculateConstantValue(constant) {
        if (constant.type === 'integer' || constant.type === 'decimal') {
            return constant.value;
        } else if (constant.type === 'fraction') {
            return constant.num / constant.den;
        } else if (constant.type === 'mixed') {
            var sign = constant.mixedWhole >= 0 ? 1 : -1;
            return constant.mixedWhole + sign * (constant.mixedNumerator / constant.mixedDenominator);
        }
        return 0;
    }

    function generateNewProblem() {
        var difficulty = getDifficultyConfig(problemCount, CONFIG);
        var config = difficulty.config;
        var difficultyLevel = difficulty.difficultyLevel;
        
        hasTwoSolutions = false;
        
        // Generate left side terms
        var leftTerms = [];
        var rightTerms = [];
        
        if (QUADRATIC_MODE) {
            // Phương trình bậc 2 đơn giản: ax² = b
            // Left side: chỉ có một term ax²
            var xPower = config.force_power ? 2 : 2; // Luôn là x²
            var coefficient = generateCoefficient(config);
            
            // Đảm bảo hệ số dương để có nghiệm
            var coeffValue = calculateCoefficient(coefficient);
            if (coeffValue < 0) {
                if (coefficient.type === 'integer') {
                    coefficient.value = Math.abs(coefficient.value);
                } else if (coefficient.type === 'fraction') {
                    coefficient.num = Math.abs(coefficient.num);
                } else if (coefficient.type === 'mixed') {
                    coefficient.whole = Math.abs(coefficient.whole);
                }
            }
            
            leftTerms.push({
                coefficient: coefficient,
                hasX: true,
                xPower: 2,
                xInFraction: false,
                hasAbsolute: false,
                hasParentheses: false
            });
            
            // Right side: chỉ có hằng số
            var constant = generateConstantTerm(config);
            var constValue = calculateConstantValue(constant);
            
            // Đảm bảo constant không âm (>= 0)
            if (constValue < 0) {
                if (constant.type === 'integer' || constant.type === 'decimal') {
                    constant.value = Math.abs(constant.value);
                } else if (constant.type === 'fraction') {
                    constant.num = Math.abs(constant.num);
                } else if (constant.type === 'mixed') {
                    constant.mixedWhole = Math.abs(constant.mixedWhole);
                }
                constValue = calculateConstantValue(constant);
            }
            
            // Ở các độ khó Medium trở lên, bắt buộc constant > 0 (không cho phép ax² = 0)
            if (difficultyLevel !== 'easy') {
                var attempts = 0;
                while (Math.abs(constValue) < 0.01 && attempts < 10) {
                    // Regenerate constant nếu = 0
                    constant = generateConstantTerm(config);
                    constValue = calculateConstantValue(constant);
                    
                    // Đảm bảo không âm
                    if (constValue < 0) {
                        if (constant.type === 'integer' || constant.type === 'decimal') {
                            constant.value = Math.abs(constant.value);
                        } else if (constant.type === 'fraction') {
                            constant.num = Math.abs(constant.num);
                        } else if (constant.type === 'mixed') {
                            constant.mixedWhole = Math.abs(constant.mixedWhole);
                        }
                        constValue = calculateConstantValue(constant);
                    }
                    
                    // Nếu vẫn = 0, set = 1
                    if (Math.abs(constValue) < 0.01) {
                        if (constant.type === 'integer' || constant.type === 'decimal') {
                            constant.value = 1;
                        } else if (constant.type === 'fraction') {
                            constant.num = 1;
                        } else if (constant.type === 'mixed') {
                            constant.mixedWhole = 1;
                        }
                        constValue = 1;
                    }
                    
                    attempts++;
                }
            }
            
            rightTerms.push(constant);
            
            // Solve: ax² = b => x² = b/a => x = ±√(b/a)
            coeffValue = calculateCoefficient(leftTerms[0].coefficient);
            constValue = calculateConstantValue(rightTerms[0]);
            
            var ratio = constValue / coeffValue;
            if (ratio < 0) {
                // Vô nghiệm - regenerate
                generateNewProblem();
                return;
            }
            
            var sqrtValue = Math.sqrt(ratio);
            sqrtValue = roundToTwoDecimals(sqrtValue);
            
            var solutions;
            // Nếu b = 0, chỉ có 1 nghiệm x = 0
            if (Math.abs(constValue) < 0.0001) {
                solutions = [0];
                hasTwoSolutions = false;
            } else {
                // Nếu b > 0, có 2 nghiệm x = ±√(b/a)
                solutions = [sqrtValue, roundToTwoDecimals(-sqrtValue)];
                hasTwoSolutions = true;
            }
            
            currentProblem = {
                leftTerms: leftTerms,
                rightTerms: rightTerms,
                solutions: solutions,
                xPower: 2,
                difficulty: difficultyLevel
            };
        } else {
            // Phương trình bậc 1 (logic gốc)
            // Determine x power (must be consistent)
            var xPower = 1; // Default
            if (config.allow_power && Math.random() < (config.power_probability || 0)) {
                // Generate odd power only
                var oddPowers = [];
                for (var p = config.power_min || 1; p <= (config.power_max || 5); p++) {
                    if (p % 2 === 1) {
                        oddPowers.push(p);
                    }
                }
                if (oddPowers.length > 0) {
                    xPower = oddPowers[getRndInteger(0, oddPowers.length - 1)];
                }
            }
            
            // Determine if x appears multiple times
            var multipleX = config.allow_multiple_x && Math.random() < (config.multiple_x_probability || 0);
            
            // Left side: at least one x term
            var numXTermsLeft = multipleX ? getRndInteger(1, 2) : 1;
            for (var i = 0; i < numXTermsLeft; i++) {
                leftTerms.push(generateTerm(config, true, xPower, false));
            }
            
            // Add constant terms to left side
            var numConstantsLeft = getRndInteger(0, 1);
            for (var i = 0; i < numConstantsLeft; i++) {
                leftTerms.push(generateConstantTerm(config));
            }
            
            // Right side: may have x terms if multipleX
            if (multipleX) {
                var numXTermsRight = getRndInteger(0, 1);
                for (var i = 0; i < numXTermsRight; i++) {
                    // Đảm bảo x trong phân số không nằm sau phép chia
                    // Nếu có x trong phân số, chỉ đặt ở vế trái hoặc đầu vế phải
                    var xInFraction = config.allow_x_in_fraction && Math.random() < (config.x_in_fraction_probability || 0);
                    // Nếu x trong phân số và đã có term ở right, không thêm nữa
                    if (xInFraction && rightTerms.length > 0) {
                        xInFraction = false;
                    }
                    rightTerms.push(generateTerm(config, true, xPower, xInFraction));
                }
            }
            
            // Right side: at least one constant term
            var numConstantsRight = getRndInteger(1, 2);
            for (var i = 0; i < numConstantsRight; i++) {
                rightTerms.push(generateConstantTerm(config));
            }
            
            // Đảm bảo phân số chứa x không nằm sau phép chia
            // Kiểm tra và điều chỉnh: nếu có x trong phân số ở vế phải và có term trước nó, di chuyển lên đầu
            var xInFractionIndex = -1;
            for (var i = 0; i < rightTerms.length; i++) {
                if (rightTerms[i].hasX && rightTerms[i].xInFraction && i > 0) {
                    xInFractionIndex = i;
                    break;
                }
            }
            if (xInFractionIndex > 0) {
                // Di chuyển term có x trong phân số lên đầu
                var xTerm = rightTerms.splice(xInFractionIndex, 1)[0];
                rightTerms.unshift(xTerm);
            }
            
            // Solve equation
            var solutions = solveEquation(leftTerms, rightTerms);
            
            if (!solutions || solutions.length === 0) {
                // Regenerate if no solution
                generateNewProblem();
                return;
            }
            
            currentProblem = {
                leftTerms: leftTerms,
                rightTerms: rightTerms,
                solutions: solutions,
                xPower: xPower,
                difficulty: difficultyLevel
            };
        }
        
        currentWrongAnswers = [];
        
        displayProblem();
        saveToLocalStorage();
    }

    function displayProblem() {
        if (currentProblem === null) return;
        
        var latex = '';
        
        // Left side
        for (var i = 0; i < currentProblem.leftTerms.length; i++) {
            if (i > 0) latex += ' + ';
            
            if (currentProblem.leftTerms[i].hasX) {
                latex += formatTermWithXLatex(currentProblem.leftTerms[i]);
            } else {
                var constLatex = formatConstantLatex(currentProblem.leftTerms[i]);
                var constValue = calculateConstantValue(currentProblem.leftTerms[i]);
                if (i > 0 && constValue >= 0) {
                    latex += constLatex;
                } else if (i === 0 && constValue < 0) {
                    latex += '-' + formatConstantLatex({
                        type: currentProblem.leftTerms[i].type,
                        value: Math.abs(constValue),
                        num: currentProblem.leftTerms[i].num ? Math.abs(currentProblem.leftTerms[i].num) : undefined,
                        den: currentProblem.leftTerms[i].den
                    });
                } else {
                    latex += constLatex;
                }
            }
        }
        
        latex += ' = ';
        
        // Right side
        for (var i = 0; i < currentProblem.rightTerms.length; i++) {
            if (i > 0) latex += ' + ';
            
            if (currentProblem.rightTerms[i].hasX) {
                latex += formatTermWithXLatex(currentProblem.rightTerms[i]);
            } else {
                var constLatex = formatConstantLatex(currentProblem.rightTerms[i]);
                var constValue = calculateConstantValue(currentProblem.rightTerms[i]);
                if (i > 0 && constValue >= 0) {
                    latex += constLatex;
                } else if (i === 0 && constValue < 0) {
                    latex += '-' + formatConstantLatex({
                        type: currentProblem.rightTerms[i].type,
                        value: Math.abs(constValue),
                        num: currentProblem.rightTerms[i].num ? Math.abs(currentProblem.rightTerms[i].num) : undefined,
                        den: currentProblem.rightTerms[i].den
                    });
                } else {
                    latex += constLatex;
                }
            }
        }
        
        renderMath(latex, 'problem-display');
        
        // Show/hide second input based on number of solutions
        // Check if two solutions are actually different (not just 0 and -0)
        hasTwoSolutions = currentProblem.solutions.length > 1;
        if (hasTwoSolutions && currentProblem.solutions.length === 2) {
            var sol1 = roundToTwoDecimals(currentProblem.solutions[0]);
            var sol2 = roundToTwoDecimals(currentProblem.solutions[1]);
            if (Math.abs(sol1 - sol2) < 0.01) {
                // Hai nghiệm giống nhau (ví dụ: 0 và -0), chỉ tính là 1 nghiệm
                hasTwoSolutions = false;
            }
        }
        
        if (hasTwoSolutions) {
            $('#answer-input-2').show();
            $('#two-solutions-note').show();
        } else {
            $('#answer-input-2').hide();
            $('#two-solutions-note').hide();
        }
        
        clearAnswerInput('#answer-input-1');
        clearAnswerInput('#answer-input-2');
        focusAnswerInput('#answer-input-1');
        hideFeedback();
        
        $('#difficulty-level').html(getDifficultyText(currentProblem.difficulty));
        $('#question-number').html((problemCount + 1));
    }

    function checkAnswer() {
        var answer1Str = $('#answer-input-1').val().trim();
        var answer2Str = $('#answer-input-2').val().trim();
        
        if (answer1Str === '') {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        
        // Normalize: replace comma with dot
        answer1Str = answer1Str.replace(',', '.');
        answer2Str = answer2Str.replace(',', '.');
        
        var answer1 = parseFloat(answer1Str);
        if (isNaN(answer1)) {
            alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
            return;
        }
        answer1 = roundToTwoDecimals(answer1);
        
        var answer2 = null;
        if (hasTwoSolutions && answer2Str !== '') {
            answer2 = parseFloat(answer2Str);
            if (isNaN(answer2)) {
                alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
                return;
            }
            answer2 = roundToTwoDecimals(answer2);
        }
        
        // Check solutions
        var correct = false;
        var solutions = currentProblem.solutions.map(function(s) { return roundToTwoDecimals(s); });
        
        if (hasTwoSolutions && solutions.length === 2) {
            // Phương trình có 2 nghiệm, yêu cầu nhập cả 2
            if (answer2Str === '' || answer2 === null) {
                // Chưa nhập nghiệm thứ 2, hiển thị cảnh báo
                alert(t('two_solutions_note', 'Phương trình này có 2 nghiệm, vui lòng nhập cả hai!'));
                return;
            }
            
            // Check if both answers match (order doesn't matter)
            var matches1 = Math.abs(answer1 - solutions[0]) < 0.01 || Math.abs(answer1 - solutions[1]) < 0.01;
            var matches2 = Math.abs(answer2 - solutions[0]) < 0.01 || Math.abs(answer2 - solutions[1]) < 0.01;
            
            if (matches1 && matches2 && Math.abs(answer1 - answer2) > 0.01) {
                correct = true;
            }
        } else {
            // Single solution
            if (Math.abs(answer1 - solutions[0]) < 0.01) {
                correct = true;
            }
        }
        
        if (correct) {
            showFeedback(true);
            
            problemCount = problemCountManager.increment();
            saveProblemToHistoryLocal(false);
            
            setTimeout(function() {
                generateNewProblem();
            }, 1500);
        } else {
            showFeedback(false);
            
            var wrongAnswer = answer1.toString();
            if (answer2 !== null) {
                wrongAnswer += ', ' + answer2.toString();
            }
            currentWrongAnswers.push(wrongAnswer);
            saveToLocalStorage();
            
            selectAnswerInput('#answer-input-1');
        }
    }

    function skipProblem() {
        standardSkipProblem(saveProblemToHistoryLocal, generateNewProblem);
    }

    function saveProblemToHistoryLocal(skipped) {
        if (!currentProblem) {
            return;
        }
        
        // Format problem text for history
        var problemText = '';
        
        // Left side
        for (var i = 0; i < currentProblem.leftTerms.length; i++) {
            if (i > 0) problemText += ' + ';
            
            if (currentProblem.leftTerms[i].hasX) {
                problemText += formatTermWithXText(currentProblem.leftTerms[i]);
            } else {
                problemText += formatConstantText(currentProblem.leftTerms[i]);
            }
        }
        
        problemText += ' = ';
        
        // Right side
        for (var i = 0; i < currentProblem.rightTerms.length; i++) {
            if (i > 0) problemText += ' + ';
            
            if (currentProblem.rightTerms[i].hasX) {
                problemText += formatTermWithXText(currentProblem.rightTerms[i]);
            } else {
                problemText += formatConstantText(currentProblem.rightTerms[i]);
            }
        }
        
        var correctAnswerText = currentProblem.solutions.map(function(s) {
            return roundToTwoDecimals(s).toString();
        }).join(', ');
        
        saveProblemToHistory({
            problemState: {
                currentProblem: currentProblem,
                currentWrongAnswers: currentWrongAnswers,
                problemHistory: problemHistory,
                historyManager: historyManager
            },
            problemText: problemText,
            correctAnswerText: correctAnswerText,
            skipped: skipped
        });
    }

    function saveToLocalStorage() {
        storage.saveState(currentProblem, currentWrongAnswers);
    }

    function loadFromLocalStorage() {
        var state = storage.loadState();
        currentProblem = state.currentProblem;
        currentWrongAnswers = state.currentWrongAnswers;
        if (currentProblem && currentProblem.solutions) {
            hasTwoSolutions = currentProblem.solutions.length > 1;
        }
    }
</script>

