<?php
/**
 * API endpoints for user and history management
 */

header('Content-Type: application/json');
require_once 'db.php';

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_users':
            $users = getAllUsers($db);
            echo json_encode(['success' => true, 'users' => $users]);
            break;
            
        case 'get_user':
            $user_id = $_REQUEST['user_id'] ?? 0;
            $user = getUserById($db, $user_id);
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
            break;
            
        case 'create_user':
            $name = $_POST['name'] ?? '';
            $avatar = $_POST['avatar'] ?? '';
            
            if (empty($name) || empty($avatar)) {
                echo json_encode(['success' => false, 'error' => 'Name and avatar are required']);
                break;
            }
            
            $user_id = createUser($db, $name, $avatar);
            $user = getUserById($db, $user_id);
            echo json_encode(['success' => true, 'user' => $user]);
            break;
            
        case 'get_history':
            $user_id = $_REQUEST['user_id'] ?? 0;
            $exercise_type = $_REQUEST['exercise_type'] ?? '';
            
            if (empty($user_id) || empty($exercise_type)) {
                echo json_encode(['success' => false, 'error' => 'User ID and exercise type are required']);
                break;
            }
            
            $history = getHistory($db, $user_id, $exercise_type);
            echo json_encode(['success' => true, 'history' => $history]);
            break;
            
        case 'add_history':
            $user_id = $_POST['user_id'] ?? 0;
            $exercise_type = $_POST['exercise_type'] ?? '';
            $problem = $_POST['problem'] ?? '';
            $correct_answer = $_POST['correct_answer'] ?? '';
            $wrong_answers = $_POST['wrong_answers'] ?? '[]';
            $skipped = $_POST['skipped'] ?? 0;
            
            if (empty($user_id) || empty($exercise_type) || empty($problem)) {
                echo json_encode(['success' => false, 'error' => 'Required fields missing']);
                break;
            }
            
            $history_id = addHistory($db, $user_id, $exercise_type, $problem, $correct_answer, $wrong_answers, $skipped);
            echo json_encode(['success' => true, 'history_id' => $history_id]);
            break;
            
        case 'clear_history':
            $user_id = $_POST['user_id'] ?? 0;
            $exercise_type = $_POST['exercise_type'] ?? '';
            
            if (empty($user_id) || empty($exercise_type)) {
                echo json_encode(['success' => false, 'error' => 'User ID and exercise type are required']);
                break;
            }
            
            $count = clearHistory($db, $user_id, $exercise_type);
            echo json_encode(['success' => true, 'deleted' => $count]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

