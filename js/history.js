/**
 * History management for exercises
 * This file handles saving/loading history from server instead of localStorage
 */

// Initialize history management for an exercise
function initHistoryManager(exerciseType, callbacks) {
    var user = getCurrentUser();
    if (!user) {
        alert(t('please_login', 'Vui lòng đăng nhập để sử dụng chức năng này!'));
        window.location.href = '../';
        return null;
    }
    
    return {
        user: user,
        exerciseType: exerciseType,
        callbacks: callbacks || {}
    };
}

// Load history from server
function loadHistoryFromServer(manager, callback) {
    if (!manager || !manager.user) {
        callback(new Error('Not logged in'), null);
        return;
    }
    
    apiGetHistory(manager.user.id, manager.exerciseType, function(err, history) {
        if (err) {
            console.error('Error loading history:', err);
            callback(err, []);
            return;
        }
        
        // Convert history format from server to client format
        var clientHistory = history.map(function(item) {
            var wrongAnswers = [];
            try {
                wrongAnswers = JSON.parse(item.wrong_answers || '[]');
            } catch (e) {
                wrongAnswers = [];
            }
            
            return {
                problem: item.problem,
                correctAnswer: item.correct_answer,
                wrongAnswers: wrongAnswers,
                skipped: item.skipped == 1,
                createdAt: item.created_at || null
            };
        });
        
        callback(null, clientHistory);
    });
}

// Save history item to server
function saveHistoryToServer(manager, problem, correctAnswer, wrongAnswers, skipped, callback) {
    if (!manager || !manager.user) {
        if (callback) callback(new Error('Not logged in'));
        return;
    }
    
    apiAddHistory(
        manager.user.id,
        manager.exerciseType,
        problem,
        correctAnswer,
        wrongAnswers,
        skipped,
        function(err, historyId) {
            if (err) {
                console.error('Error saving history:', err);
            }
            if (callback) callback(err, historyId);
        }
    );
}

// Clear all history for current exercise
function clearHistoryOnServer(manager, callback) {
    if (!manager || !manager.user) {
        if (callback) callback(new Error('Not logged in'));
        return;
    }
    
    apiClearHistory(manager.user.id, manager.exerciseType, function(err, deleted) {
        if (err) {
            console.error('Error clearing history:', err);
        }
        if (callback) callback(err, deleted);
    });
}

// Display user info in exercise page
function displayUserInfo() {
    var user = getCurrentUser();
    if (!user) {
        return '<div style="color: #999;">Not logged in</div>';
    }
    
    return '<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">' +
           '<span style="font-size: 2em;">' + user.avatar + '</span>' +
           '<span style="font-weight: bold; color: #333;">' + user.name + '</span>' +
           '</div>';
}

