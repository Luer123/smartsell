<?php
/**
 * 机器人配置模板
 */

if (!defined('ABSPATH')) {
    exit;
}

$api_token = get_option('smartsell_api_token', '');

$bot_settings = get_option('smartsell_bot_settings', array(
    'avatar' => '',
    'name' => 'SmartSell客服',
    'greeting' => '您好！我是SmartSell智能客服，有什么可以帮助您的吗？'
));
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('机器人配置', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('自定义AI客服机器人的外观', 'smartsell-assistant'); ?></p>
    </div>
    
    <?php if (!empty($api_token)): ?>
    <div class="smartsell-card smartsell-card-highlight">
        <div class="smartsell-status-box smartsell-status-success">
            <span class="smartsell-status-icon">✅</span>
            <span><?php esc_html_e('机器人已激活，将自动在网站前端显示', 'smartsell-assistant'); ?></span>
        </div>
    </div>
    <?php else: ?>
    <div class="smartsell-card smartsell-card-highlight">
        <div class="smartsell-status-box smartsell-status-warning">
            <span class="smartsell-status-icon">⚠️</span>
            <span><?php esc_html_e('请先登录SmartSell账户以激活机器人', 'smartsell-assistant'); ?></span>
        </div>
    </div>
    <?php endif; ?>
    
    <form id="smartsell-bot-settings-form" class="smartsell-form">
        <div class="smartsell-card">
            <h3 class="smartsell-card-title"><?php esc_html_e('外观设置', 'smartsell-assistant'); ?></h3>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label"><?php esc_html_e('客服头像', 'smartsell-assistant'); ?></label>
                <div class="smartsell-avatar-upload">
                    <div class="smartsell-avatar-preview" id="smartsell-avatar-preview">
                        <?php if (!empty($bot_settings['avatar'])): ?>
                            <img src="<?php echo esc_url($bot_settings['avatar']); ?>" alt="Avatar">
                        <?php else: ?>
                            <div class="smartsell-avatar-placeholder">
                                <span>🤖</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="smartsell-avatar-actions">
                        <input type="hidden" name="bot_avatar" id="smartsell-bot-avatar" value="<?php echo esc_attr($bot_settings['avatar']); ?>">
                        <button type="button" id="smartsell-upload-avatar" class="smartsell-btn smartsell-btn-secondary">
                            <?php esc_html_e('上传头像', 'smartsell-assistant'); ?>
                        </button>
                        <button type="button" id="smartsell-remove-avatar" class="smartsell-btn smartsell-btn-danger" <?php echo empty($bot_settings['avatar']) ? 'style="display:none;"' : ''; ?>>
                            <?php esc_html_e('移除', 'smartsell-assistant'); ?>
                        </button>
                        <p class="smartsell-form-hint"><?php esc_html_e('建议尺寸: 100x100像素', 'smartsell-assistant'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label" for="smartsell-bot-name"><?php esc_html_e('客服名称', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-bot-name" name="bot_name" class="smartsell-form-input" value="<?php echo esc_attr($bot_settings['name']); ?>">
            </div>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label" for="smartsell-bot-greeting"><?php esc_html_e('欢迎语', 'smartsell-assistant'); ?></label>
                <textarea id="smartsell-bot-greeting" name="bot_greeting" class="smartsell-form-textarea" rows="3"><?php echo esc_textarea($bot_settings['greeting']); ?></textarea>
                <p class="smartsell-form-hint"><?php esc_html_e('用户打开聊天窗口时显示的第一条消息', 'smartsell-assistant'); ?></p>
            </div>
        </div>
        
        <div class="smartsell-form-actions">
            <button type="submit" class="smartsell-btn smartsell-btn-primary smartsell-btn-lg">
                <?php esc_html_e('保存设置', 'smartsell-assistant'); ?>
            </button>
        </div>
    </form>
</div>

<style>
.smartsell-card-highlight {
    border: 2px solid #4a90d9;
    background: linear-gradient(135deg, #f0f7ff 0%, #fff 100%);
}

.smartsell-card-title {
    margin: 0 0 20px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    padding-bottom: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.smartsell-status-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 14px;
}

.smartsell-status-success {
    background: #f6ffed;
    border: 1px solid #b7eb8f;
    color: #52c41a;
}

.smartsell-status-warning {
    background: #fffbe6;
    border: 1px solid #ffe58f;
    color: #faad14;
}

.smartsell-status-icon {
    font-size: 16px;
}

.smartsell-avatar-upload {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.smartsell-avatar-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e0e0e0;
}

.smartsell-avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.smartsell-avatar-placeholder {
    font-size: 48px;
}

.smartsell-avatar-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.smartsell-form-actions {
    margin-top: 20px;
}

.smartsell-btn-lg {
    padding: 12px 32px;
    font-size: 16px;
}

.smartsell-btn-danger {
    background: #ff4d4f;
    color: #fff;
}

.smartsell-btn-danger:hover {
    background: #ff7875;
}
</style>

<script>
jQuery(document).ready(function($) {
    // 媒体库上传
    var mediaUploader;
    
    $('#smartsell-upload-avatar').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: '<?php esc_html_e('选择客服头像', 'smartsell-assistant'); ?>',
            button: {
                text: '<?php esc_html_e('使用此图片', 'smartsell-assistant'); ?>'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#smartsell-bot-avatar').val(attachment.url);
            $('#smartsell-avatar-preview').html('<img src="' + attachment.url + '" alt="Avatar">');
            $('#smartsell-remove-avatar').show();
        });
        
        mediaUploader.open();
    });
    
    // 移除头像
    $('#smartsell-remove-avatar').on('click', function() {
        $('#smartsell-bot-avatar').val('');
        $('#smartsell-avatar-preview').html('<div class="smartsell-avatar-placeholder"><span>🤖</span></div>');
        $(this).hide();
    });
    
    // 表单提交
    $('#smartsell-bot-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            avatar: $('#smartsell-bot-avatar').val(),
            name: $('#smartsell-bot-name').val(),
            greeting: $('#smartsell-bot-greeting').val()
        };
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_save_bot_settings',
                nonce: smartsellAdmin.nonce,
                settings: formData
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php esc_html_e('设置已保存', 'smartsell-assistant'); ?>');
                } else {
                    alert('<?php esc_html_e('保存失败，请重试', 'smartsell-assistant'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('保存失败，请重试', 'smartsell-assistant'); ?>');
            }
        });
    });
});
</script>
