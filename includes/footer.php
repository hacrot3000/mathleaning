<?php
/**
 * Common footer component
 * 
 * Usage:
 *   include '../includes/footer.php';
 */

if (!isset($lang)) {
    require_once __DIR__ . '/../lang.php';
    $lang = getLang();
}
?>
        <footer class="site-footer">
            <div class="footer-content">
                <p class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo $lang['app_title']; ?>. 
                    <?php if (isset($lang['all_rights_reserved'])): ?>
                        <?php echo $lang['all_rights_reserved']; ?>
                    <?php else: ?>
                        All rights reserved.
                    <?php endif; ?>
                </p>
                <p class="footer-source">
                    <a href="https://github.com/hacrot3000/mathleaning" target="_blank" rel="noopener noreferrer" class="footer-link">
                        <span class="footer-icon">📦</span>
                        <span class="footer-text"><?php echo isset($lang['view_source_on_github']) ? $lang['view_source_on_github'] : 'View Source on GitHub'; ?></span>
                    </a>
                </p>
            </div>
        </footer>
    </body>
</html>

<style type="text/css">
    .site-footer {
        margin-top: 40px;
        padding: 20px;
        text-align: center;
        background-color: #f5f5f5;
        border-top: 1px solid #ddd;
    }
    
    .footer-content {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .footer-copyright {
        color: #666;
        font-size: 14px;
        margin: 5px 0;
    }
    
    .footer-source {
        margin: 10px 0;
    }
    
    .footer-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2196F3;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .footer-link:hover {
        color: #1976D2;
        text-decoration: underline;
    }
    
    .footer-icon {
        font-size: 18px;
    }
    
    .footer-text {
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .site-footer {
            padding: 15px;
            margin-top: 20px;
        }
        
        .footer-copyright,
        .footer-link {
            font-size: 12px;
        }
    }
</style>

