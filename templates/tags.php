<?php
/**
 * 标签管理模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('标签管理', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('管理客户和线索的标签', 'smartsell-assistant'); ?></p>
    </div>
    
    <div class="smartsell-card">
        <!-- 筛选区域 -->
        <div class="smartsell-filters">
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('搜索', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-tags-search" class="smartsell-form-input" placeholder="<?php esc_attr_e('搜索标签名称...', 'smartsell-assistant'); ?>" style="width: 200px;">
            </div>
            <div class="smartsell-filter-item">
                <button type="button" id="smartsell-tags-filter" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('搜索', 'smartsell-assistant'); ?>
                </button>
            </div>
            <div class="smartsell-filter-item" style="margin-left: auto;">
                <button type="button" id="smartsell-tags-add" class="smartsell-btn smartsell-btn-success">
                    + <?php esc_html_e('新增标签', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 表格 -->
        <table class="smartsell-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('标签名称', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('状态', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('创建时间', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('更新时间', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('操作', 'smartsell-assistant'); ?></th>
                </tr>
            </thead>
            <tbody id="smartsell-tags-list">
                <tr>
                    <td colspan="6" class="smartsell-loading">
                        <div class="smartsell-spinner"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <div class="smartsell-pagination" data-type="tags"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 加载标签列表
    function loadTags(page) {
        page = page || 1;
        var search = $('#smartsell-tags-search').val();

        var requestData = {
            page: page,
            page_size: 10
        };

        if (search) {
            requestData.search_name = search;
        }

        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/tags/list',
                method: 'GET',
                data: requestData
            },
            beforeSend: function() {
                $('#smartsell-tags-list').html('<tr><td colspan="6" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderTags(response.data.data);
                } else {
                    $('#smartsell-tags-list').html('<tr><td colspan="6" class="smartsell-empty"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></td></tr>');
                    $('.smartsell-pagination[data-type="tags"]').html('');
                }
            }
        });
    }
    
    // 渲染标签列表
    function renderTags(data) {
        var html = '';
        var statusMap = {
            1: {text: '<?php esc_html_e('已上架', 'smartsell-assistant'); ?>', class: 'new'},
            0: {text: '<?php esc_html_e('已下架', 'smartsell-assistant'); ?>', class: 'invalid'}
        };
        
        if (!data.items || data.items.length === 0) {
            html = '<tr><td colspan="6" class="smartsell-empty"><div class="smartsell-empty-icon">🏷️</div><div class="smartsell-empty-text"><?php esc_html_e('暂无标签', 'smartsell-assistant'); ?></div></td></tr>';
        } else {
            $.each(data.items, function(i, tag) {
                var status = statusMap[tag.status] || {text: '-', class: ''};
                
                html += '<tr>';
                html += '<td>' + tag.id + '</td>';
                html += '<td>' + escapeHtml(tag.name || '-') + '</td>';
                html += '<td><span class="smartsell-status ' + status.class + '">' + status.text + '</span></td>';
                html += '<td>' + (tag.create_time || '-') + '</td>';
                html += '<td>' + (tag.update_time || '-') + '</td>';
                html += '<td class="smartsell-actions-cell">';
                html += '<button type="button" class="smartsell-btn smartsell-btn-sm smartsell-btn-primary smartsell-edit-tag" data-id="' + tag.id + '" data-name="' + escapeHtml(tag.name) + '" data-status="' + tag.status + '"><?php esc_html_e('编辑', 'smartsell-assistant'); ?></button> ';
                html += '<button type="button" class="smartsell-btn smartsell-btn-sm ' + (tag.status === 1 ? 'smartsell-btn-warning' : 'smartsell-btn-success') + ' smartsell-toggle-tag" data-id="' + tag.id + '" data-status="' + tag.status + '">' + (tag.status === 1 ? '<?php esc_html_e('下架', 'smartsell-assistant'); ?>' : '<?php esc_html_e('上架', 'smartsell-assistant'); ?>') + '</button> ';
                html += '<button type="button" class="smartsell-btn smartsell-btn-sm smartsell-btn-danger smartsell-delete-tag" data-id="' + tag.id + '"><?php esc_html_e('删除', 'smartsell-assistant'); ?></button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#smartsell-tags-list').html(html);
        // 渲染分页
        if (data && typeof data.total !== 'undefined') {
            renderPagination(data.total, data.total_pages, data.page);
        }
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

        $('.smartsell-pagination[data-type="tags"]').html(html);
    }
    
    // HTML转义
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // 分页点击
    $(document).on('click', '.smartsell-pagination[data-type="tags"] a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            loadTags(page);
        }
    });
    
    // 筛选按钮点击
    $('#smartsell-tags-filter').on('click', function() {
        loadTags(1);
    });
    
    // 当前编辑的标签ID
    var currentTagId = null;
    var isAddMode = false;
    
    // 新增标签按钮点击
    $('#smartsell-tags-add').on('click', function() {
        isAddMode = true;
        currentTagId = null;
        $('#edit-tag-name').val('');
        $('#edit-tag-status').val('1');
        $('#smartsell-tag-modal .smartsell-modal-header h3').text('<?php esc_html_e('新增标签', 'smartsell-assistant'); ?>');
        $('#smartsell-tag-modal').show();
    });
    
    // 编辑标签按钮点击
    $(document).on('click', '.smartsell-edit-tag', function() {
        isAddMode = false;
        currentTagId = $(this).data('id');
        var name = $(this).data('name');
        var status = $(this).data('status');
        
        $('#edit-tag-name').val(name);
        $('#edit-tag-status').val(status);
        $('#smartsell-tag-modal .smartsell-modal-header h3').text('<?php esc_html_e('编辑标签', 'smartsell-assistant'); ?>');
        $('#smartsell-tag-modal').show();
    });
    
    // 保存标签
    $(document).on('click', '#smartsell-tag-save', function() {
        var name = $('#edit-tag-name').val().trim();
        var status = $('#edit-tag-status').val();
        
        if (!name) {
            alert('<?php esc_html_e('请输入标签名称', 'smartsell-assistant'); ?>');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('保存中...', 'smartsell-assistant'); ?>');
        
        var endpoint = isAddMode ? '/tags/add' : '/tags/update';
        var requestData = {
            name: name,
            status: parseInt(status)
        };
        
        if (!isAddMode) {
            requestData.id = currentTagId;
        }
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: endpoint,
                method: 'POST',
                contentType: 'form',
                data: requestData
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert(isAddMode ? '<?php esc_html_e('新增成功', 'smartsell-assistant'); ?>' : '<?php esc_html_e('保存成功', 'smartsell-assistant'); ?>');
                    $('#smartsell-tag-modal').hide();
                    loadTags(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('保存失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('保存失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('保存', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 上下架切换
    $(document).on('click', '.smartsell-toggle-tag', function() {
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        var newStatus = currentStatus === 1 ? 0 : 1;
        var confirmMsg = currentStatus === 1 ? '<?php esc_html_e('确定要下架此标签吗？', 'smartsell-assistant'); ?>' : '<?php esc_html_e('确定要上架此标签吗？', 'smartsell-assistant'); ?>';
        
        if (!confirm(confirmMsg)) {
            return;
        }
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/tags/status',
                method: 'POST',
                contentType: 'form',
                data: {
                    id: id,
                    status: newStatus
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('操作成功', 'smartsell-assistant'); ?>');
                    loadTags(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('操作失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('操作失败，请检查网络', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 删除标签
    $(document).on('click', '.smartsell-delete-tag', function() {
        var id = $(this).data('id');
        
        if (!confirm('<?php esc_html_e('确定要删除此标签吗？删除后不可恢复', 'smartsell-assistant'); ?>')) {
            return;
        }
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/tags/delete',
                method: 'POST',
                contentType: 'form',
                data: {
                    id: id
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('删除成功', 'smartsell-assistant'); ?>');
                    loadTags(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('删除失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('删除失败，请检查网络', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 关闭模态框
    $(document).on('click', '#smartsell-tag-modal .smartsell-modal-close, #smartsell-tag-cancel', function() {
        $('#smartsell-tag-modal').hide();
    });
    
    // 初始加载
    loadTags(1);
});
</script>

<!-- 样式 -->
<style>
.smartsell-actions-cell { position: relative; }
.smartsell-btn-success {
    background-color: #52c41a;
    border-color: #52c41a;
    color: #fff;
}
.smartsell-btn-success:hover {
    background-color: #73d13d;
    border-color: #73d13d;
}
.smartsell-btn-warning {
    background-color: #faad14;
    border-color: #faad14;
    color: #fff;
}
.smartsell-btn-warning:hover {
    background-color: #ffc53d;
    border-color: #ffc53d;
}
.smartsell-btn-danger {
    background-color: #ff4d4f;
    border-color: #ff4d4f;
    color: #fff;
}
.smartsell-btn-danger:hover {
    background-color: #ff7875;
    border-color: #ff7875;
}
</style>

<!-- 新增/编辑标签模态框 -->
<div id="smartsell-tag-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content smartsell-modal-sm">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('新增标签', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <form id="smartsell-tag-form">
                <div class="smartsell-form-group">
                    <label><?php esc_html_e('标签名称', 'smartsell-assistant'); ?> <span style="color: #ff4d4f;">*</span></label>
                    <input type="text" id="edit-tag-name" class="smartsell-input" placeholder="<?php esc_attr_e('请输入标签名称', 'smartsell-assistant'); ?>">
                </div>
                <div class="smartsell-form-group">
                    <label><?php esc_html_e('状态', 'smartsell-assistant'); ?></label>
                    <select id="edit-tag-status" class="smartsell-filter-select" style="width: 100%;">
                        <option value="1"><?php esc_html_e('已上架', 'smartsell-assistant'); ?></option>
                        <option value="0"><?php esc_html_e('已下架', 'smartsell-assistant'); ?></option>
                    </select>
                </div>
            </form>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-tag-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-tag-save"><?php esc_html_e('保存', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>
