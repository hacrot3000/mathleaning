<?php
/**
 * Common header for exercise pages
 * 
 * Usage:
 *   $page_title = 'Cộng Trừ Số Nguyên';
 *   $config_type = 'congtru';
 *   $extra_css = []; // Optional: ['cuuchuong.css']
 *   $use_katex = false; // Optional: true for fraction pages
 *   $use_user = true; // Optional: true for pages with user management
 *   $use_history = true; // Optional: true for pages with history
 *   $config_general = false; // Optional: true if need CONFIG_GENERAL
 *   include '../includes/header.php';
 */

 $jsVersion = 8;

// Default values
$extra_css = isset($extra_css) ? $extra_css : [];
$use_katex = isset($use_katex) ? $use_katex : false;
$use_user = isset($use_user) ? $use_user : false;
$use_history = isset($use_history) ? $use_history : false;
$config_general = isset($config_general) ? $config_general : false;

// Require config and language
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lang.php';
$lang = getLang();
$lang_code = getLangCode();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title><?php echo htmlspecialchars($page_title); ?></title>
        
        <!-- Language data for JavaScript -->
        <script type="text/javascript">
            var LANG = <?php echo json_encode($lang, JSON_UNESCAPED_UNICODE); ?>;
            var LANG_CODE = '<?php echo $lang_code; ?>';
        </script>
        
        <!-- Common CSS -->
        <link rel="stylesheet" href="../css/common.css?v=<?php echo $jsVersion; ?>">
        
        <!-- Extra CSS files -->
        <?php foreach ($extra_css as $css_file): ?>
            <?php if (strpos($css_file, 'http') === 0): ?>
                <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>?v=<?php echo $jsVersion; ?>">
            <?php elseif (strpos($css_file, '/') === 0): ?>
                <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>?v=<?php echo $jsVersion; ?>">
            <?php else: ?>
                <link rel="stylesheet" href="../css/<?php echo htmlspecialchars($css_file); ?>?v=<?php echo $jsVersion; ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- KaTeX CSS (for fraction pages) -->
        <?php if ($use_katex): ?>
            <link rel="stylesheet" href="../lib/katex-0.16.9/katex/katex.min.css">
        <?php endif; ?>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        
        <!-- KaTeX JS (for fraction pages) -->
        <?php if ($use_katex): ?>
            <script src="../lib/katex-0.16.9/katex/katex.min.js"></script>
            <script src="../lib/katex-0.16.9/katex/contrib/auto-render.min.js"></script>
        <?php endif; ?>
        
        <!-- Ion Sound -->
        <script src="../lib/ion.sound-3.0.7/ion.sound.min.js"></script>
        
        <!-- Common JavaScript -->
        <script src="../js/common.js?v=<?php echo $jsVersion; ?>"></script>
        
        <!-- Exercise Common Functions -->
        <script src="../js/exercise-common.js?v=<?php echo $jsVersion; ?>"></script>
        
        <!-- User management (if needed) -->
        <?php if ($use_user): ?>
            <script src="../js/user.js?v=<?php echo $jsVersion; ?>"></script>
        <?php endif; ?>
        
        <!-- History management (if needed) -->
        <?php if ($use_history): ?>
            <script src="../js/history.js?v=<?php echo $jsVersion; ?>"></script>
        <?php endif; ?>
        
        <!-- Config from PHP -->
        <?php if (!empty($config_type)): ?>
        <script type="text/javascript">
            // Load config from PHP
            var CONFIG = <?php echo getConfigAsJSON($config_type); ?>;
            <?php if ($config_general): ?>
            var CONFIG_GENERAL = <?php echo getConfigAsJSON('general'); ?>;
            <?php endif; ?>
        </script>
        <?php endif; ?>
    </head>
    <body class="with-padding">

