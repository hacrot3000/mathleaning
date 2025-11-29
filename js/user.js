/**
 * User management utilities
 */

// Available avatars (math symbols)
var AVATARS = ['➕', '➖', '✖️', '➗', '🟰', '∑', '∫', '√', '∞', 'π', '⊕', '⊗', '≠', '≤', '≥', '∈', '∉', '∪', '∩', '⊂'];

// Get current user from cookie
function getCurrentUser() {
    var user = getCookie('hoctoan_user');
    if (user) {
        try {
            return JSON.parse(decodeURIComponent(user));
        } catch (e) {
            return null;
        }
    }
    return null;
}

// Set current user to cookie
function setCurrentUser(user) {
    var userJson = JSON.stringify(user);
    setCookie('hoctoan_user', encodeURIComponent(userJson), 365); // 1 year
}

// Clear current user
function clearCurrentUser() {
    deleteCookie('hoctoan_user');
}

// Cookie helpers
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function deleteCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}

// API calls
function apiCreateUser(name, avatar, callback) {
    $.ajax({
        url: '/hoctoan/api.php',
        method: 'POST',
        data: {
            action: 'create_user',
            name: name,
            avatar: avatar
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                setCurrentUser(response.user);
                callback(null, response.user);
            } else {
                callback(response.error || 'Failed to create user');
            }
        },
        error: function() {
            callback('Network error');
        }
    });
}

function apiGetUsers(callback) {
    $.ajax({
        url: '/hoctoan/api.php',
        method: 'GET',
        data: { action: 'get_users' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                callback(null, response.users);
            } else {
                callback(response.error || 'Failed to get users');
            }
        },
        error: function() {
            callback('Network error');
        }
    });
}

function apiGetHistory(userId, exerciseType, callback) {
    $.ajax({
        url: '/hoctoan/api.php',
        method: 'GET',
        data: {
            action: 'get_history',
            user_id: userId,
            exercise_type: exerciseType
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                callback(null, response.history);
            } else {
                callback(response.error || 'Failed to get history');
            }
        },
        error: function() {
            callback('Network error');
        }
    });
}

function apiAddHistory(userId, exerciseType, problem, correctAnswer, wrongAnswers, skipped, callback) {
    $.ajax({
        url: '/hoctoan/api.php',
        method: 'POST',
        data: {
            action: 'add_history',
            user_id: userId,
            exercise_type: exerciseType,
            problem: problem,
            correct_answer: correctAnswer,
            wrong_answers: JSON.stringify(wrongAnswers),
            skipped: skipped ? 1 : 0
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                callback(null, response.history_id);
            } else {
                callback(response.error || 'Failed to add history');
            }
        },
        error: function() {
            callback('Network error');
        }
    });
}

function apiClearHistory(userId, exerciseType, callback) {
    $.ajax({
        url: '/hoctoan/api.php',
        method: 'POST',
        data: {
            action: 'clear_history',
            user_id: userId,
            exercise_type: exerciseType
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                callback(null, response.deleted);
            } else {
                callback(response.error || 'Failed to clear history');
            }
        },
        error: function() {
            callback('Network error');
        }
    });
}

