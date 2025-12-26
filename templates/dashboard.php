<?php
/**
 * 仪表板模板
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('SmartSell客服', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('AI智能客服系统管理面板', 'smartsell-assistant'); ?></p>
    </div>
    
    <!-- 快捷统计 -->
    <div class="smartsell-stats">
        <div class="smartsell-stat-card blue">
            <div class="smartsell-stat-value" id="stat-chats">-</div>
            <div class="smartsell-stat-label"><?php esc_html_e('今日会话', 'smartsell-assistant'); ?></div>
        </div>
        <div class="smartsell-stat-card green">
            <div class="smartsell-stat-value" id="stat-inquiries">-</div>
            <div class="smartsell-stat-label"><?php esc_html_e('新线索', 'smartsell-assistant'); ?></div>
        </div>
        <div class="smartsell-stat-card orange">
            <div class="smartsell-stat-value" id="stat-customers">-</div>
            <div class="smartsell-stat-label"><?php esc_html_e('客户数', 'smartsell-assistant'); ?></div>
        </div>
        <div class="smartsell-stat-card">
            <div class="smartsell-stat-value" id="stat-synced">-</div>
            <div class="smartsell-stat-label"><?php esc_html_e('已同步知识', 'smartsell-assistant'); ?></div>
        </div>
    </div>
    
    <!-- 快捷入口 -->
    <div class="smartsell-card">
        <div class="smartsell-card-header">
            <h2 class="smartsell-card-title"><?php esc_html_e('快捷入口', 'smartsell-assistant'); ?></h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="<?php echo admin_url('admin.php?page=smartsell-chat'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-format-chat"></span>
                <?php esc_html_e('会话管理', 'smartsell-assistant'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=smartsell-inquiry'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-search"></span>
                <?php esc_html_e('线索追踪', 'smartsell-assistant'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=smartsell-customer'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-groups"></span>
                <?php esc_html_e('客户管理', 'smartsell-assistant'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=smartsell-posts-sync'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-admin-post"></span>
                <?php esc_html_e('文章同步', 'smartsell-assistant'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=smartsell-products-sync'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-cart"></span>
                <?php esc_html_e('商品同步', 'smartsell-assistant'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=smartsell-bot-settings'); ?>" class="smartsell-btn smartsell-btn-secondary" style="justify-content: center; padding: 20px;">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('机器人配置', 'smartsell-assistant'); ?>
            </a>
        </div>
    </div>
    
    <!-- 系统配置 -->
    <div class="smartsell-card">
        <div class="smartsell-card-header">
            <h2 class="smartsell-card-title"><?php esc_html_e('API 配置', 'smartsell-assistant'); ?></h2>
        </div>
        
        <form method="post" action="options.php">
            <?php settings_fields('smartsell_settings'); ?>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label" for="smartsell_api_url">
                    <?php esc_html_e('API 地址', 'smartsell-assistant'); ?>
                </label>
                <input type="url" 
                       id="smartsell_api_url" 
                       name="smartsell_api_url" 
                       class="smartsell-form-input" 
                       value="<?php echo esc_attr(get_option('smartsell_api_url', SMARTSELL_DEFAULT_API_URL)); ?>"
                       placeholder="https://app.smartsell.cloud/api">
                <p class="smartsell-form-hint"><?php esc_html_e('SmartSell 服务端 API 地址', 'smartsell-assistant'); ?></p>
            </div>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label" for="smartsell_api_token">
                    <?php esc_html_e('API Token', 'smartsell-assistant'); ?>
                </label>
                <input type="text" 
                       id="smartsell_api_token" 
                       name="smartsell_api_token" 
                       class="smartsell-form-input" 
                       value="<?php echo esc_attr(get_option('smartsell_api_token', '')); ?>"
                       placeholder="请输入 API Token">
                <p class="smartsell-form-hint"><?php esc_html_e('用于 API 认证的 Token', 'smartsell-assistant'); ?></p>
            </div>
            
            <div class="smartsell-form-group">
                <label class="smartsell-form-label" for="smartsell_channel_id">
                    <?php esc_html_e('渠道 ID', 'smartsell-assistant'); ?>
                </label>
                <input type="text" 
                       id="smartsell_channel_id" 
                       name="smartsell_channel_id" 
                       class="smartsell-form-input" 
                       value="<?php echo esc_attr(get_option('smartsell_channel_id', '')); ?>"
                       placeholder="请输入渠道 ID">
                <p class="smartsell-form-hint"><?php esc_html_e('SmartSell 系统中的渠道 ID', 'smartsell-assistant'); ?></p>
            </div>
            
            <button type="submit" class="smartsell-btn smartsell-btn-primary">
                <?php esc_html_e('保存配置', 'smartsell-assistant'); ?>
            </button>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 加载统计数据
    // 这里可以通过 API 获取实时统计数据
});
</script>
