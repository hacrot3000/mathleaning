<?php
/**
 * Database connection and initialization
 */

// Database file path
$db_file = __DIR__ . '/db/hoctoan.db';

// Create SQLite connection
try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Initialize tables if they don't exist
function initDatabase($db) {
    // Users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        avatar TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // History table
    $db->exec("CREATE TABLE IF NOT EXISTS history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        exercise_type TEXT NOT NULL,
        problem TEXT NOT NULL,
        correct_answer TEXT NOT NULL,
        wrong_answers TEXT,
        skipped INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Create index for faster queries
    $db->exec("CREATE INDEX IF NOT EXISTS idx_history_user_exercise 
               ON history(user_id, exercise_type, created_at DESC)");
}

// Initialize database
initDatabase($db);

// Helper functions
function getUserById($db, $user_id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getAllUsers($db) {
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function createUser($db, $name, $avatar) {
    $stmt = $db->prepare("INSERT INTO users (name, avatar) VALUES (?, ?)");
    $stmt->execute([$name, $avatar]);
    return $db->lastInsertId();
}

function getHistory($db, $user_id, $exercise_type, $limit = 100) {
    $stmt = $db->prepare("SELECT * FROM history 
                          WHERE user_id = ? AND exercise_type = ? 
                          ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$user_id, $exercise_type, $limit]);
    return $stmt->fetchAll();
}

function addHistory($db, $user_id, $exercise_type, $problem, $correct_answer, $wrong_answers, $skipped = 0) {
    $stmt = $db->prepare("INSERT INTO history 
                          (user_id, exercise_type, problem, correct_answer, wrong_answers, skipped) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $wrong_answers_json = is_array($wrong_answers) ? json_encode($wrong_answers) : $wrong_answers;
    $stmt->execute([$user_id, $exercise_type, $problem, $correct_answer, $wrong_answers_json, $skipped ? 1 : 0]);
    return $db->lastInsertId();
}

function clearHistory($db, $user_id, $exercise_type) {
    $stmt = $db->prepare("DELETE FROM history WHERE user_id = ? AND exercise_type = ?");
    $stmt->execute([$user_id, $exercise_type]);
    return $stmt->rowCount();
}
?>

