/**
 * Common JavaScript utilities for math quiz pages
 */

// Generate random integer between min and max (inclusive)
function getRndInteger(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Round number to two decimal places
function roundToTwoDecimals(num) {
    return Math.round(num * 100) / 100;
}

// Initialize ion.sound library
function initializeSounds(soundPath) {
    ion.sound({
        sounds: [
            {name: "light_bulb_breaking"},
            {name: "bell_ring"},
        ],
        path: soundPath || "../lib/ion.sound-3.0.7/sounds/",
        preload: true,
        multiplay: true,
        volume: 1
    });
}

// Play sound effect
function playSound(soundName) {
    ion.sound.play(soundName);
}

// Show feedback message
function showFeedback(isCorrect, message) {
    var $feedback = $('#feedback');
    if (isCorrect) {
        $feedback.removeClass('incorrect').addClass('correct');
        playSound('bell_ring');
    } else {
        $feedback.removeClass('correct').addClass('incorrect');
        playSound('light_bulb_breaking');
    }
    $feedback.html(message || (isCorrect ? '✓ Chính xác!' : '✗ Sai rồi! Thử lại.'));
    $feedback.show();
}

// Hide feedback message
function hideFeedback() {
    $('#feedback').hide();
}

// Focus on answer input
function focusAnswerInput(selector) {
    $(selector || '#answer-input').focus();
}

// Select answer input text
function selectAnswerInput(selector) {
    $(selector || '#answer-input').select();
}

// Clear answer input
function clearAnswerInput(selector) {
    $(selector || '#answer-input').val('');
}

// Generic function to save data to localStorage
function saveToStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
    } catch (e) {
        console.error('Error saving to localStorage:', e);
    }
}

// Generic function to load data from localStorage
function loadFromStorage(key) {
    try {
        var data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Error loading from localStorage:', e);
        return null;
    }
}

// Generic function to remove data from localStorage
function removeFromStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.error('Error removing from localStorage:', e);
    }
}

// Confirm dialog for clearing history
function confirmClearHistory() {
    return confirm('Bạn có chắc muốn xóa toàn bộ lịch sử?');
}

// Format number with parentheses if negative
function formatNumber(num) {
    if (num < 0) {
        return '(' + num + ')';
    }
    return num;
}

// Setup enter key handler for input
function setupEnterKeyHandler(inputSelector, callback) {
    $(inputSelector).keypress(function(e) {
        if (e.which === 13) { // Enter key
            callback();
        }
    });
}

// Translate function - get text from LANG object
function t(key, defaultValue) {
    if (typeof LANG !== 'undefined' && LANG[key]) {
        return LANG[key];
    }
    return defaultValue || key;
}

