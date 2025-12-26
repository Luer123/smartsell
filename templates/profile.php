<?php
/**
 * 个人中心模板
 */

if (!defined('ABSPATH')) {
    exit;
}

// 获取保存的用户信息
$smartsell_username = get_option('smartsell_username', '');
$smartsell_real_name = get_option('smartsell_real_name', '');
$smartsell_login_time = get_option('smartsell_login_time', '');
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('个人中心', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('查看您的账户信息和使用统计', 'smartsell-assistant'); ?></p>
    </div>
    
    <div class="smartsell-profile-container">
        <!-- 用户信息卡片 -->
        <div class="smartsell-card">
            <h3 class="smartsell-card-title"><?php esc_html_e('账户信息', 'smartsell-assistant'); ?></h3>
            
            <div class="smartsell-profile-info">
                <div class="smartsell-profile-avatar">
                    <div class="smartsell-avatar-placeholder">👤</div>
                </div>
                <div class="smartsell-profile-details">
                    <div class="smartsell-profile-item">
                        <label><?php esc_html_e('用户名', 'smartsell-assistant'); ?></label>
                        <span id="profile-username"><?php echo esc_html($smartsell_username ?: '-'); ?></span>
                    </div>
                    <div class="smartsell-profile-item">
                        <label><?php esc_html_e('真实姓名', 'smartsell-assistant'); ?></label>
                        <span id="profile-realname"><?php echo esc_html($smartsell_real_name ?: '-'); ?></span>
                    </div>
                    <div class="smartsell-profile-item">
                        <label><?php esc_html_e('登录时间', 'smartsell-assistant'); ?></label>
                        <span id="profile-logintime"><?php echo esc_html($smartsell_login_time ?: '-'); ?></span>
                    </div>
                    <div class="smartsell-profile-item">
                        <label><?php esc_html_e('登录状态', 'smartsell-assistant'); ?></label>
                        <span class="smartsell-status-badge success"><?php esc_html_e('已登录', 'smartsell-assistant'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 使用统计 -->
    <div class="smartsell-card">
        <h3 class="smartsell-card-title"><?php esc_html_e('使用统计', 'smartsell-assistant'); ?></h3>
        
        <div class="smartsell-usage-stats">
            <div class="smartsell-usage-item">
                <div class="smartsell-usage-icon">💬</div>
                <div class="smartsell-usage-info">
                    <span class="smartsell-usage-value" id="smartsell-total-chats">-</span>
                    <span class="smartsell-usage-label"><?php esc_html_e('会话总数', 'smartsell-assistant'); ?></span>
                </div>
            </div>
            <div class="smartsell-usage-item">
                <div class="smartsell-usage-icon">📋</div>
                <div class="smartsell-usage-info">
                    <span class="smartsell-usage-value" id="smartsell-total-inquiries">-</span>
                    <span class="smartsell-usage-label"><?php esc_html_e('线索总数', 'smartsell-assistant'); ?></span>
                </div>
            </div>
            <div class="smartsell-usage-item">
                <div class="smartsell-usage-icon">👥</div>
                <div class="smartsell-usage-info">
                    <span class="smartsell-usage-value" id="smartsell-total-customers">-</span>
                    <span class="smartsell-usage-label"><?php esc_html_e('客户总数', 'smartsell-assistant'); ?></span>
                </div>
            </div>
            <div class="smartsell-usage-item">
                <div class="smartsell-usage-icon">📝</div>
                <div class="smartsell-usage-info">
                    <span class="smartsell-usage-value" id="smartsell-synced-posts">-</span>
                    <span class="smartsell-usage-label"><?php esc_html_e('已同步文章', 'smartsell-assistant'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.smartsell-profile-container {
    margin-bottom: 20px;
}

.smartsell-card-title {
    margin: 0 0 20px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
    padding-bottom: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.smartsell-profile-info {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.smartsell-profile-avatar {
    flex-shrink: 0;
}

.smartsell-avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    border: 3px solid #e0e0e0;
}

.smartsell-profile-details {
    flex: 1;
}

.smartsell-profile-item {
    display: flex;
    margin-bottom: 12px;
    font-size: 14px;
}

.smartsell-profile-item label {
    width: 80px;
    color: #666;
    flex-shrink: 0;
}

.smartsell-profile-item span {
    color: #333;
    font-weight: 500;
}

.smartsell-status-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
}

.smartsell-status-badge.success {
    background: #f6ffed;
    color: #52c41a;
    border: 1px solid #b7eb8f;
}

.smartsell-usage-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

@media (max-width: 1200px) {
    .smartsell-usage-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .smartsell-usage-stats {
        grid-template-columns: 1fr;
    }
}

.smartsell-usage-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    gap: 16px;
}

.smartsell-usage-icon {
    font-size: 32px;
}

.smartsell-usage-info {
    display: flex;
    flex-direction: column;
}

.smartsell-usage-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.smartsell-usage-label {
    font-size: 14px;
    color: #666;
}
</style>

<script>
jQuery(document).ready(function($) {
    // 加载使用统计
    function loadUsageStats() {
        // 加载会话数
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/chat/list',
                method: 'GET',
                data: { page: 1, page_size: 1 }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    $('#smartsell-total-chats').text(response.data.data.total || 0);
                }
            }
        });
        
        // 加载线索数
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/list',
                method: 'GET',
                data: { page: 1, page_size: 1 }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    $('#smartsell-total-inquiries').text(response.data.data.total || 0);
                }
            }
        });
        
        // 加载客户数
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/customer/list',
                method: 'GET',
                data: { page: 1, page_size: 1 }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    $('#smartsell-total-customers').text(response.data.data.total || 0);
                }
            }
        });
        
        // 已同步文章数（本地统计）
        var syncedCount = localStorage.getItem('smartsell_synced_posts_count') || 0;
        $('#smartsell-synced-posts').text(syncedCount);
    }
    
    // 初始加载
    loadUsageStats();
});
</script>
