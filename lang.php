<?php
/**
 * Language loader
 * 
 * Usage:
 *   require_once 'lang.php';
 *   $lang = getLang();
 *   echo $lang['home'];
 */

// Default language
$default_lang = 'vi';

// Get language from cookie or default
function getCurrentLang() {
    $default_lang = 'vi';
    if (isset($_COOKIE['hoctoan_lang'])) {
        $lang = $_COOKIE['hoctoan_lang'];
        if (in_array($lang, ['vi', 'en'])) {
            return $lang;
        }
    }
    return $default_lang;
}

// Set language
function setCurrentLang($lang) {
    if (in_array($lang, ['vi', 'en'])) {
        setcookie('hoctoan_lang', $lang, time() + (365 * 24 * 60 * 60), '/'); // 1 year
        return true;
    }
    return false;
}

// Load language file
function loadLang($lang_code) {
    $lang_file = __DIR__ . '/lang/' . $lang_code . '.php';
    if (file_exists($lang_file)) {
        return require $lang_file;
    }
    // Fallback to Vietnamese if file not found
    return require __DIR__ . '/lang/vi.php';
}

// Get language array
function getLang() {
    $lang_code = getCurrentLang();
    return loadLang($lang_code);
}

// Get language code
function getLangCode() {
    return getCurrentLang();
}

// Translate function (shortcut)
function t($key, $default = '') {
    $lang = getLang();
    return isset($lang[$key]) ? $lang[$key] : ($default ? $default : $key);
}

