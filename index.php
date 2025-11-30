<?php
require_once 'lang.php';
$lang = getLang();
$lang_code = getLangCode();
?>
<!doctype html>
<html lang="<?php echo $lang_code; ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title><?php echo $lang['app_title']; ?> - <?php echo $lang['home']; ?></title>
        <link rel="stylesheet" href="css/common.css?v=1">
        <link rel="stylesheet" href="css/home.css?v=1">
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="js/user.js"></script>
        <script src="js/common.js"></script>
        <script type="text/javascript">
            var LANG = <?php echo json_encode($lang, JSON_UNESCAPED_UNICODE); ?>;
            var LANG_CODE = '<?php echo $lang_code; ?>';
        </script>
    </head>
    <body class="home-page">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>🎓 <?php echo $lang['app_title']; ?></h1>
                <?php include 'includes/language-switcher.php'; ?>
            </div>
            
            <!-- User Section -->
            <div class="user-section" id="user-section">
                <!-- User Info (when logged in) -->
                <div id="user-info" class="hidden">
                    <div class="user-info">
                        <span class="user-avatar" id="current-user-avatar"></span>
                        <span class="user-name" id="current-user-name"></span>
                    </div>
                    <button class="btn btn-secondary" id="logout-btn" style="margin-top: 15px;"><?php echo $lang['logout']; ?></button>
                </div>
                
                <!-- Login/Register (when not logged in) -->
                <div id="auth-section">
                    <div id="login-view">
                        <h3><?php echo $lang['select_user']; ?></h3>
                        <div class="user-list" id="user-list"></div>
                        <button class="btn btn-primary" id="show-register-btn" style="margin-top: 20px;">+ <?php echo $lang['create_user']; ?></button>
                    </div>
                    
                    <div id="register-view" class="hidden">
                        <h3><?php echo $lang['create_user']; ?></h3>
                        <form class="auth-form" id="register-form">
                            <div class="form-group">
                                <label for="user-name"><?php echo $lang['user_name']; ?>:</label>
                                <input type="text" id="user-name" required maxlength="50" placeholder="<?php echo $lang['user_name']; ?>...">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang['select_avatar']; ?>:</label>
                                <div class="avatar-grid" id="avatar-grid"></div>
                            </div>
                            <button type="submit" class="btn btn-primary"><?php echo $lang['create_account']; ?></button>
                            <button type="button" class="btn btn-secondary" id="back-to-login-btn" style="margin-left: 10px;"><?php echo $lang['back']; ?></button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Features Section -->
            <div id="features-section" class="hidden">
                <p class="subtitle"><?php echo $lang['select_exercise']; ?></p>
                
                <div class="features">                
                    <a href="exercises/cuuchuong" class="feature-card green">
                        <div class="feature-icon">✖️</div>
                        <div class="feature-title"><?php echo $lang['multiplication_table']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['multiplication_table_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/congtrusonguyen" class="feature-card blue">
                        <div class="feature-icon">➕➖</div>
                        <div class="feature-title"><?php echo $lang['add_subtract_integers']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['add_subtract_integers_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/nhanchiasonguyen" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); background: -moz-linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); background: -o-linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%); background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
                        <div class="feature-icon">✖️➗</div>
                        <div class="feature-title"><?php echo $lang['multiply_divide_integers']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['multiply_divide_integers_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/phanso" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #E91E63 0%, #C2185B 100%); background: -moz-linear-gradient(135deg, #E91E63 0%, #C2185B 100%); background: -o-linear-gradient(135deg, #E91E63 0%, #C2185B 100%); background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%);">
                        <div class="feature-icon">➕➖</div>
                        <div class="feature-title"><?php echo $lang['add_subtract_fractions']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['add_subtract_fractions_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/nhanchiaphanso" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #a044ff 0%, #6a3093 100%); background: -moz-linear-gradient(135deg, #a044ff 0%, #6a3093 100%); background: -o-linear-gradient(135deg, #a044ff 0%, #6a3093 100%); background: linear-gradient(135deg, #a044ff 0%, #6a3093 100%);">
                        <div class="feature-icon">✖️➗</div>
                        <div class="feature-title"><?php echo $lang['multiply_divide_fractions']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['multiply_divide_fractions_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/phanso-mixed" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #FF6F00 0%, #FF8F00 100%); background: -moz-linear-gradient(135deg, #FF6F00 0%, #FF8F00 100%); background: -o-linear-gradient(135deg, #FF6F00 0%, #FF8F00 100%); background: linear-gradient(135deg, #FF6F00 0%, #FF8F00 100%);">
                        <div class="feature-icon">➕➖</div>
                        <div class="feature-title"><?php echo $lang['add_subtract_mixed']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['add_subtract_mixed_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/nhanchiaphanso-mixed" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #00796B 0%, #00695C 100%); background: -moz-linear-gradient(135deg, #00796B 0%, #00695C 100%); background: -o-linear-gradient(135deg, #00796B 0%, #00695C 100%); background: linear-gradient(135deg, #00796B 0%, #00695C 100%);">
                        <div class="feature-icon">✖️➗</div>
                        <div class="feature-title"><?php echo $lang['multiply_divide_mixed']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['multiply_divide_mixed_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/luythua" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #667eea 0%, #764ba2 100%); background: -moz-linear-gradient(135deg, #667eea 0%, #764ba2 100%); background: -o-linear-gradient(135deg, #667eea 0%, #764ba2 100%); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="feature-icon">x²</div>
                        <div class="feature-title"><?php echo $lang['power_practice']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['power_practice_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/trituyetdoi" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #f093fb 0%, #f5576c 100%); background: -moz-linear-gradient(135deg, #f093fb 0%, #f5576c 100%); background: -o-linear-gradient(135deg, #f093fb 0%, #f5576c 100%); background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="feature-icon">|x|</div>
                        <div class="feature-title"><?php echo $lang['absolute_value_practice']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['absolute_value_practice_desc']; ?>
                        </div>
                    </a>
                    
                    <a href="exercises/timx" class="feature-card" style="background: #f093fb; background: -webkit-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); background: -moz-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); background: -o-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="feature-icon">x = ?</div>
                        <div class="feature-title"><?php echo $lang['find_x_practice']; ?></div>
                        <div class="feature-description">
                            <?php echo $lang['find_x_practice_desc']; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var selectedAvatar = null;
            
            $(function() {
                checkLoginStatus();
                loadUserList();
                renderAvatarGrid();
                
                // Event handlers
                $('#show-register-btn').click(function() {
                    $('#login-view').addClass('hidden');
                    $('#register-view').removeClass('hidden');
                });
                
                $('#back-to-login-btn').click(function() {
                    $('#register-view').addClass('hidden');
                    $('#login-view').removeClass('hidden');
                });
                
                $('#register-form').submit(function(e) {
                    e.preventDefault();
                    register();
                });
                
                $('#logout-btn').click(function() {
                    logout();
                });
            });
            
            function checkLoginStatus() {
                var user = getCurrentUser();
                if (user) {
                    showLoggedIn(user);
                } else {
                    showLoggedOut();
                }
            }
            
            function showLoggedIn(user) {
                $('#current-user-avatar').text(user.avatar);
                $('#current-user-name').text(user.name);
                $('#user-info').removeClass('hidden');
                $('#auth-section').addClass('hidden');
                $('#features-section').removeClass('hidden');
            }
            
            function showLoggedOut() {
                $('#user-info').addClass('hidden');
                $('#auth-section').removeClass('hidden');
                $('#features-section').addClass('hidden');
            }
            
            function loadUserList() {
                apiGetUsers(function(err, users) {
                    if (err) {
                        console.error('Error loading users:', err);
                        return;
                    }
                    
                    var html = '';
                    if (users.length === 0) {
                        html = '<p style="color: #999;">' + (typeof LANG !== 'undefined' ? LANG.no_users : 'Chưa có người dùng nào. Hãy tạo tài khoản mới!') + '</p>';
                    } else {
                        users.forEach(function(user) {
                            html += '<div class="user-card" data-user-id="' + user.id + '">';
                            html += '<div class="user-card-avatar">' + user.avatar + '</div>';
                            html += '<div class="user-card-name">' + user.name + '</div>';
                            html += '</div>';
                        });
                    }
                    
                    $('#user-list').html(html);
                    
                    // Add click handlers
                    $('.user-card').click(function() {
                        var userId = $(this).data('user-id');
                        loginAsUser(userId);
                    });
                });
            }
            
            function renderAvatarGrid() {
                var html = '';
                AVATARS.forEach(function(avatar) {
                    html += '<div class="avatar-option" data-avatar="' + avatar + '">' + avatar + '</div>';
                });
                $('#avatar-grid').html(html);
                
                $('.avatar-option').click(function() {
                    $('.avatar-option').removeClass('selected');
                    $(this).addClass('selected');
                    selectedAvatar = $(this).data('avatar');
                });
            }
            
            function register() {
                var name = $('#user-name').val().trim();
                if (!name) {
                    alert(typeof LANG !== 'undefined' ? LANG.enter_name : 'Vui lòng nhập tên!');
                    return;
                }
                
                if (!selectedAvatar) {
                    alert(typeof LANG !== 'undefined' ? LANG.select_avatar : 'Vui lòng chọn avatar!');
                    return;
                }
                
                apiCreateUser(name, selectedAvatar, function(err, user) {
                    if (err) {
                        alert('Lỗi: ' + err);
                        return;
                    }
                    
                    showLoggedIn(user);
                });
            }
            
            function loginAsUser(userId) {
                // Simple login - just set cookie
                apiGetUsers(function(err, users) {
                    if (err) return;
                    
                    var user = users.find(function(u) { return u.id == userId; });
                    if (user) {
                        setCurrentUser(user);
                        showLoggedIn(user);
                    }
                });
            }
            
            function logout() {
                clearCurrentUser();
                // Reset selected avatar
                selectedAvatar = null;
                // Clear form if visible
                $('#user-name').val('');
                $('.avatar-option').removeClass('selected');
                // Update UI
                showLoggedOut();
                // Reload user list
                loadUserList();
            }
        </script>
        </div> <!-- End container -->
        
        <?php include 'includes/footer.php'; ?>
