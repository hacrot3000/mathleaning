<?php
/**
 * Main exercises router with template wrapper
 * Routes to appropriate exercise file based on 'type' parameter
 */

// Get exercise type from URL
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Map exercise types to their files and metadata
$exercises = [
    'congtrusonguyen' => [
        'file' => 'congtrusonguyen.php',
        'page_title' => 'Cộng Trừ Số Nguyên',
        'config_type' => 'congtru',
        'extra_css' => [],
        'use_katex' => false,
        'use_user' => true,
        'use_history' => true,
        'config_general' => false,
        'exercise_type' => 'congtrusonguyen'
    ],
    'nhanchiasonguyen' => [
        'file' => 'nhanchiasonguyen.php',
        'page_title' => 'Nhân Chia Số Nguyên',
        'config_type' => 'nhanchia',
        'extra_css' => ['nhanchiasonguyen.css'],
        'use_katex' => false,
        'use_user' => true,
        'use_history' => true,
        'config_general' => true,
        'exercise_type' => 'nhanchiasonguyen'
    ],
    'phanso' => [
        'file' => 'phanso.php',
        'page_title' => 'Cộng Trừ Phân Số',
        'config_type' => 'phanso',
        'extra_css' => ['phanso.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => false,
        'exercise_type' => 'phanso',
        'mode' => 'fraction'
    ],
    'phanso-mixed' => [
        'file' => 'phanso.php',
        'page_title' => 'Cộng Trừ Hỗn Số',
        'config_type' => 'phanso',
        'extra_css' => ['phanso.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => false,
        'exercise_type' => 'phanso_mixed',
        'mode' => 'mixed'
    ],
    'nhanchiaphanso' => [
        'file' => 'nhanchiaphanso.php',
        'page_title' => 'Nhân Chia Phân Số',
        'config_type' => 'nhanchiaphanso',
        'extra_css' => ['nhanchiaphanso.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => false,
        'exercise_type' => 'nhanchiaphanso',
        'mode' => 'fraction'
    ],
    'nhanchiaphanso-mixed' => [
        'file' => 'nhanchiaphanso.php',
        'page_title' => 'Nhân Chia Hỗn Số',
        'config_type' => 'nhanchiaphanso',
        'extra_css' => ['nhanchiaphanso.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => false,
        'exercise_type' => 'nhanchiaphanso_mixed',
        'mode' => 'mixed'
    ],
    'cuuchuong' => [
        'file' => 'cuuchuong.php',
        'page_title' => 'Bảng Cửu Chương',
        'config_type' => '',
        'extra_css' => ['cuuchuong.css'],
        'use_katex' => false,
        'use_user' => false,
        'use_history' => false,
        'config_general' => false,
        'exercise_type' => 'cuuchuong'
    ],
    'luythua' => [
        'file' => 'luythua.php',
        'page_title' => 'Luyện Tập Luỹ Thừa',
        'config_type' => 'luythua',
        'extra_css' => ['luythua.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => true,
        'exercise_type' => 'luythua'
    ],
    'trituyetdoi' => [
        'file' => 'trituyetdoi.php',
        'page_title' => 'Luyện Tập Trị Tuyệt Đối',
        'config_type' => 'trituyetdoi',
        'extra_css' => ['trituyetdoi.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => true,
        'exercise_type' => 'trituyetdoi'
    ],
    'timx' => [
        'file' => 'timx.php',
        'page_title' => 'Tìm X - Phương Trình Bậc Nhất',
        'config_type' => 'timx',
        'extra_css' => ['timx.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => true,
        'exercise_type' => 'timx',
        'mode' => 'linear'
    ],
    'timx2' => [
        'file' => 'timx.php',
        'page_title' => 'Tìm X - Phương Trình Bậc Hai Đơn Giản',
        'config_type' => 'timx2',
        'extra_css' => ['timx.css'],
        'use_katex' => true,
        'use_user' => true,
        'use_history' => true,
        'config_general' => true,
        'exercise_type' => 'timx2',
        'mode' => 'quadratic'
    ]
];

// Check if exercise type exists
if (empty($type) || !isset($exercises[$type])) {
    header('Location: ../');
    exit;
}

$exercise = $exercises[$type];
$exercise_file = __DIR__ . '/' . $exercise['file'];

// Check if exercise file exists
if (!file_exists($exercise_file)) {
    header('Location: ../');
    exit;
}

// Set variables for header
$page_title = $exercise['page_title'];
$config_type = $exercise['config_type'];
$extra_css = $exercise['extra_css'];
$use_katex = $exercise['use_katex'];
$use_user = $exercise['use_user'];
$use_history = $exercise['use_history'];
$config_general = $exercise['config_general'];
$exercise_type = $exercise['exercise_type'];
$mode = isset($exercise['mode']) ? $exercise['mode'] : null;

// Include header
include __DIR__ . '/../includes/header.php';

// Start container and common header HTML
// Special handling for cuuchuong - it manages its own container
if ($type !== 'cuuchuong') {
    if ($use_user || $use_history) {
        ?>
            <div class="container">
                <!-- Header with home button and user info -->
                <div class="container-header">
                    <div class="container-header-left">
                        <a href="../" class="home-btn">🏠 <?php echo $lang['home']; ?></a>
                    </div>
                    <div class="container-header-right">
                        <?php if ($use_user): ?>
                        <div id="user-info-display"></div>
                        <?php endif; ?>
                        <?php include __DIR__ . '/../includes/language-switcher.php'; ?>
                    </div>
                </div>
        <?php
    }
}

// Include the exercise-specific content
ob_start();
include $exercise_file;
$exercise_content = ob_get_clean();
echo $exercise_content;

// Close container and add history section if needed (except for cuuchuong)
if ($type !== 'cuuchuong') {
    if ($use_history) {
        include __DIR__ . '/../includes/history-section.php';
    }

    if ($use_user || $use_history) {
        echo '</div> <!-- End container -->';
    }
}

// Include footer
include __DIR__ . '/../includes/footer.php';
