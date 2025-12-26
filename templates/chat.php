<?php
/**
 * 会话管理模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
/* 聊天详情样式 */
.smartsell-chat-container {
    display: flex;
    gap: 20px;
    height: calc(100vh - 200px);
    min-height: 500px;
}
.smartsell-chat-list-panel {
    width: 400px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.smartsell-chat-detail-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.smartsell-chat-detail-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.smartsell-chat-detail-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}
.smartsell-chat-detail-meta {
    font-size: 12px;
    color: #6b7280;
}
.smartsell-chat-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}
.smartsell-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}
.smartsell-toggle-label {
    font-size: 13px;
    color: #374151;
}
.smartsell-toggle {
    position: relative;
    width: 44px;
    height: 22px;
}
.smartsell-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.smartsell-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .3s;
    border-radius: 22px;
}
.smartsell-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
.smartsell-toggle input:checked + .smartsell-toggle-slider {
    background-color: #3b82f6;
}
.smartsell-toggle input:checked + .smartsell-toggle-slider:before {
    transform: translateX(22px);
}
.smartsell-status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}
.smartsell-status-dot.online {
    background-color: #10b981;
}
.smartsell-status-dot.offline {
    background-color: #9ca3af;
}
.smartsell-status-dot.connecting {
    background-color: #f59e0b;
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.smartsell-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f9fafb;
}
.smartsell-message {
    display: flex;
    margin-bottom: 16px;
}
.smartsell-message.customer {
    justify-content: flex-start;
}
.smartsell-message.agent {
    justify-content: flex-end;
}
.smartsell-message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}
.smartsell-message.customer .smartsell-message-bubble {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}
.smartsell-message.agent .smartsell-message-bubble {
    background: #3b82f6;
    color: #fff;
    border-bottom-right-radius: 4px;
}
/* 商品/资料卡片样式 */
.smartsell-message-bubble card {
    display: block;
    max-width: 100%;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin: 12px 0;
    overflow: hidden;
    border: 1px solid #eaeaea;
}
.smartsell-message.agent .smartsell-message-bubble card {
    color: #333;
}
.smartsell-message-bubble card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
    border-bottom: 1px solid #f0f0f0;
}
.smartsell-message-bubble card div {
    padding: 12px;
}
.smartsell-message-bubble card span {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}
.smartsell-message-bubble card time {
    display: inline-block;
    font-size: 12px;
    color: #999;
}
.smartsell-message-bubble card a {
    display: inline-block;
    float: right;
    font-size: 13px;
    color: #3b82f6;
    text-decoration: none;
}
.smartsell-message-bubble card a:hover {
    text-decoration: underline;
}
.smartsell-message-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
}
.smartsell-message.agent .smartsell-message-time {
    text-align: right;
    color: rgba(255,255,255,0.7);
}
.smartsell-chat-input-area {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 0 0 8px 8px;
}
.smartsell-chat-input-wrap {
    display: flex;
    gap: 10px;
}
.smartsell-chat-textarea {
    flex: 1;
    min-height: 60px;
    max-height: 120px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    resize: none;
    font-size: 14px;
    line-height: 1.5;
}
.smartsell-chat-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}
.smartsell-chat-send-btn {
    padding: 10px 24px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background .2s;
}
.smartsell-chat-send-btn:hover {
    background: #2563eb;
}
.smartsell-chat-send-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
.smartsell-chat-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #9ca3af;
}
.smartsell-chat-empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
}
.smartsell-chat-empty-text {
    font-size: 14px;
}
.smartsell-chat-list-item {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    cursor: pointer;
    transition: background .2s;
}
.smartsell-chat-list-item:hover {
    background: #f3f4f6;
}
.smartsell-chat-list-item.active {
    background: #eff6ff;
    border-left: 3px solid #3b82f6;
}
.smartsell-chat-list-item-title {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.smartsell-chat-list-item-meta {
    font-size: 12px;
    color: #6b7280;
}
.smartsell-unread-badge {
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 500;
}

/* 表单两列对齐样式 */
.smartsell-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.smartsell-form-group {
    margin-bottom: 0;
}

.smartsell-form-group-full {
    grid-column: 1 / -1;
    margin-bottom: 20px;
}

.smartsell-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.smartsell-required {
    color: #ef4444;
    margin-left: 4px;
}

.smartsell-input,
.smartsell-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
    box-sizing: border-box;
    font-family: inherit;
}

.smartsell-input:focus,
.smartsell-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.smartsell-input-error,
.smartsell-textarea.smartsell-input-error {
    border-color: #ef4444 !important;
}

.smartsell-input-error:focus,
.smartsell-textarea.smartsell-input-error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.smartsell-form-error {
    display: block;
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
}

.smartsell-textarea {
    min-height: 60px;
    resize: vertical;
}
</style>

