/**
 * Common Exercise Functions
 * Shared utilities for all exercise types
 */

// ============================================================================
// MATH UTILITIES
// ============================================================================

function getRndInteger(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getRndDecimal(min, max, places) {
    var num = Math.random() * (max - min) + min;
    return Math.round(num * Math.pow(10, places)) / Math.pow(10, places);
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
    
    if (den < 0) {
        num = -num;
        den = -den;
    }
    
    return {num: num, den: den, normalized: true};
}

function roundToTwoDecimals(num) {
    return Math.round(num * 100) / 100;
}

// ============================================================================
// EXERCISE INITIALIZATION
// ============================================================================

/**
 * Setup standard event handlers for exercise
 * @param {object} params - Parameters object
 * @param {string} params.submitSelector - Selector for submit button (default: '#submit-btn')
 * @param {string} params.skipSelector - Selector for skip button (default: '#skip-btn')
 * @param {string} params.inputSelector - Selector for answer input(s)
 * @param {function} params.checkAnswerFn - Function to check answer
 * @param {function} params.skipProblemFn - Function to skip problem
 */
function setupStandardEventHandlers(params) {
    var submitSelector = params.submitSelector || '#submit-btn';
    var skipSelector = params.skipSelector || '#skip-btn';
    
    $(submitSelector).click(function() {
        params.checkAnswerFn();
    });

    $(skipSelector).click(function() {
        params.skipProblemFn();
    });

    setupEnterKeyHandler(params.inputSelector, params.checkAnswerFn);
}

// ============================================================================
// TIMESTAMP GENERATION
// ============================================================================

/**
 * Generate timestamp in format: YYYY-MM-DD HH:MM:SS
 * @returns {string} Formatted timestamp
 */
function generateTimestamp() {
    var now = new Date();
    return now.getFullYear() + '-' + 
           String(now.getMonth() + 1).padStart(2, '0') + '-' + 
           String(now.getDate()).padStart(2, '0') + ' ' + 
           String(now.getHours()).padStart(2, '0') + ':' + 
           String(now.getMinutes()).padStart(2, '0') + ':' + 
           String(now.getSeconds()).padStart(2, '0');
}

// ============================================================================
// HISTORY MANAGEMENT
// ============================================================================

/**
 * Save problem to history (both client and server)
 * @param {object} params - Parameters object
 * @param {object} params.problemState - Object with historyManager, problemHistory, currentProblem, currentWrongAnswers
 * @param {string} params.problemText - Problem as text
 * @param {string} params.correctAnswerText - Correct answer as text
 * @param {boolean} params.skipped - Whether problem was skipped
 */
function saveProblemToHistory(params) {
    if (!params.problemState.currentProblem) {
        return;
    }
    
    var historyItem = {
        problem: params.problemText,
        correctAnswer: params.correctAnswerText,
        wrongAnswers: params.problemState.currentWrongAnswers.slice(),
        skipped: params.skipped || false,
        createdAt: generateTimestamp()
    };
    
    params.problemState.problemHistory.unshift(historyItem);
    
    saveHistoryToServer(
        params.problemState.historyManager,
        params.problemText,
        params.correctAnswerText,
        params.problemState.currentWrongAnswers,
        params.skipped,
        function(err) {
            if (err) console.error('Failed to save history to server');
        }
    );
    
    displayHistory();
}

/**
 * STANDARD PATTERN for skipProblem():
 * 
 * Each exercise should implement this pattern:
 * 
 * function skipProblem() {
 *     saveProblemToHistoryLocal(true);
 *     generateNewProblem();
 * }
 * 
 * Note: We don't increment problemCount when skipping to avoid increasing 
 * difficulty for problems the user hasn't attempted.
 */

/**
 * Standard skip problem implementation
 * @param {function} saveProblemToHistoryFn - Function to save problem to history
 * @param {function} generateNewProblemFn - Function to generate new problem
 */
function standardSkipProblem(saveProblemToHistoryFn, generateNewProblemFn) {
    saveProblemToHistoryFn(true);
    generateNewProblemFn();
}

/**
 * Skip current problem
 * @param {object} problemState - Object with problemCount reference
 * @param {function} saveProblemToHistoryFn - Function to save problem to history
 * @param {function} generateNewProblemFn - Function to generate new problem
 */
function skipProblem(problemState, saveProblemToHistoryFn, generateNewProblemFn) {
    // problemState.problemCount++;
    saveProblemToHistoryFn(true);
    generateNewProblemFn();
}

// ============================================================================
// ANSWER CHECKING
// ============================================================================

/**
 * Check numeric answer (for integer/decimal problems)
 * @param {object} params - Parameters object
 * @param {object} params.problemState - Object with currentProblem, currentWrongAnswers, problemCount
 * @param {string} params.inputSelector - jQuery selector for input field
 * @param {function} params.saveToLocalStorageFn - Function to save to localStorage
 * @param {function} params.saveProblemToHistoryFn - Function to save to history
 * @param {function} params.generateNewProblemFn - Function to generate new problem
 * @param {number} params.tolerance - Tolerance for answer comparison (default: 0.01)
 */
function checkNumericAnswer(params) {
    var userAnswerStr = $(params.inputSelector).val().trim();
    
    if (userAnswerStr === '') {
        alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
        return;
    }
    
    // Normalize: replace comma with dot
    userAnswerStr = userAnswerStr.replace(',', '.');
    
    var userAnswer = parseFloat(userAnswerStr);
    
    if (isNaN(userAnswer)) {
        alert(t('enter_valid_number', 'Vui lòng nhập một số hợp lệ!'));
        return;
    }
    
    userAnswer = roundToTwoDecimals(userAnswer);
    var correctAnswer = roundToTwoDecimals(params.problemState.currentProblem.correctAnswer);
    var tolerance = params.tolerance || 0.01;
    
    if (Math.abs(userAnswer - correctAnswer) < tolerance) {
        showFeedback(true);
        
        params.problemState.problemCount++;
        params.saveProblemToHistoryFn(false);
        
        setTimeout(function() {
            params.generateNewProblemFn();
        }, 1500);
    } else {
        showFeedback(false);
        
        params.problemState.currentWrongAnswers.push(userAnswer);
        params.saveToLocalStorageFn();
        
        selectAnswerInput();
    }
}

/**
 * Check fraction answer
 * @param {object} params - Parameters object
 * @param {object} params.problemState - Object with currentProblem, currentWrongAnswers, problemCount
 * @param {string} params.numSelector - jQuery selector for numerator input
 * @param {string} params.denSelector - jQuery selector for denominator input
 * @param {function} params.saveToLocalStorageFn - Function to save to localStorage
 * @param {function} params.saveProblemToHistoryFn - Function to save to history
 * @param {function} params.generateNewProblemFn - Function to generate new problem
 */
function checkFractionAnswer(params) {
    var userNum = parseInt($(params.numSelector).val().trim());
    var userDen = parseInt($(params.denSelector).val().trim());
    
    if (isNaN(userNum) || isNaN(userDen)) {
        alert(t('enter_valid_fraction', 'Vui lòng nhập một phân số hợp lệ!'));
        return;
    }
    
    if (userDen === 0) {
        alert(t('denominator_cannot_zero', 'Mẫu số không thể bằng 0!'));
        return;
    }
    
    var userFraction = simplifyFraction(userNum, userDen);
    var correctFraction = params.problemState.currentProblem.correctAnswer;
    
    if (userFraction.num === correctFraction.num && userFraction.den === correctFraction.den) {
        showFeedback(true);
        
        params.problemState.problemCount++;
        params.saveProblemToHistoryFn(false);
        
        setTimeout(function() {
            params.generateNewProblemFn();
        }, 1500);
    } else {
        showFeedback(false);
        
        var wrongAnswer = userNum + '/' + userDen;
        params.problemState.currentWrongAnswers.push(wrongAnswer);
        params.saveToLocalStorageFn();
        
        selectAnswerInput();
    }
}

// ============================================================================
// DIFFICULTY LEVEL HELPERS
// ============================================================================

/**
 * Get difficulty configuration based on problem count
 * @param {number} problemCount - Current problem count
 * @param {object} config - Configuration object with easy, medium, hard, expert levels
 * @returns {object} Object with config and difficultyLevel string
 */
function getDifficultyConfig(problemCount, config) {
    var difficultyLevel = '';
    var currentConfig = null;
    
    if (config.easy && problemCount < config.easy.threshold) {
        currentConfig = config.easy;
        difficultyLevel = 'easy';
    } else if (config.medium && problemCount < config.medium.threshold) {
        currentConfig = config.medium;
        difficultyLevel = 'medium';
    } else if (config.hard && problemCount < config.hard.threshold) {
        currentConfig = config.hard;
        difficultyLevel = 'hard';
    } else if (config.expert) {
        currentConfig = config.expert;
        difficultyLevel = 'expert';
    } else {
        currentConfig = config.hard || config.medium || config.easy;
        difficultyLevel = 'hard';
    }
    
    return {
        config: currentConfig,
        difficultyLevel: difficultyLevel
    };
}

/**
 * Get difficulty level text for display
 * @param {string} difficultyLevel - Difficulty level ('easy', 'medium', 'hard', 'expert')
 * @returns {string} Translated difficulty text
 */
function getDifficultyText(difficultyLevel) {
    var texts = {
        'easy': t('difficulty_easy', 'Dễ'),
        'medium': t('difficulty_medium', 'Trung bình'),
        'hard': t('difficulty_hard', 'Khó'),
        'expert': t('difficulty_expert', 'Rất khó')
    };
    
    return texts[difficultyLevel] || texts.easy;
}

// ============================================================================
// FRACTION UTILITIES
// ============================================================================

/**
 * Create fraction variant (with negative in numerator or denominator)
 * @param {number} num - Numerator
 * @param {number} den - Denominator
 * @returns {object} Fraction object
 */
function createFractionVariant(num, den) {
    if (num === 0 || den === 1) {
        return {num: num, den: den, normalized: true};
    }
    
    if (Math.random() < 0.3 && num < 0) {
        return {num: -num, den: -den, normalized: false};
    }
    
    return {num: num, den: den, normalized: true};
}

/**
 * Add two fractions
 * @param {object} f1 - First fraction {num, den}
 * @param {object} f2 - Second fraction {num, den}
 * @returns {object} Result fraction
 */
function addFractions(f1, f2) {
    var num = f1.num * f2.den + f2.num * f1.den;
    var den = f1.den * f2.den;
    var result = simplifyFraction(num, den);
    result.normalized = true;
    return result;
}

/**
 * Subtract two fractions
 * @param {object} f1 - First fraction {num, den}
 * @param {object} f2 - Second fraction {num, den}
 * @returns {object} Result fraction
 */
function subtractFractions(f1, f2) {
    var num = f1.num * f2.den - f2.num * f1.den;
    var den = f1.den * f2.den;
    var result = simplifyFraction(num, den);
    result.normalized = true;
    return result;
}

/**
 * Multiply two fractions
 * @param {object} f1 - First fraction {num, den}
 * @param {object} f2 - Second fraction {num, den}
 * @returns {object} Result fraction
 */
function multiplyFractions(f1, f2) {
    var num = f1.num * f2.num;
    var den = f1.den * f2.den;
    var result = simplifyFraction(num, den);
    result.normalized = true;
    return result;
}

/**
 * Divide two fractions
 * @param {object} f1 - First fraction {num, den}
 * @param {object} f2 - Second fraction {num, den}
 * @returns {object} Result fraction
 */
function divideFractions(f1, f2) {
    var num = f1.num * f2.den;
    var den = f1.den * f2.num;
    var result = simplifyFraction(num, den);
    result.normalized = true;
    return result;
}

/**
 * Generate random fraction
 * @param {number} minVal - Minimum value for numerator/denominator
 * @param {number} maxVal - Maximum value for numerator/denominator
 * @returns {object} Fraction object
 */
function generateRandomFraction(minVal, maxVal) {
    var num, den;
    do {
        num = getRndInteger(minVal, maxVal);
        den = getRndInteger(Math.max(2, minVal), maxVal);
        
        if (den === 0) continue;
        if (num < 0 && den < 0) continue;
        if (num === 0 && Math.random() < 0.7) continue;
        
        break;
    } while (true);
    
    var simplified = simplifyFraction(num, den);
    return createFractionVariant(simplified.num, simplified.den);
}

/**
 * Generate mixed number
 * @param {object} mixedConfig - Configuration for mixed numbers
 * @returns {object} Mixed number as improper fraction
 */
function generateMixedNumber(mixedConfig) {
    var whole = getRndInteger(mixedConfig.whole_min, mixedConfig.whole_max);
    var numerator = getRndInteger(1, mixedConfig.numerator_max);
    var denominator = getRndInteger(mixedConfig.denominator_min, mixedConfig.denominator_max);
    
    if (numerator >= denominator) {
        numerator = getRndInteger(1, denominator - 1);
    }
    
    var g = gcd(numerator, denominator);
    numerator = numerator / g;
    denominator = denominator / g;
    
    if (Math.random() < 0.3) {
        whole = -whole;
    }
    
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

// ============================================================================
// RENDERING HELPERS
// ============================================================================

/**
 * Render LaTeX using KaTeX
 * @param {string} latex - LaTeX string to render
 * @param {string} elementId - ID of element to render into
 */
function renderMath(latex, elementId) {
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

/**
 * Format number with parentheses if negative
 * @param {number} num - Number to format
 * @returns {string} Formatted number
 */
function formatNumber(num) {
    if (num < 0) {
        return '(' + num + ')';
    }
    return num.toString();
}

// ============================================================================
// LOCAL STORAGE HELPERS (specific to exercise type)
// ============================================================================

/**
 * Get today's date string (YYYY-MM-DD)
 * @returns {string} Today's date string
 */
function getTodayDateString() {
    var today = new Date();
    return today.getFullYear() + '-' + 
           String(today.getMonth() + 1).padStart(2, '0') + '-' + 
           String(today.getDate()).padStart(2, '0');
}

/**
 * Create problemCount manager that resets daily
 * @param {string} keyPrefix - Prefix for localStorage keys
 * @returns {object} Object with get, increment, and reset functions
 */
function createProblemCountManager(keyPrefix) {
    var storageKey = keyPrefix + '_problemCount';
    var dateKey = keyPrefix + '_problemCountDate';
    
    return {
        /**
         * Get current problemCount (resets if date changed)
         * @returns {number} Current problemCount
         */
        get: function() {
            var savedDate = loadFromStorage(dateKey);
            var today = getTodayDateString();
            
            // If date changed, reset problemCount
            if (savedDate !== today) {
                saveToStorage(storageKey, 0);
                saveToStorage(dateKey, today);
                return 0;
            }
            
            // Return saved problemCount or 0
            return parseInt(loadFromStorage(storageKey) || '0', 10);
        },
        
        /**
         * Increment problemCount and save
         * @returns {number} New problemCount
         */
        increment: function() {
            var current = this.get();
            var newCount = current + 1;
            saveToStorage(storageKey, newCount);
            saveToStorage(dateKey, getTodayDateString());
            return newCount;
        },
        
        /**
         * Reset problemCount to 0
         */
        reset: function() {
            saveToStorage(storageKey, 0);
            saveToStorage(dateKey, getTodayDateString());
        }
    };
}

/**
 * Create localStorage management functions
 * @param {string} keyPrefix - Prefix for localStorage keys
 * @returns {object} Object with save and load functions
 */
function createLocalStorageManager(keyPrefix) {
    return {
        save: function(problemState) {
            saveToStorage(keyPrefix + '_currentProblem', problemState.currentProblem);
            saveToStorage(keyPrefix + '_currentWrongAnswers', problemState.currentWrongAnswers);
        },
        load: function(problemState) {
            problemState.currentProblem = loadFromStorage(keyPrefix + '_currentProblem');
            problemState.currentWrongAnswers = loadFromStorage(keyPrefix + '_currentWrongAnswers') || [];
        },
        // Wrapper functions for backward compatibility
        saveState: function(currentProblem, currentWrongAnswers) {
            saveToStorage(keyPrefix + '_currentProblem', currentProblem);
            saveToStorage(keyPrefix + '_currentWrongAnswers', currentWrongAnswers);
        },
        loadState: function() {
            return {
                currentProblem: loadFromStorage(keyPrefix + '_currentProblem'),
                currentWrongAnswers: loadFromStorage(keyPrefix + '_currentWrongAnswers') || []
            };
        }
    };
}

