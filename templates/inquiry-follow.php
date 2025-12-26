<?php
/**
 * 线索跟进日志模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('线索跟进日志', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('查看所有线索的跟进记录', 'smartsell-assistant'); ?></p>
    </div>
    
    <div class="smartsell-card">
        <!-- 筛选区域 -->
        <div class="smartsell-filters">
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('跟进人', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-follow-person" class="smartsell-form-input" style="width: 150px;">
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('开始日期', 'smartsell-assistant'); ?></label>
                <input type="date" id="smartsell-follow-start-date" class="smartsell-form-input" style="width: 150px;">
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('结束日期', 'smartsell-assistant'); ?></label>
                <input type="date" id="smartsell-follow-end-date" class="smartsell-form-input" style="width: 150px;">
            </div>
            <div class="smartsell-filter-item">
                <button type="button" id="smartsell-follow-filter" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('筛选', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 表格 -->
        <table class="smartsell-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('线索信息', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('联系人', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('跟进人', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('跟进方式', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('跟进内容', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('跟进时间', 'smartsell-assistant'); ?></th>
                </tr>
            </thead>
            <tbody id="smartsell-follow-list">
                <tr>
                    <td colspan="6" class="smartsell-loading">
                        <div class="smartsell-spinner"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <div class="smartsell-pagination" data-type="inquiry-follow"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 加载跟进日志列表
    function loadFollowLogs(page) {
        var followPerson = $('#smartsell-follow-person').val();
        var startDate = $('#smartsell-follow-start-date').val();
        var endDate = $('#smartsell-follow-end-date').val();
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry_follow/log',
                method: 'GET',
                data: {
                    page: page,
                    page_size: 20,
                    follow_person: followPerson,
                    start_date: startDate,
                    end_date: endDate
                }
            },
            beforeSend: function() {
                $('#smartsell-follow-list').html('<tr><td colspan="6" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderFollowLogs(response.data.data);
                } else {
                    $('#smartsell-follow-list').html('<tr><td colspan="6" class="smartsell-empty"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></td></tr>');
                }
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
    
    // 渲染跟进日志列表
    function renderFollowLogs(data) {
        var html = '';
        
        if (!data.items || data.items.length === 0) {
            html = '<tr><td colspan="6" class="smartsell-empty"><div class="smartsell-empty-icon">📋</div><div class="smartsell-empty-text"><?php esc_html_e('暂无跟进记录', 'smartsell-assistant'); ?></div></td></tr>';
        } else {
            $.each(data.items, function(i, log) {
                // 处理线索信息
                var inquiryInfo = log.inquiry_info || '-';
                var inquiryInfoDisplay = inquiryInfo;
                var inquiryInfoTitle = '';
                
                if (inquiryInfo !== '-' && inquiryInfo.length > 30) {
                    inquiryInfoDisplay = inquiryInfo.substring(0, 30) + '...';
                    inquiryInfoTitle = ' title="' + escapeHtml(inquiryInfo) + '"';
                }
                
                // 处理跟进内容
                var followInfo = log.follow_info || '-';
                var followInfoDisplay = followInfo;
                var followInfoTitle = '';
                
                if (followInfo !== '-' && followInfo.length > 50) {
                    followInfoDisplay = followInfo.substring(0, 50) + '...';
                    followInfoTitle = ' title="' + escapeHtml(followInfo) + '"';
                }
                
                html += '<tr>';
                html += '<td class="smartsell-follow-info-cell"' + inquiryInfoTitle + '>' + escapeHtml(inquiryInfoDisplay) + '</td>';
                html += '<td>' + escapeHtml(log.contact_name || '-') + '</td>';
                html += '<td>' + escapeHtml(log.follow_person || '-') + '</td>';
                html += '<td>' + escapeHtml(log.follow_method || '-') + '</td>';
                html += '<td class="smartsell-follow-info-cell"' + followInfoTitle + '>' + escapeHtml(followInfoDisplay) + '</td>';
                html += '<td>' + (log.create_time || '-') + '</td>';
                html += '</tr>';
            });
        }
        
        $('#smartsell-follow-list').html(html);
    }
    
    // 筛选按钮点击
    $('#smartsell-follow-filter').on('click', function() {
        loadFollowLogs(1);
    });
    
    // 初始加载
    loadFollowLogs(1);
});
</script>

<style>
/* 跟进内容单元格样式 */
.smartsell-follow-info-cell {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: help;
    position: relative;
}

/* 当内容被截断时，鼠标悬浮显示完整内容 */
.smartsell-follow-info-cell[title] {
    position: relative;
}

.smartsell-follow-info-cell[title]:hover::after {
    content: attr(title);
    position: absolute;
    left: 0;
    top: 100%;
    background: #333;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    white-space: normal;
    word-break: break-word;
    max-width: 400px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    font-size: 13px;
    line-height: 1.5;
    margin-top: 5px;
    pointer-events: none;
}

.smartsell-follow-info-cell[title]:hover::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 100%;
    border: 5px solid transparent;
    border-bottom-color: #333;
    z-index: 1001;
    margin-top: -1px;
}
</style>