<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('会话管理', 'smartsell-assistant'); ?></h1>
        <p>
            <?php esc_html_e('查看和管理所有客户会话，支持手动回复', 'smartsell-assistant'); ?>
            <span id="smartsell-ws-status" style="margin-left: 15px; font-size: 12px;"><span class="smartsell-status-dot connecting"></span><?php esc_html_e('连接中...', 'smartsell-assistant'); ?></span>
        </p>
    </div>
    
    <div class="smartsell-chat-container">
        <!-- 左侧会话列表 -->
        <div class="smartsell-chat-list-panel">
            <div class="smartsell-card" style="flex: 1; display: flex; flex-direction: column; margin-bottom: 0;">
                <!-- 筛选区域 -->
                <div class="smartsell-filters" style="padding: 15px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" id="smartsell-chat-search" class="smartsell-form-input" placeholder="<?php esc_attr_e('搜索标题...', 'smartsell-assistant'); ?>" style="flex: 1;">
                        <button type="button" id="smartsell-chat-filter" class="smartsell-btn smartsell-btn-primary">
                            <?php esc_html_e('搜索', 'smartsell-assistant'); ?>
                        </button>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <input type="date" id="smartsell-chat-start-date" class="smartsell-form-input" style="flex: 1;">
                        <input type="date" id="smartsell-chat-end-date" class="smartsell-form-input" style="flex: 1;">
                    </div>
                </div>
                
                <!-- 会话列表 -->
                <div id="smartsell-chat-list" style="flex: 1; overflow-y: auto;">
                    <div class="smartsell-loading" style="padding: 40px; text-align: center;">
                        <div class="smartsell-spinner"></div>
                    </div>
                </div>
                
                <!-- 分页 -->
                <div class="smartsell-pagination" data-type="chat" style="padding: 10px 15px; border-top: 1px solid #e5e7eb;"></div>
            </div>
        </div>
        
        <!-- 右侧聊天详情 -->
        <div class="smartsell-chat-detail-panel" id="smartsell-chat-detail">
            <div class="smartsell-chat-empty-state">
                <div class="smartsell-chat-empty-icon">💬</div>
                <div class="smartsell-chat-empty-text"><?php esc_html_e('请从左侧选择一个会话', 'smartsell-assistant'); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var currentPage = 1;
    var currentChatId = null;
    var currentSessionId = null;
    var isAiStop = false;
    
    // WebSocket 相关变量
    var ws = null;
    var wsReconnectTimer = null;
    var wsHeartbeatTimer = null;
    var wsConnected = false;
    var userId = '<?php echo esc_js(get_option('smartsell_user_id', '')); ?>';
    
    // 获取 WebSocket 地址 (从 API 地址推断)
    var apiUrl = smartsellAdmin.apiUrl || '';
    var wsUrl = apiUrl.replace(/^http/, 'ws').replace(/\/api$/, '') + '/smartsell/chat';
    
    // 初始化 WebSocket 连接
    function initWebSocket() {
        if (!userId) {
            console.warn('未找到用户ID，无法建立WebSocket连接');
            updateWsStatus('offline');
            return;
        }
        
        var sessionId = 'admin_' + userId;
        console.log('正在连接WebSocket:', wsUrl + '?session_id=' + sessionId);
        updateWsStatus('connecting');
        
        try {
            ws = new WebSocket(wsUrl + '?session_id=' + sessionId);
            
            ws.onopen = function() {
                console.log('✓ WebSocket连接已建立');
                wsConnected = true;
                updateWsStatus('connected');
                startHeartbeat();
                
                // 连接成功后，请求订阅所有会话状态
                setTimeout(function() {
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({
                            type: 'subscribe',
                            data: {
                                admin_id: userId,
                                subscribe_type: 'all_sessions'
                            }
                        }));
                        console.log('>>> 已发送订阅请求');
                    }
                }, 500);
            };
            
            ws.onmessage = function(event) {
                try {
                    var data = JSON.parse(event.data);
                    console.log('收到WebSocket消息:', data);
                    handleWsMessage(data);
                } catch (e) {
                    console.error('解析WebSocket消息失败:', e);
                }
            };
            
            ws.onclose = function(event) {
                console.log('WebSocket连接已关闭:', event.code, event.reason);
                wsConnected = false;
                updateWsStatus('offline');
                stopHeartbeat();
                
                // 非正常关闭时尝试重连
                if (event.code !== 1000 && event.code !== 1001) {
                    scheduleReconnect();
                }
            };
            
            ws.onerror = function(error) {
                console.error('WebSocket错误:', error);
                wsConnected = false;
                updateWsStatus('offline');
            };
        } catch (e) {
            console.error('创建WebSocket连接失败:', e);
            updateWsStatus('offline');
        }
    }
    
    // 处理 WebSocket 消息
    function handleWsMessage(data) {
        console.log('=== 收到WebSocket原始消息 ===', data);
        console.log('消息类型:', data.type, '| 消息代码:', data.code);
        
        // 心跳响应
        if (data.type === 'health_response' || data.type === 'pong') {
            console.log('✓ 收到心跳响应');
            return;
        }
        
        // 会话连接状态变化
        if (data.type === 'session_status' && data.data) {
            console.log('✓ 收到会话状态变化:', data.data);
            handleSessionStatusChange(data.data);
            return;
        }
        
        // 用户上线/下线通知
        if (data.type === 'user_online' || data.type === 'user_offline' || 
            data.type === 'online' || data.type === 'offline') {
            console.log('✓ 收到用户状态变化:', data.type, data.data || data);
            handleUserStatusChange(data);
            return;
        }
        
        // 新消息事件（参考前端Vue实现：直接重新获取完整聊天记录）
        if (data.type === 'new_message' || data.type === 'message') {
            console.log('✓ 收到新消息事件:', data.data);
            if (data.data && data.data.session_id) {
                // 立即更新该会话的在线状态为在线（左侧列表）
                var $item = $('.smartsell-chat-list-item[data-session="' + data.data.session_id + '"]');
                if ($item.length) {
                    var $statusDot = $item.find('.smartsell-status-dot');
                    $statusDot.removeClass('offline connecting').addClass('online');
                    $item.attr('data-is-connected', 'true');
                    console.log('✓ 已将会话设置为在线:', data.data.session_id);
                }
                
                // 如果是当前查看的会话，重新获取完整的聊天记录（参考前端fetchChatLogs实现）
                if (currentSessionId && data.data.session_id === currentSessionId && currentChatId) {
                    console.log('✓ 当前会话有新消息，重新获取完整聊天记录');
                    // 直接重新加载完整的聊天详情（这样会从API获取最新的状态和消息）
                    loadChatDetail(currentChatId, false);
                }
                // 延迟刷新会话列表（避免频繁刷新影响性能）
                setTimeout(function() {
                    loadChats(currentPage, true);
                }, 500);
            }
            return;
        }
        
        // 未读消息通知
        if (data.type === 'unread' && data.data) {
            console.log('✓ 收到未读消息通知:', data.data);
            handleUnreadNotification(data.data);
            return;
        }
        
        // 新消息通知（旧格式兼容，参考前端Vue实现）
        if (data.code === 0 && data.data && data.data.output) {
            console.log('✓ 收到新消息(旧格式):', data.data);
            
            // 立即更新该会话的在线状态为在线（左侧列表）
            if (data.data.session_id) {
                var $item = $('.smartsell-chat-list-item[data-session="' + data.data.session_id + '"]');
                if ($item.length) {
                    var $statusDot = $item.find('.smartsell-status-dot');
                    $statusDot.removeClass('offline connecting').addClass('online');
                    $item.attr('data-is-connected', 'true');
                    console.log('✓ 已将会话设置为在线:', data.data.session_id);
                }
            }
            
            // 如果是当前查看的会话，重新获取完整的聊天记录（参考前端fetchChatLogs实现）
            if (currentSessionId && data.data.session_id === currentSessionId && currentChatId) {
                console.log('✓ 当前会话有新消息，重新获取完整聊天记录');
                // 直接重新加载完整的聊天详情（这样会从API获取最新的状态和消息）
                loadChatDetail(currentChatId, false);
            }
            // 延迟刷新会话列表以更新未读数（避免频繁刷新影响性能）
            setTimeout(function() {
                loadChats(currentPage, true);
            }, 500);
            return;
        }
        
        console.warn('⚠ 未处理的WebSocket消息类型:', data);
    }
    
    // 处理会话状态变化
    function handleSessionStatusChange(statusData) {
        console.log('>>> 处理会话状态变化:', statusData);
        if (statusData.session_id) {
            // 更新左侧列表中的状态
            var $item = $('.smartsell-chat-list-item[data-session="' + statusData.session_id + '"]');
            console.log('>>> 找到会话元素:', $item.length > 0 ? '是' : '否', '| session_id:', statusData.session_id);
            if ($item.length) {
                var $statusDot = $item.find('.smartsell-status-dot');
                var isOnline = statusData.is_connected === true || statusData.is_connected === 1 || 
                               statusData.status === 'online' || statusData.status === 1 ||
                               statusData.is_connected === 'true';
                if (isOnline) {
                    $statusDot.removeClass('offline connecting').addClass('online');
                    $item.attr('data-is-connected', 'true');
                    console.log('✓ 设置为在线');
                } else {
                    $statusDot.removeClass('online connecting').addClass('offline');
                    $item.attr('data-is-connected', 'false');
                    console.log('✓ 设置为离线');
                }
            } else {
                // 如果找不到元素，可能是新会话，刷新列表
                console.log('>>> 未找到会话元素，刷新列表');
                loadChats(currentPage, true);
            }
            
            // 如果是当前查看的会话，同时更新右侧详情中的状态显示
            if (currentSessionId && statusData.session_id === currentSessionId) {
                var isOnline = statusData.is_connected === true || statusData.is_connected === 1 || 
                               statusData.status === 'online' || statusData.status === 1 ||
                               statusData.is_connected === 'true';
                updateConnectionStatusDisplay(isOnline);
            }
        }
    }
    
    // 处理用户上线/下线
    function handleUserStatusChange(data) {
        console.log('>>> 处理用户上线/下线:', data);
        var isOnline = data.type === 'user_online' || data.type === 'online';
        var sessionId = (data.data && data.data.session_id) || data.session_id || (data.data && data.data.id);
        
        console.log('>>> 在线状态:', isOnline ? '在线' : '离线', '| session_id:', sessionId);
        
        if (sessionId) {
            // 更新左侧列表中的状态
            var $item = $('.smartsell-chat-list-item[data-session="' + sessionId + '"]');
            console.log('>>> 找到会话元素:', $item.length > 0 ? '是' : '否');
            if ($item.length) {
                var $statusDot = $item.find('.smartsell-status-dot');
                if (isOnline) {
                    $statusDot.removeClass('offline connecting').addClass('online');
                    $item.attr('data-is-connected', 'true');
                    console.log('✓ 设置为在线');
                } else {
                    $statusDot.removeClass('online connecting').addClass('offline');
                    $item.attr('data-is-connected', 'false');
                    console.log('✓ 设置为离线');
                }
            } else {
                // 如果找不到元素，可能是新会话，刷新列表
                console.log('>>> 未找到会话元素，刷新列表');
                loadChats(currentPage, true);
            }
            
            // 如果是当前查看的会话，同时更新右侧详情中的状态显示
            if (currentSessionId && sessionId === currentSessionId) {
                updateConnectionStatusDisplay(isOnline);
            }
        } else {
            // 如果没有session_id，刷新整个列表
            console.log('>>> 没有session_id，刷新列表');
            loadChats(currentPage, true);
        }
    }
    
    // 处理未读消息通知
    function handleUnreadNotification(unreadData) {
        // 更新会话列表中的未读数
        $.each(unreadData, function(sessionId, count) {
            var $item = $('.smartsell-chat-list-item[data-session="' + sessionId + '"]');
            if ($item.length) {
                var $badge = $item.find('.smartsell-unread-badge');
                if (count > 0) {
                    if ($badge.length) {
                        $badge.text(count);
                    } else {
                        $item.find('.smartsell-chat-list-item-title').append('<span class="smartsell-unread-badge">' + count + '</span>');
                    }
                } else {
                    $badge.remove();
                }
            }
        });
        
        // 如果是当前查看的会话有新消息，刷新聊天记录
        if (currentSessionId && unreadData[currentSessionId]) {
            loadChatDetail(currentChatId, true);
        }
    }
    
    // 更新 WebSocket 状态显示
    function updateWsStatus(status) {
        var $status = $('#smartsell-ws-status');
        if ($status.length) {
            if (status === 'connected') {
                $status.html('<span class="smartsell-status-dot online"></span><?php esc_html_e('实时连接', 'smartsell-assistant'); ?>');
            } else if (status === 'connecting') {
                $status.html('<span class="smartsell-status-dot connecting"></span><?php esc_html_e('连接中...', 'smartsell-assistant'); ?>');
            } else {
                $status.html('<span class="smartsell-status-dot offline"></span><?php esc_html_e('离线', 'smartsell-assistant'); ?>');
            }
        }
    }
    
    // 发送心跳
    function startHeartbeat() {
        stopHeartbeat();
        wsHeartbeatTimer = setInterval(function() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({
                    type: 'health',
                    data: {
                        session_id: 'admin_' + userId,
                        timestamp: new Date().getTime()
                    }
                }));
            }
        }, 15000); // 15秒一次心跳
    }
    
    // 停止心跳
    function stopHeartbeat() {
        if (wsHeartbeatTimer) {
            clearInterval(wsHeartbeatTimer);
            wsHeartbeatTimer = null;
        }
    }
    
    // 计划重连
    function scheduleReconnect() {
        if (wsReconnectTimer) {
            clearTimeout(wsReconnectTimer);
        }
        wsReconnectTimer = setTimeout(function() {
            console.log('尝试重新连接WebSocket...');
            initWebSocket();
        }, 5000); // 5秒后重连
    }
    
    // 加载会话列表
    function loadChats(page, silent) {
        var search = $('#smartsell-chat-search').val();
        var startDate = $('#smartsell-chat-start-date').val();
        var endDate = $('#smartsell-chat-end-date').val();
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/chat/list_all',
                method: 'GET',
                data: {
                    page: page,
                    page_size: 10,
                    search_title: search,
                    start_date: startDate,
                    end_date: endDate
                }
            },
            beforeSend: function() {
                // 静默刷新时不显示加载动画
                if (!silent) {
                    $('#smartsell-chat-list').html('<div class="smartsell-loading" style="padding: 40px; text-align: center;"><div class="smartsell-spinner"></div></div>');
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderChats(response.data.data);
                } else if (!silent) {
                    $('#smartsell-chat-list').html('<div class="smartsell-empty" style="padding: 40px; text-align: center;"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></div>');
                }
            }
        });
    }
    
    // 渲染会话列表
    function renderChats(data) {
        var html = '';
        
        if (!data.items || data.items.length === 0) {
            html = '<div class="smartsell-empty" style="padding: 40px; text-align: center;"><div class="smartsell-empty-icon">💬</div><div class="smartsell-empty-text"><?php esc_html_e('暂无会话', 'smartsell-assistant'); ?></div></div>';
        } else {
            var now = new Date().getTime();
            $.each(data.items, function(i, chat) {
                var activeClass = chat.id === currentChatId ? ' active' : '';
                
                // 判断在线状态：优先使用 is_connected，其次判断最后活动时间
                var isOnline = false;
                if (typeof chat.is_connected !== 'undefined') {
                    isOnline = chat.is_connected;
                } else if (chat.last_active_time) {
                    // 如果最后活动时间在5分钟内，视为在线
                    var lastActiveTime = new Date(chat.last_active_time).getTime();
                    isOnline = (now - lastActiveTime) < 5 * 60 * 1000; // 5分钟
                } else if (chat.update_time) {
                    // 如果更新时间在5分钟内，视为在线
                    var updateTime = new Date(chat.update_time).getTime();
                    isOnline = (now - updateTime) < 5 * 60 * 1000; // 5分钟
                }
                
                var statusDotClass = isOnline ? 'online' : 'offline';
                var unreadHtml = chat.unread_count > 0 ? '<span class="smartsell-unread-badge">' + chat.unread_count + '</span>' : '';
                
                html += '<div class="smartsell-chat-list-item' + activeClass + '" data-id="' + chat.id + '" data-session="' + (chat.session_id || '') + '" data-is-connected="' + isOnline + '">';
                html += '<div class="smartsell-chat-list-item-title">';
                html += '<span><span class="smartsell-status-dot ' + statusDotClass + '"></span>' + (chat.title || '<?php esc_html_e('未命名会话', 'smartsell-assistant'); ?>') + '</span>';
                html += unreadHtml;
                html += '</div>';
                html += '<div class="smartsell-chat-list-item-meta">' + (chat.ip_address || '-') + ' · ' + (chat.update_time || '-') + '</div>';
                html += '</div>';
            });
        }
        
        $('#smartsell-chat-list').html(html);
        
        // 渲染分页
        renderPagination(data.total, data.total_pages, data.page);
    }
    
    // 渲染分页
    function renderPagination(total, pages, current) {
        var html = '<div class="smartsell-pagination-info" style="font-size: 12px; color: #6b7280;"><?php esc_html_e('共', 'smartsell-assistant'); ?> ' + total + ' <?php esc_html_e('条', 'smartsell-assistant'); ?></div>';
        html += '<div class="smartsell-pagination-links">';
        
        if (current > 1) {
            html += '<a href="#" data-page="' + (current - 1) + '">&laquo;</a>';
        }
        
        var startPage = Math.max(1, current - 2);
        var endPage = Math.min(pages, current + 2);
        
        for (var i = startPage; i <= endPage; i++) {
            if (i === current) {
                html += '<span class="current">' + i + '</span>';
            } else {
                html += '<a href="#" data-page="' + i + '">' + i + '</a>';
            }
        }
        
        if (current < pages) {
            html += '<a href="#" data-page="' + (current + 1) + '">&raquo;</a>';
        }
        
        html += '</div>';
        
        $('.smartsell-pagination[data-type="chat"]').html(html);
    }
    
    // 加载聊天详情（参考前端Vue的fetchChatLogs和getChatInfo实现）
    function loadChatDetail(chatId, isRefresh) {
        // 如果不是刷新操作，显示加载动画
        if (!isRefresh) {
            $('#smartsell-chat-detail').html('<div class="smartsell-chat-empty-state"><div class="smartsell-spinner"></div><div class="smartsell-chat-empty-text" style="margin-top: 15px;"><?php esc_html_e('加载中...', 'smartsell-assistant'); ?></div></div>');
        }
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/chat/chat_log',
                method: 'GET',
                data: {
                    chat_id: chatId
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    // 参考前端实现：无论是否刷新，都重新渲染完整详情
                    // 这样可以从API获取最新的状态信息（包括is_connected）
                    renderChatDetail(response.data.data);
                } else if (!isRefresh) {
                    $('#smartsell-chat-detail').html('<div class="smartsell-chat-empty-state"><div class="smartsell-chat-empty-icon">❌</div><div class="smartsell-chat-empty-text"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></div></div>');
                }
            },
            error: function(xhr, status, error) {
                if (!isRefresh) {
                    console.error('加载聊天详情失败:', error);
                    $('#smartsell-chat-detail').html('<div class="smartsell-chat-empty-state"><div class="smartsell-chat-empty-icon">❌</div><div class="smartsell-chat-empty-text"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></div></div>');
                }
            }
        });
    }
    
    // 注意：updateMessages函数已不再使用
    // 参考前端Vue实现，收到新消息时直接重新获取完整聊天记录（loadChatDetail）
    // 这样可以确保状态和消息都是最新的，不需要单独维护增量更新逻辑
    
    // 渲染聊天详情
    function renderChatDetail(data) {
        var chatInfo = data.chat_info;
        var logs = data.chat_logs || [];
        
        currentSessionId = chatInfo.session_id;
        isAiStop = chatInfo.is_stop === 1;
        
        // 从API数据中读取初始连接状态（优先使用is_connected，如果没有则根据最后活动时间判断）
        var isInitiallyConnected = false;
        if (typeof chatInfo.is_connected !== 'undefined') {
            isInitiallyConnected = chatInfo.is_connected === 1 || chatInfo.is_connected === true;
        } else if (chatInfo.last_active_time) {
            // 如果最后活动时间在5分钟内，视为在线
            var now = new Date().getTime();
            var lastActiveTime = new Date(chatInfo.last_active_time).getTime();
            isInitiallyConnected = (now - lastActiveTime) < 5 * 60 * 1000; // 5分钟
        }
        
        var statusDotClass = isInitiallyConnected ? 'online' : 'offline';
        var statusText = isInitiallyConnected ? '<?php esc_html_e('在线', 'smartsell-assistant'); ?>' : '<?php esc_html_e('离线', 'smartsell-assistant'); ?>';
        
        var html = '<div class="smartsell-chat-detail-header">';
        html += '<div>';
        html += '<div class="smartsell-chat-detail-title">' + (chatInfo.title || '<?php esc_html_e('未命名会话', 'smartsell-assistant'); ?>') + '</div>';
        html += '<div class="smartsell-chat-detail-meta">';
        html += '<span id="smartsell-connection-status"><span class="smartsell-status-dot ' + statusDotClass + '"></span>' + statusText + '</span>';
        html += ' · <?php esc_html_e('开始时间', 'smartsell-assistant'); ?>: ' + (chatInfo.start_time || '-');
        html += '</div>';
        html += '</div>';
        html += '<div class="smartsell-chat-controls">';
        html += '<button type="button" class="smartsell-btn smartsell-btn-success smartsell-btn-sm" id="smartsell-extract-inquiry"><?php esc_html_e('提取线索', 'smartsell-assistant'); ?></button>';
        html += '<div class="smartsell-toggle-wrap">';
        html += '<span class="smartsell-toggle-label"><?php esc_html_e('暂停AI', 'smartsell-assistant'); ?></span>';
        html += '<label class="smartsell-toggle">';
        html += '<input type="checkbox" id="smartsell-ai-stop" ' + (isAiStop ? 'checked' : '') + '>';
        html += '<span class="smartsell-toggle-slider"></span>';
        html += '</label>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        html += '<div class="smartsell-chat-messages" id="smartsell-chat-messages">';
        if (logs.length === 0) {
            html += '<div class="smartsell-chat-empty-state"><div class="smartsell-chat-empty-text"><?php esc_html_e('暂无消息', 'smartsell-assistant'); ?></div></div>';
        } else {
            $.each(logs, function(i, log) {
                var msgClass = log.type === 1 ? 'customer' : 'agent';
                html += '<div class="smartsell-message ' + msgClass + '">';
                html += '<div class="smartsell-message-content">';
                html += '<div class="smartsell-message-bubble">' + formatMessage(log.content) + '</div>';
                html += '<div class="smartsell-message-time">' + (log.create_time || '') + '</div>';
                html += '</div>';
                html += '</div>';
            });
        }
        html += '</div>';
        
        html += '<div class="smartsell-chat-input-area">';
        html += '<div class="smartsell-chat-input-wrap">';
        html += '<textarea class="smartsell-chat-textarea" id="smartsell-chat-input" placeholder="<?php esc_attr_e('输入消息...', 'smartsell-assistant'); ?>"></textarea>';
        html += '<button type="button" class="smartsell-chat-send-btn" id="smartsell-chat-send"><?php esc_html_e('发送', 'smartsell-assistant'); ?></button>';
        html += '</div>';
        html += '</div>';
        
        $('#smartsell-chat-detail').html(html);
        
        // 滚动到底部（参考前端Vue的scrollToBottom实现）
        scrollToBottom();
        
        // 参考前端Vue实现：不再单独检查连接状态
        // 因为renderChatDetail已经从API获取了最新的chatInfo，包括is_connected状态
        // 状态已经在渲染时根据API数据正确设置了
    }
    
    // 更新连接状态显示
    function updateConnectionStatusDisplay(isConnected) {
        var $statusElement = $('#smartsell-connection-status');
        if ($statusElement.length) {
            var statusDotClass = isConnected ? 'online' : 'offline';
            var statusText = isConnected ? '<?php esc_html_e('在线', 'smartsell-assistant'); ?>' : '<?php esc_html_e('离线', 'smartsell-assistant'); ?>';
            $statusElement.html('<span class="smartsell-status-dot ' + statusDotClass + '"></span>' + statusText);
        }
    }
    
    // 滚动到底部
    function scrollToBottom() {
        var container = document.getElementById('smartsell-chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // 检查连接状态
    function checkConnectionStatus() {
        if (!currentSessionId) return;
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/ws/status',
                method: 'GET',
                data: {
                    session_id: currentSessionId
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    var isConnected = response.data.data.status === 1 || response.data.data.status === true;
                    updateConnectionStatusDisplay(isConnected);
                    console.log('✓ 连接状态已更新:', isConnected ? '在线' : '离线');
                } else {
                    console.warn('获取连接状态失败:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('检查连接状态时出错:', error);
            }
        });
    }
    
    // 发送消息
    function sendMessage() {
        var message = $('#smartsell-chat-input').val().trim();
        if (!message || !currentSessionId) return;
        
        var $btn = $('#smartsell-chat-send');
        $btn.prop('disabled', true).text('<?php esc_html_e('发送中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/ws/send',
                method: 'POST',
                data: {
                    session_id: currentSessionId,
                    output: message
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    // 清空输入框
                    $('#smartsell-chat-input').val('');
                    
                    // 添加消息到界面
                    var now = new Date();
                    var timeStr = now.getFullYear() + '-' + 
                        String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(now.getDate()).padStart(2, '0') + ' ' +
                        String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0') + ':' +
                        String(now.getSeconds()).padStart(2, '0');
                    
                    var msgHtml = '<div class="smartsell-message agent">';
                    msgHtml += '<div class="smartsell-message-content">';
                    msgHtml += '<div class="smartsell-message-bubble">' + escapeHtml(message) + '</div>';
                    msgHtml += '<div class="smartsell-message-time">' + timeStr + '</div>';
                    msgHtml += '</div>';
                    msgHtml += '</div>';
                    
                    $('#smartsell-chat-messages').append(msgHtml);
                    scrollToBottom();
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('发送失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('发送失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('发送', 'smartsell-assistant'); ?>');
            }
        });
    }
    
    // 切换AI状态
    function toggleAiStop(isStop) {
        if (!currentChatId) return;
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/chat/update',
                method: 'POST',
                data: {
                    chat_id: currentChatId,
                    is_stop: isStop ? 1 : 0
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    isAiStop = isStop;
                } else {
                    // 恢复开关状态
                    $('#smartsell-ai-stop').prop('checked', !isStop);
                }
            },
            error: function() {
                $('#smartsell-ai-stop').prop('checked', !isStop);
            }
        });
    }
    
    // HTML转义
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // 格式化消息（支持card标签）
    function formatMessage(text) {
        if (!text) return '';
        
        try {
            // 匹配 <card>...</card> 标签
            var cardRegex = /<card>[\s\S]*?<\/card>/g;
            var parts = [];
            var lastIndex = 0;
            var match;
            
            while ((match = cardRegex.exec(text)) !== null) {
                // 卡片前的文本需要转义
                if (match.index > lastIndex) {
                    var beforeCard = text.substring(lastIndex, match.index);
                    parts.push(escapeHtml(beforeCard).replace(/\n/g, '<br>'));
                }
                // 卡片内容直接保留
                parts.push(match[0]);
                lastIndex = match.index + match[0].length;
            }
            
            // 最后一个卡片后的文本
            if (lastIndex < text.length) {
                parts.push(escapeHtml(text.substring(lastIndex)).replace(/\n/g, '<br>'));
            }
            
            return parts.join('');
        } catch (e) {
            console.error('格式化消息出错:', e);
            return escapeHtml(text).replace(/\n/g, '<br>');
        }
    }
    
    // 筛选按钮点击
    $('#smartsell-chat-filter').on('click', function() {
        loadChats(1);
    });
    
    // 搜索框回车
    $('#smartsell-chat-search').on('keypress', function(e) {
        if (e.which === 13) {
            loadChats(1);
        }
    });
    
    // 分页点击
    $(document).on('click', '.smartsell-pagination[data-type="chat"] a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadChats(page);
    });
    
    // 会话项点击
    $(document).on('click', '.smartsell-chat-list-item', function() {
        var chatId = $(this).data('id');
        currentChatId = chatId;
        
        // 更新选中状态
        $('.smartsell-chat-list-item').removeClass('active');
        $(this).addClass('active');
        
        // 清除该会话的未读徽章
        $(this).find('.smartsell-unread-badge').remove();
        
        // 加载聊天详情
        loadChatDetail(chatId);
    });
    
    // 发送按钮点击
    $(document).on('click', '#smartsell-chat-send', function() {
        sendMessage();
    });
    
    // 输入框回车发送
    $(document).on('keypress', '#smartsell-chat-input', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // AI开关切换
    $(document).on('change', '#smartsell-ai-stop', function() {
        toggleAiStop($(this).is(':checked'));
    });
    
    // 提取线索按钮点击
    $(document).on('click', '#smartsell-extract-inquiry', function() {
        if (!currentChatId) return;
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('提取中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/chat/ana_all_input?chat_id=' + currentChatId,
                method: 'GET'
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    var data = response.data.data || {};
                    showInquiryModal(data);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('提取失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('提取失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('提取线索', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 显示提取线索模态框
    function showInquiryModal(data) {
        // 清除错误状态
        $('.smartsell-input, .smartsell-textarea').removeClass('smartsell-input-error');
        $('.smartsell-form-error').remove();
        
        // API返回字段: contact_name, contact_info, inquiry_info, country, region, remark
        $('#inquiry-contact-name').val(data.contact_name || '');
        $('#inquiry-contact-info').val(data.contact_info || '');
        $('#inquiry-inquiry-info').val(data.inquiry_info || '');
        $('#inquiry-country').val(data.country || '');
        $('#inquiry-region').val(data.region || '');
        $('#inquiry-remark').val(data.remark || '');
        $('#smartsell-inquiry-modal').show();
    }
    
    // 关闭提取线索模态框
    $(document).on('click', '#smartsell-inquiry-modal .smartsell-modal-close, #smartsell-inquiry-cancel', function() {
        $('#smartsell-inquiry-modal').hide();
    });
    
    // 保存线索
    $(document).on('click', '#smartsell-inquiry-save', function() {
        var $btn = $(this);
        
        // 表单验证
        var inquiryInfo = $('#inquiry-inquiry-info').val().trim();
        
        // 清除之前的错误样式
        $('.smartsell-input, .smartsell-textarea').removeClass('smartsell-input-error');
        $('.smartsell-form-error').remove();
        
        // 验证必填字段
        var hasError = false;
        if (!inquiryInfo) {
            $('#inquiry-inquiry-info').addClass('smartsell-input-error');
            $('#inquiry-inquiry-info').closest('.smartsell-form-group').append('<span class="smartsell-form-error"><?php esc_html_e('线索信息不能为空', 'smartsell-assistant'); ?></span>');
            hasError = true;
        }
        
        if (hasError) {
            return;
        }
        
        var formData = {
            chat_id: currentChatId,
            contact_name: $('#inquiry-contact-name').val().trim(),
            contact_info: $('#inquiry-contact-info').val().trim(),
            inquiry_info: inquiryInfo,
            country: $('#inquiry-country').val().trim(),
            region: $('#inquiry-region').val().trim(),
            remark: $('#inquiry-remark').val().trim()
        };
        
        $btn.prop('disabled', true).text('<?php esc_html_e('保存中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/add',
                method: 'POST',
                contentType: 'form',
                data: formData
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('线索已保存', 'smartsell-assistant'); ?>');
                    $('#smartsell-inquiry-modal').hide();
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('保存失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('保存失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('保存线索', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 输入框输入时清除错误状态
    $(document).on('input', '#inquiry-inquiry-info', function() {
        $(this).removeClass('smartsell-input-error');
        $(this).closest('.smartsell-form-group').find('.smartsell-form-error').remove();
    });
    
    // 定时刷新会话列表以更新在线状态（参考前端实现，缩短刷新间隔以提高实时性）
    var chatListRefreshTimer = setInterval(function() {
        // 静默刷新会话列表（不显示加载动画）
        loadChats(currentPage, true);
    }, 10000); // 每10秒刷新一次（从30秒改为10秒，提高实时性）
    
    // 定时检查当前会话连接状态
    var statusCheckTimer = setInterval(function() {
        if (currentSessionId) {
            checkConnectionStatus();
        }
    }, 5000); // 每5秒检查一次（从10秒改为5秒，提高实时性）
    
    // 页面离开时关闭 WebSocket 和清理定时器
    $(window).on('beforeunload', function() {
        stopHeartbeat();
        if (ws) {
            ws.close(1000, '页面关闭');
        }
        if (chatListRefreshTimer) {
            clearInterval(chatListRefreshTimer);
        }
        if (statusCheckTimer) {
            clearInterval(statusCheckTimer);
        }
    });
    
    // 初始化
    loadChats(1);
    initWebSocket();
});
</script>

<!-- 提取线索模态框 -->
<div id="smartsell-inquiry-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content smartsell-modal-lg">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('提取线索', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <form id="smartsell-inquiry-form">
                <div class="smartsell-form-row">
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('联系人', 'smartsell-assistant'); ?></label>
                        <input type="text" id="inquiry-contact-name" class="smartsell-input">
                    </div>
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('联系方式', 'smartsell-assistant'); ?></label>
                        <input type="text" id="inquiry-contact-info" class="smartsell-input">
                    </div>
                </div>
                <div class="smartsell-form-row">
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('国家', 'smartsell-assistant'); ?></label>
                        <input type="text" id="inquiry-country" class="smartsell-input">
                    </div>
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('地区', 'smartsell-assistant'); ?></label>
                        <input type="text" id="inquiry-region" class="smartsell-input">
                    </div>
                </div>
                <div class="smartsell-form-group smartsell-form-group-full">
                    <label><?php esc_html_e('线索信息', 'smartsell-assistant'); ?><span class="smartsell-required">*</span></label>
                    <textarea id="inquiry-inquiry-info" class="smartsell-textarea" rows="3" required></textarea>
                </div>
                <div class="smartsell-form-group smartsell-form-group-full">
                    <label><?php esc_html_e('备注', 'smartsell-assistant'); ?></label>
                    <textarea id="inquiry-remark" class="smartsell-textarea" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-inquiry-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-inquiry-save"><?php esc_html_e('保存线索', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>
