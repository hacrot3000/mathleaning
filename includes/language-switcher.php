<?php
/**
 * Language switcher component
 * 
 * Usage:
 *   include '../includes/language-switcher.php';
 */

if (!isset($lang)) {
    require_once __DIR__ . '/../lang.php';
    $lang = getLang();
    $lang_code = getLangCode();
}
?>
<div class="language-switcher">
    <button class="lang-btn <?php echo $lang_code === 'vi' ? 'lang-btn-active' : ''; ?>" data-lang="vi" title="Tiếng Việt">🇻🇳</button>
    <button class="lang-btn <?php echo $lang_code === 'en' ? 'lang-btn-active' : ''; ?>" data-lang="en" title="English">🇬🇧</button>
</div>

<script type="text/javascript">
    $(function() {
        $('.lang-btn').click(function() {
            var lang = $(this).data('lang');
            
            // Set cookie
            document.cookie = 'hoctoan_lang=' + lang + '; path=/; max-age=' + (365 * 24 * 60 * 60);
            
            // Reload page to apply new language
            window.location.reload();
        });
    });
</script>

<style type="text/css">
    .language-switcher {
        display: inline-flex;
        gap: 5px;
        margin-left: 10px;
    }
    
    .lang-btn {
        padding: 5px 10px;
        background: #f0f0f0;
        border: 2px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s;
    }
    
    .lang-btn:hover {
        background: #e0e0e0;
        border-color: #4CAF50;
    }
    
    .lang-btn-active {
        background: #4CAF50;
        border-color: #4CAF50;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>

