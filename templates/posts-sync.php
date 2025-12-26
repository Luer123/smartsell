<?php
/**
 * 文章同步模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('文章同步', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('将WordPress文章同步到AI知识库', 'smartsell-assistant'); ?></p>
    </div>
    
    <div class="smartsell-card">
        <div class="smartsell-card-header">
            <h3><?php esc_html_e('文章列表', 'smartsell-assistant'); ?></h3>
            <div class="smartsell-card-actions">
                <button type="button" id="smartsell-select-all-posts" class="smartsell-btn smartsell-btn-secondary">
                    <?php esc_html_e('全选', 'smartsell-assistant'); ?>
                </button>
                <button type="button" id="smartsell-sync-selected-posts" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('同步选中文章', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 筛选区域 -->
        <div class="smartsell-filters">
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('文章分类', 'smartsell-assistant'); ?></label>
                <select id="smartsell-post-category" class="smartsell-form-select" style="width: 150px;">
                    <option value=""><?php esc_html_e('全部分类', 'smartsell-assistant'); ?></option>
                    <?php
                    $categories = get_categories(array('hide_empty' => false));
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('关键词', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-post-keyword" class="smartsell-form-input" style="width: 200px;" placeholder="<?php esc_attr_e('搜索文章标题', 'smartsell-assistant'); ?>">
            </div>
            <div class="smartsell-filter-item">
                <button type="button" id="smartsell-post-filter" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('筛选', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 表格 -->
        <table class="smartsell-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="smartsell-check-all-posts">
                    </th>
                    <th><?php esc_html_e('ID', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('标题', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('分类', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('作者', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('发布时间', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('同步状态', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('操作', 'smartsell-assistant'); ?></th>
                </tr>
            </thead>
            <tbody id="smartsell-posts-list">
                <tr>
                    <td colspan="8" class="smartsell-loading">
                        <div class="smartsell-spinner"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <div class="smartsell-pagination" data-type="posts"></div>
    </div>
    
    <!-- 同步进度弹窗 -->
    <div id="smartsell-sync-modal" class="smartsell-modal" style="display: none;">
        <div class="smartsell-modal-content">
            <div class="smartsell-modal-header">
                <h3><?php esc_html_e('同步进度', 'smartsell-assistant'); ?></h3>
            </div>
            <div class="smartsell-modal-body">
                <div class="smartsell-progress-bar">
                    <div class="smartsell-progress-fill" id="smartsell-sync-progress" style="width: 0%;"></div>
                </div>
                <div class="smartsell-progress-text">
                    <span id="smartsell-sync-current">0</span> / <span id="smartsell-sync-total">0</span>
                </div>
                <div id="smartsell-sync-status" class="smartsell-sync-status"></div>
            </div>
        </div>
    </div>
</div>

<style>
.smartsell-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e0e0e0;
}

.smartsell-card-header h3 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.smartsell-card-actions {
    display: flex;
    gap: 8px;
}

.smartsell-sync-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.smartsell-sync-badge.synced {
    background: #e6f7e6;
    color: #52c41a;
}

.smartsell-sync-badge.not-synced {
    background: #f0f0f0;
    color: #999;
}

.smartsell-progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.smartsell-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4a90d9, #357abd);
    transition: width 0.3s;
}

.smartsell-progress-text {
    text-align: center;
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.smartsell-sync-status {
    max-height: 200px;
    overflow-y: auto;
    font-size: 12px;
    color: #666;
}

.smartsell-sync-status .sync-item {
    padding: 4px 0;
    border-bottom: 1px dashed #e0e0e0;
}

.smartsell-sync-status .sync-item.success {
    color: #52c41a;
}

.smartsell-sync-status .sync-item.error {
    color: #ff4d4f;
}

.smartsell-row-synced {
    opacity: 0.7;
    background-color: #f9f9f9;
}

.smartsell-row-synced input[type="checkbox"]:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

.smartsell-btn-disabled {
    background-color: #d9d9d9 !important;
    color: #999 !important;
    cursor: not-allowed !important;
    border-color: #d9d9d9 !important;
}

.smartsell-btn-disabled:hover {
    background-color: #d9d9d9 !important;
    color: #999 !important;
}
</style>

<script>
jQuery(document).ready(function($) {
    var currentPage = 1;
    var syncedPosts = [];
    
    // 加载文章列表
    function loadPosts(page) {
        currentPage = page;
        var category = $('#smartsell-post-category').val();
        var keyword = $('#smartsell-post-keyword').val();
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_load_posts',
                nonce: smartsellAdmin.nonce,
                page: page,
                category: category,
                keyword: keyword
            },
            beforeSend: function() {
                $('#smartsell-posts-list').html('<tr><td colspan="8" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    renderPosts(response.data);
                } else {
                    $('#smartsell-posts-list').html('<tr><td colspan="8" class="smartsell-empty"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></td></tr>');
                }
            }
        });
    }
    
    // 渲染文章列表
    function renderPosts(data) {
        var html = '';
        
        if (!data.posts || data.posts.length === 0) {
            html = '<tr><td colspan="8" class="smartsell-empty"><div class="smartsell-empty-icon">📝</div><div class="smartsell-empty-text"><?php esc_html_e('暂无文章', 'smartsell-assistant'); ?></div></td></tr>';
        } else {
            $.each(data.posts, function(i, post) {
                // 使用后端返回的 synced 状态
                var isSynced = post.synced === true;
                var syncBadge = isSynced ? 
                    '<span class="smartsell-sync-badge synced"><?php esc_html_e('已同步', 'smartsell-assistant'); ?></span>' : 
                    '<span class="smartsell-sync-badge not-synced"><?php esc_html_e('未同步', 'smartsell-assistant'); ?></span>';
                
                // 如果已同步，禁用复选框和按钮
                var checkboxDisabled = isSynced ? 'disabled' : '';
                var buttonDisabled = isSynced ? 'disabled' : '';
                var buttonClass = isSynced ? 'smartsell-btn smartsell-btn-sm smartsell-btn-disabled' : 'smartsell-btn smartsell-btn-sm smartsell-sync-single';
                var buttonText = isSynced ? '<?php esc_html_e('已同步', 'smartsell-assistant'); ?>' : '<?php esc_html_e('同步', 'smartsell-assistant'); ?>';
                
                html += '<tr' + (isSynced ? ' class="smartsell-row-synced"' : '') + '>';
                html += '<td><input type="checkbox" class="smartsell-post-check" data-id="' + post.ID + '" ' + checkboxDisabled + '></td>';
                html += '<td>' + post.ID + '</td>';
                html += '<td><a href="' + post.edit_link + '" target="_blank">' + post.title + '</a></td>';
                html += '<td>' + post.categories + '</td>';
                html += '<td>' + post.author + '</td>';
                html += '<td>' + post.date + '</td>';
                html += '<td>' + syncBadge + '</td>';
                html += '<td>';
                html += '<button type="button" class="' + buttonClass + '" data-id="' + post.ID + '" ' + buttonDisabled + '>' + buttonText + '</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#smartsell-posts-list').html(html);
        
        // 渲染分页
        if (data.total_pages > 1) {
            renderPagination('.smartsell-pagination[data-type="posts"]', currentPage, data.total_pages);
        } else {
            $('.smartsell-pagination[data-type="posts"]').html('');
        }
    }
    
    // 渲染分页
    function renderPagination(container, current, total) {
        var html = '';
        
        if (current > 1) {
            html += '<button class="smartsell-page-btn" data-page="' + (current - 1) + '">&laquo;</button>';
        }
        
        for (var i = 1; i <= total; i++) {
            if (i === current) {
                html += '<span class="smartsell-page-btn active">' + i + '</span>';
            } else if (i === 1 || i === total || (i >= current - 2 && i <= current + 2)) {
                html += '<button class="smartsell-page-btn" data-page="' + i + '">' + i + '</button>';
            } else if (i === current - 3 || i === current + 3) {
                html += '<span class="smartsell-page-dots">...</span>';
            }
        }
        
        if (current < total) {
            html += '<button class="smartsell-page-btn" data-page="' + (current + 1) + '">&raquo;</button>';
        }
        
        $(container).html(html);
    }
    
    // 分页点击
    $(document).on('click', '.smartsell-pagination[data-type="posts"] .smartsell-page-btn', function() {
        var page = $(this).data('page');
        if (page) {
            loadPosts(page);
        }
    });
    
    // 全选/取消全选（只选中未禁用的复选框）
    $('#smartsell-check-all-posts, #smartsell-select-all-posts').on('click', function() {
        var isChecked = $(this).is(':checked') || $(this).is('button');
        if ($(this).is('button')) {
            isChecked = !$('#smartsell-check-all-posts').prop('checked');
        }
        $('#smartsell-check-all-posts').prop('checked', isChecked);
        // 只选中未禁用的复选框
        $('.smartsell-post-check:not(:disabled)').prop('checked', isChecked);
    });
    
    // 筛选按钮
    $('#smartsell-post-filter').on('click', function() {
        loadPosts(1);
    });
    
    // 单个同步（只处理未禁用的按钮）
    $(document).on('click', '.smartsell-sync-single', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        var postId = $(this).data('id');
        syncPosts([postId]);
    });
    
    // 批量同步（只同步未禁用的复选框）
    $('#smartsell-sync-selected-posts').on('click', function() {
        var selectedIds = [];
        $('.smartsell-post-check:checked:not(:disabled)').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            alert('<?php esc_html_e('请选择要同步的文章', 'smartsell-assistant'); ?>');
            return;
        }
        
        syncPosts(selectedIds);
    });
    
    // 同步文章
    function syncPosts(postIds) {
        $('#smartsell-sync-modal').show();
        $('#smartsell-sync-total').text(postIds.length);
        $('#smartsell-sync-current').text(0);
        $('#smartsell-sync-progress').css('width', '0%');
        $('#smartsell-sync-status').html('');
        
        var completed = 0;
        
        function syncNext() {
            if (completed >= postIds.length) {
                setTimeout(function() {
                    $('#smartsell-sync-modal').hide();
                    loadPosts(currentPage);
                }, 1000);
                return;
            }
            
            var postId = postIds[completed];
            
            $.ajax({
                url: smartsellAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smartsell_sync_posts',
                    nonce: smartsellAdmin.nonce,
                    post_ids: [postId]
                },
                success: function(response) {
                    completed++;
                    var progress = (completed / postIds.length * 100).toFixed(0);
                    $('#smartsell-sync-current').text(completed);
                    $('#smartsell-sync-progress').css('width', progress + '%');
                    
                    if (response.success) {
                        syncedPosts.push(postId);
                        $('#smartsell-sync-status').prepend('<div class="sync-item success">✓ <?php esc_html_e('文章ID', 'smartsell-assistant'); ?> ' + postId + ' <?php esc_html_e('同步成功', 'smartsell-assistant'); ?></div>');
                    } else {
                        $('#smartsell-sync-status').prepend('<div class="sync-item error">✗ <?php esc_html_e('文章ID', 'smartsell-assistant'); ?> ' + postId + ' <?php esc_html_e('同步失败', 'smartsell-assistant'); ?></div>');
                    }
                    
                    syncNext();
                },
                error: function() {
                    completed++;
                    $('#smartsell-sync-status').prepend('<div class="sync-item error">✗ <?php esc_html_e('文章ID', 'smartsell-assistant'); ?> ' + postId + ' <?php esc_html_e('同步失败', 'smartsell-assistant'); ?></div>');
                    syncNext();
                }
            });
        }
        
        syncNext();
    }
    
    // 初始加载
    loadPosts(1);
});
</script>
