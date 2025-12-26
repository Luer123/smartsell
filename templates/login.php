<?php
/**
 * 登录模板
 */

if (!defined('ABSPATH')) {
    exit;
}

// API URL 使用常量配置
$api_url = SMARTSELL_DEFAULT_API_URL;
$is_logged_in = !empty(get_option('smartsell_api_token', ''));
?>
<div class="smartsell-wrap smartsell-login-wrap">
    <div class="smartsell-login-container">
        <!-- 左侧品牌区域 -->
        <div class="smartsell-login-brand">
            <div class="smartsell-brand-content">
                <div class="smartsell-brand-logo">🤖</div>
                <h1 class="smartsell-brand-title">SmartSell智能客服</h1>
                <p class="smartsell-brand-subtitle">具有主动销售能力的AI客服</p>
            </div>
        </div>
        
        <!-- 右侧登录表单 -->
        <div class="smartsell-login-form-section">
            <div class="smartsell-login-form-container">
                <?php if ($is_logged_in): ?>
                    <!-- 已登录状态 -->
                    <div class="smartsell-logged-in">
                        <h2 class="smartsell-form-title"><?php esc_html_e('已登录', 'smartsell-assistant'); ?></h2>
                        <div class="smartsell-user-info" id="smartsell-user-info">
                            <div class="smartsell-loading">
                                <div class="smartsell-spinner"></div>
                            </div>
                        </div>
                        <div class="smartsell-login-actions">
                            <button type="button" id="smartsell-logout-btn" class="smartsell-btn smartsell-btn-danger smartsell-btn-block">
                                <?php esc_html_e('退出登录', 'smartsell-assistant'); ?>
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- 登录表单 -->
                    <h2 class="smartsell-form-title"><?php esc_html_e('账户登录', 'smartsell-assistant'); ?></h2>
                    
                    <form id="smartsell-login-form" class="smartsell-form">
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('用户名', 'smartsell-assistant'); ?></label>
                            <input type="text" id="smartsell-username" name="username" class="smartsell-form-input" placeholder="<?php esc_attr_e('请输入账号', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('密码', 'smartsell-assistant'); ?></label>
                            <input type="password" id="smartsell-password" name="password" class="smartsell-form-input" placeholder="<?php esc_attr_e('请输入密码', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('验证码', 'smartsell-assistant'); ?></label>
                            <div class="smartsell-captcha-container">
                                <input type="text" id="smartsell-captcha" name="captcha" class="smartsell-form-input smartsell-captcha-input" placeholder="<?php esc_attr_e('请输入验证码', 'smartsell-assistant'); ?>" required>
                                <div class="smartsell-captcha-image" id="smartsell-captcha-box">
                                    <div class="smartsell-spinner"></div>
                                </div>
                            </div>
                            <input type="hidden" id="smartsell-captcha-key" name="captcha_key" value="">
                        </div>
                        
                        <div class="smartsell-form-group">
                            <button type="submit" id="smartsell-login-btn" class="smartsell-btn smartsell-btn-primary smartsell-btn-block">
                                <?php esc_html_e('登录', 'smartsell-assistant'); ?>
                            </button>
                        </div>
                        
                        <div class="smartsell-form-message" id="smartsell-login-message"></div>
                        
                        <div class="smartsell-form-footer">
                            <p class="smartsell-form-link">还没有账号？<a href="#" id="smartsell-show-register"><?php esc_html_e('立即注册', 'smartsell-assistant'); ?></a></p>
                        </div>
                    </form>
                    
                    <!-- 注册表单 -->
                    <form id="smartsell-register-form" class="smartsell-form" style="display: none;">
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('用户名', 'smartsell-assistant'); ?></label>
                            <input type="text" id="smartsell-reg-username" name="username" class="smartsell-form-input" placeholder="<?php esc_attr_e('请输入用户名', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('邮箱', 'smartsell-assistant'); ?></label>
                            <input type="email" id="smartsell-reg-email" name="email" class="smartsell-form-input" placeholder="<?php esc_attr_e('请输入邮箱', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('密码', 'smartsell-assistant'); ?></label>
                            <input type="password" id="smartsell-reg-password" name="password" class="smartsell-form-input" placeholder="<?php esc_attr_e('请输入密码', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <label class="smartsell-form-label"><?php esc_html_e('确认密码', 'smartsell-assistant'); ?></label>
                            <input type="password" id="smartsell-reg-password-confirm" name="password_confirm" class="smartsell-form-input" placeholder="<?php esc_attr_e('请再次输入密码', 'smartsell-assistant'); ?>" required>
                        </div>
                        
                        <div class="smartsell-form-group">
                            <button type="submit" id="smartsell-register-btn" class="smartsell-btn smartsell-btn-primary smartsell-btn-block">
                                <?php esc_html_e('注册', 'smartsell-assistant'); ?>
                            </button>
                        </div>
                        
                        <div class="smartsell-form-message" id="smartsell-register-message"></div>
                        
                        <div class="smartsell-form-footer">
                            <p class="smartsell-form-link">已有账号？<a href="#" id="smartsell-show-login"><?php esc_html_e('返回登录', 'smartsell-assistant'); ?></a></p>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.smartsell-login-wrap {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: calc(100vh - 32px);
    margin-left: -20px;
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.smartsell-login-container {
    display: flex;
    width: 100%;
    max-width: 900px;
    min-height: 500px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    background: #fff;
}

.smartsell-login-brand {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px;
    background: linear-gradient(135deg, #4a90d9 0%, #357abd 100%);
    color: #fff;
}

.smartsell-brand-content {
    text-align: center;
}

.smartsell-brand-logo {
    font-size: 64px;
    margin-bottom: 20px;
}

.smartsell-brand-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
}

.smartsell-brand-subtitle {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.smartsell-login-form-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

.smartsell-login-form-container {
    width: 100%;
    max-width: 320px;
}

.smartsell-form-title {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 30px 0;
    color: #333;
}

.smartsell-captcha-container {
    display: flex;
    gap: 10px;
}

.smartsell-captcha-input {
    flex: 1;
}

.smartsell-captcha-image {
    width: 120px;
    height: 42px;
    border-radius: 6px;
    overflow: hidden;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border: 1px solid #ddd;
}

.smartsell-captcha-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.smartsell-btn-block {
    width: 100%;
}

.smartsell-form-message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
    display: none;
}

.smartsell-form-message.error {
    display: block;
    background: #fff2f0;
    border: 1px solid #ffccc7;
    color: #ff4d4f;
}

.smartsell-form-message.success {
    display: block;
    background: #f6ffed;
    border: 1px solid #b7eb8f;
    color: #52c41a;
}

/* 已登录状态 */
.smartsell-logged-in {
    text-align: center;
}

.smartsell-user-info {
    background: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.smartsell-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 15px;
    background: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
}

.smartsell-user-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.smartsell-user-name {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 0 0 5px 0;
}

.smartsell-user-account {
    font-size: 14px;
    color: #666;
    margin: 0 0 15px 0;
}

.smartsell-user-details {
    text-align: left;
    font-size: 14px;
}

.smartsell-user-details .detail-item {
    display: flex;
    padding: 8px 0;
    border-bottom: 1px dashed #e0e0e0;
}

.smartsell-user-details .detail-item:last-child {
    border-bottom: none;
}

.smartsell-user-details .detail-label {
    width: 80px;
    color: #999;
}

.smartsell-user-details .detail-value {
    flex: 1;
    color: #333;
}

.smartsell-login-actions {
    margin-top: 20px;
}

/* 响应式 */
@media (max-width: 768px) {
    .smartsell-login-container {
        flex-direction: column;
    }
    
    .smartsell-login-brand {
        padding: 30px;
    }
    
    .smartsell-brand-title {
        font-size: 22px;
    }
}

/* 表单底部链接 */
.smartsell-form-footer {
    margin-top: 20px;
    text-align: center;
}

.smartsell-form-link {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.smartsell-form-link a {
    color: #4a90d9;
    text-decoration: none;
    font-weight: 500;
}

.smartsell-form-link a:hover {
    text-decoration: underline;
}
</style>

<script>
jQuery(document).ready(function($) {
    // API URL 使用常量配置
    var apiUrl = '<?php echo esc_js($api_url); ?>';
    var captchaKey = '';
    
    // 刷新验证码
    function refreshCaptcha() {
        var url = apiUrl + '/user/captcha';
        
        $('#smartsell-captcha-box').html('<div class="smartsell-spinner"></div>');
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.code === 0) {
                    captchaKey = response.data.captcha_key;
                    $('#smartsell-captcha-key').val(captchaKey);
                    $('#smartsell-captcha-box').html('<img src="' + response.data.captcha_image + '" alt="验证码">');
                    $('#smartsell-captcha').val('');
                } else {
                    $('#smartsell-captcha-box').html('<span style="color:#999;font-size:12px;"><?php esc_html_e('获取失败', 'smartsell-assistant'); ?></span>');
                }
            },
            error: function() {
                $('#smartsell-captcha-box').html('<span style="color:#999;font-size:12px;"><?php esc_html_e('获取失败', 'smartsell-assistant'); ?></span>');
            }
        });
    }
    
    // 初始化时刷新验证码
    refreshCaptcha();
    
    // 点击验证码刷新
    $('#smartsell-captcha-box').on('click', function() {
        refreshCaptcha();
    });
    
    // 登录表单提交
    $('#smartsell-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#smartsell-login-btn');
        var $message = $('#smartsell-login-message');
        
        var username = $('#smartsell-username').val();
        var password = $('#smartsell-password').val();
        var captcha = $('#smartsell-captcha').val();
        
        if (!username || !password || !captcha) {
            $message.removeClass('success').addClass('error').text('<?php esc_html_e('请填写完整信息', 'smartsell-assistant'); ?>');
            return;
        }
        
        $btn.prop('disabled', true).text('<?php esc_html_e('登录中...', 'smartsell-assistant'); ?>');
        $message.removeClass('error success').hide();
        
        // 先调用远程登录API
        $.ajax({
            url: apiUrl + '/user/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                password: password,
                captcha: captcha,
                captcha_key: captchaKey
            }),
            success: function(response) {
                if (response.code === 0) {
                    // 登录成功，保存token到WordPress
                    $.ajax({
                        url: smartsellAdmin.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'smartsell_save_login',
                            nonce: smartsellAdmin.nonce,
                            token: response.data.access_token,
                            app_token: response.data.app_token || '',
                            user_id: response.data.user_id,
                            username: response.data.username,
                            real_name: response.data.real_name || response.data.username
                        },
                        success: function(wpResponse) {
                            if (wpResponse.success) {
                                $message.removeClass('error').addClass('success').text('<?php esc_html_e('登录成功，正在跳转...', 'smartsell-assistant'); ?>');
                                setTimeout(function() {
                                    // 跳转到会话管理页面
                                    window.location.href = '<?php echo esc_url(admin_url('admin.php?page=smartsell-chat')); ?>';
                                }, 1000);
                            } else {
                                $message.removeClass('success').addClass('error').text(wpResponse.data.message || '<?php esc_html_e('保存登录信息失败', 'smartsell-assistant'); ?>');
                                $btn.prop('disabled', false).text('<?php esc_html_e('登录', 'smartsell-assistant'); ?>');
                            }
                        },
                        error: function() {
                            $message.removeClass('success').addClass('error').text('<?php esc_html_e('保存登录信息失败', 'smartsell-assistant'); ?>');
                            $btn.prop('disabled', false).text('<?php esc_html_e('登录', 'smartsell-assistant'); ?>');
                        }
                    });
                } else {
                    $message.removeClass('success').addClass('error').text(response.msg || '<?php esc_html_e('登录失败', 'smartsell-assistant'); ?>');
                    $btn.prop('disabled', false).text('<?php esc_html_e('登录', 'smartsell-assistant'); ?>');
                    refreshCaptcha();
                }
            },
            error: function(xhr) {
                var errorMsg = '<?php esc_html_e('登录失败，请检查网络或API地址', 'smartsell-assistant'); ?>';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.msg) errorMsg = resp.msg;
                } catch(e) {}
                $message.removeClass('success').addClass('error').text(errorMsg);
                $btn.prop('disabled', false).text('<?php esc_html_e('登录', 'smartsell-assistant'); ?>');
                refreshCaptcha();
            }
        });
    });
    
    // 退出登录
    $('#smartsell-logout-btn').on('click', function() {
        if (!confirm('<?php esc_html_e('确定要退出登录吗？', 'smartsell-assistant'); ?>')) {
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('退出中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_logout',
                nonce: smartsellAdmin.nonce
            },
            success: function(response) {
                // 退出成功后跳转到登录页面
                window.location.href = '<?php echo esc_url(admin_url('admin.php?page=smartsell-assistant')); ?>';
            },
            error: function() {
                window.location.href = '<?php echo esc_url(admin_url('admin.php?page=smartsell-assistant')); ?>';
            }
        });
    });
    
    // 显示注册表单
    $('#smartsell-show-register').on('click', function(e) {
        e.preventDefault();
        $('#smartsell-login-form').hide();
        $('#smartsell-register-form').show();
        $('.smartsell-form-title').text('<?php esc_html_e('账户注册', 'smartsell-assistant'); ?>');
    });
    
    // 显示登录表单
    $('#smartsell-show-login').on('click', function(e) {
        e.preventDefault();
        $('#smartsell-register-form').hide();
        $('#smartsell-login-form').show();
        $('.smartsell-form-title').text('<?php esc_html_e('账户登录', 'smartsell-assistant'); ?>');
    });
    
    // 注册表单提交
    $('#smartsell-register-form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#smartsell-register-btn');
        var $message = $('#smartsell-register-message');
        
        var regApiUrl = $('#smartsell-reg-api-url').val() || apiUrl;
        var username = $('#smartsell-reg-username').val();
        var email = $('#smartsell-reg-email').val();
        var password = $('#smartsell-reg-password').val();
        var passwordConfirm = $('#smartsell-reg-password-confirm').val();
        
        if (!username || !email || !password || !passwordConfirm) {
            $message.removeClass('success').addClass('error').text('<?php esc_html_e('请填写完整信息', 'smartsell-assistant'); ?>');
            return;
        }
        
        if (password !== passwordConfirm) {
            $message.removeClass('success').addClass('error').text('<?php esc_html_e('两次输入的密码不一致', 'smartsell-assistant'); ?>');
            return;
        }
        
        if (password.length < 6) {
            $message.removeClass('success').addClass('error').text('<?php esc_html_e('密码长度不能少于6位', 'smartsell-assistant'); ?>');
            return;
        }
        
        $btn.prop('disabled', true).text('<?php esc_html_e('注册中...', 'smartsell-assistant'); ?>');
        $message.removeClass('error success').hide();
        
        // 调用注册 API（使用 query string 格式）
        $.ajax({
            url: apiUrl + '/user/register?username=' + encodeURIComponent(username) + '&email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password),
            type: 'POST',
            success: function(response) {
                if (response.code === 0) {
                    $message.removeClass('error').addClass('success').text('<?php esc_html_e('注册成功，请登录', 'smartsell-assistant'); ?>');
                    setTimeout(function() {
                        // 切换到登录表单
                        $('#smartsell-show-login').trigger('click');
                        $('#smartsell-username').val(username);
                    }, 1500);
                } else {
                    $message.removeClass('success').addClass('error').text(response.msg || '<?php esc_html_e('注册失败', 'smartsell-assistant'); ?>');
                }
                $btn.prop('disabled', false).text('<?php esc_html_e('注册', 'smartsell-assistant'); ?>');
            },
            error: function(xhr) {
                var errorMsg = '<?php esc_html_e('注册失败，请检查网络或API地址', 'smartsell-assistant'); ?>';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.msg) errorMsg = resp.msg;
                } catch(e) {}
                $message.removeClass('success').addClass('error').text(errorMsg);
                $btn.prop('disabled', false).text('<?php esc_html_e('注册', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 加载用户信息 - 直接调用后端 API
    function loadUserInfo() {
        var token = smartsellAdmin.token;
        var url = smartsellAdmin.apiUrl + '/user/me';
        
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function(xhr) {
                if (token) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            },
            success: function(response) {
                if (response.code === 0) {
                    var user = response.data;
                    var html = '';
                    html += '<div class="smartsell-user-avatar">';
                    if (user.avatar_url) {
                        html += '<img src="' + user.avatar_url + '" alt="头像">';
                    } else {
                        html += '👤';
                    }
                    html += '</div>';
                    html += '<h3 class="smartsell-user-name">' + (user.real_name || user.username) + '</h3>';
                    html += '<p class="smartsell-user-account">@' + user.username + '</p>';
                    html += '<div class="smartsell-user-details">';
                    html += '<div class="detail-item"><span class="detail-label"><?php esc_html_e('邮箱', 'smartsell-assistant'); ?></span><span class="detail-value">' + (user.email || '-') + '</span></div>';
                    html += '<div class="detail-item"><span class="detail-label"><?php esc_html_e('手机', 'smartsell-assistant'); ?></span><span class="detail-value">' + (user.phone || '-') + '</span></div>';
                    html += '<div class="detail-item"><span class="detail-label"><?php esc_html_e('性别', 'smartsell-assistant'); ?></span><span class="detail-value">' + (user.gender === 'M' ? '<?php esc_html_e('男', 'smartsell-assistant'); ?>' : (user.gender === 'F' ? '<?php esc_html_e('女', 'smartsell-assistant'); ?>' : '-')) + '</span></div>';
                    html += '</div>';
                    $('#smartsell-user-info').html(html);
                } else {
                    $('#smartsell-user-info').html('<p style="color:#999;"><?php esc_html_e('获取用户信息失败', 'smartsell-assistant'); ?></p>');
                }
            },
            error: function() {
                $('#smartsell-user-info').html('<p style="color:#999;"><?php esc_html_e('获取用户信息失败', 'smartsell-assistant'); ?></p>');
            }
        });
    }
    
    // 初始化
    <?php if ($is_logged_in): ?>
    loadUserInfo();
    <?php else: ?>
    refreshCaptcha();
    <?php endif; ?>
});
</script>
