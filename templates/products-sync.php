<?php
/**
 * 商品同步模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('商品同步', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('将WooCommerce商品同步到AI知识库', 'smartsell-assistant'); ?></p>
    </div>
    
    <?php if (!class_exists('WooCommerce')): ?>
    <div class="smartsell-card">
        <div class="smartsell-notice smartsell-notice-warning">
            <div class="smartsell-notice-icon">⚠️</div>
            <div class="smartsell-notice-content">
                <h4><?php esc_html_e('WooCommerce未安装', 'smartsell-assistant'); ?></h4>
                <p><?php esc_html_e('商品同步功能需要安装并激活WooCommerce插件。', 'smartsell-assistant'); ?></p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="smartsell-card">
        <div class="smartsell-card-header">
            <h3><?php esc_html_e('商品列表', 'smartsell-assistant'); ?></h3>
            <div class="smartsell-card-actions">
                <button type="button" id="smartsell-select-all-products" class="smartsell-btn smartsell-btn-secondary">
                    <?php esc_html_e('全选', 'smartsell-assistant'); ?>
                </button>
                <button type="button" id="smartsell-sync-selected-products" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('同步选中商品', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 筛选区域 -->
        <div class="smartsell-filters">
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('商品分类', 'smartsell-assistant'); ?></label>
                <select id="smartsell-product-category" class="smartsell-form-select" style="width: 150px;">
                    <option value=""><?php esc_html_e('全部分类', 'smartsell-assistant'); ?></option>
                    <?php
                    $product_categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false
                    ));
                    if (!is_wp_error($product_categories)) {
                        foreach ($product_categories as $category) {
                            echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('商品状态', 'smartsell-assistant'); ?></label>
                <select id="smartsell-product-status" class="smartsell-form-select" style="width: 120px;">
                    <option value=""><?php esc_html_e('全部状态', 'smartsell-assistant'); ?></option>
                    <option value="publish"><?php esc_html_e('已发布', 'smartsell-assistant'); ?></option>
                    <option value="draft"><?php esc_html_e('草稿', 'smartsell-assistant'); ?></option>
                </select>
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('关键词', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-product-keyword" class="smartsell-form-input" style="width: 200px;" placeholder="<?php esc_attr_e('搜索商品名称', 'smartsell-assistant'); ?>">
            </div>
            <div class="smartsell-filter-item">
                <button type="button" id="smartsell-product-filter" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('筛选', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 表格 -->
        <table class="smartsell-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="smartsell-check-all-products">
                    </th>
                    <th style="width: 60px;"><?php esc_html_e('图片', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('ID', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('商品名称', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('SKU', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('价格', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('分类', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('状态', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('同步状态', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('操作', 'smartsell-assistant'); ?></th>
                </tr>
            </thead>
            <tbody id="smartsell-products-list">
                <tr>
                    <td colspan="10" class="smartsell-loading">
                        <div class="smartsell-spinner"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <div class="smartsell-pagination" data-type="products"></div>
    </div>
    
    <!-- 同步进度弹窗 -->
    <div id="smartsell-sync-products-modal" class="smartsell-modal" style="display: none;">
        <div class="smartsell-modal-content">
            <div class="smartsell-modal-header">
                <h3><?php esc_html_e('同步进度', 'smartsell-assistant'); ?></h3>
            </div>
            <div class="smartsell-modal-body">
                <div class="smartsell-progress-bar">
                    <div class="smartsell-progress-fill" id="smartsell-sync-products-progress" style="width: 0%;"></div>
                </div>
                <div class="smartsell-progress-text">
                    <span id="smartsell-sync-products-current">0</span> / <span id="smartsell-sync-products-total">0</span>
                </div>
                <div id="smartsell-sync-products-status" class="smartsell-sync-status"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>
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

.smartsell-notice {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    border-radius: 8px;
}

.smartsell-notice-warning {
    background: #fff7e6;
    border: 1px solid #ffd591;
}

.smartsell-notice-icon {
    font-size: 24px;
    margin-right: 12px;
}

.smartsell-notice-content h4 {
    margin: 0 0 8px 0;
    color: #d46b08;
}

.smartsell-notice-content p {
    margin: 0;
    color: #d46b08;
}

.smartsell-product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    background: #f5f5f5;
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

.smartsell-status-badge.publish {
    background: #e6f7e6;
    color: #52c41a;
}

.smartsell-status-badge.draft {
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
    var syncedProducts = [];
    
    // 加载商品列表
    function loadProducts(page) {
        currentPage = page;
        var category = $('#smartsell-product-category').val();
        var status = $('#smartsell-product-status').val();
        var keyword = $('#smartsell-product-keyword').val();
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_load_products',
                nonce: smartsellAdmin.nonce,
                page: page,
                category: category,
                status: status,
                keyword: keyword
            },
            beforeSend: function() {
                $('#smartsell-products-list').html('<tr><td colspan="10" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    renderProducts(response.data);
                } else {
                    $('#smartsell-products-list').html('<tr><td colspan="10" class="smartsell-empty"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></td></tr>');
                }
            }
        });
    }
    
    // 渲染商品列表
    function renderProducts(data) {
        var html = '';
        
        if (!data.products || data.products.length === 0) {
            html = '<tr><td colspan="10" class="smartsell-empty"><div class="smartsell-empty-icon">📦</div><div class="smartsell-empty-text"><?php esc_html_e('暂无商品', 'smartsell-assistant'); ?></div></td></tr>';
        } else {
            $.each(data.products, function(i, product) {
                // 主要依赖后端返回的同步状态，syncedProducts 仅用于临时显示刚同步成功的商品
                var isSynced = product.synced === true || syncedProducts.indexOf(product.ID) > -1;
                var syncBadge = isSynced ? 
                    '<span class="smartsell-sync-badge synced"><?php esc_html_e('已同步', 'smartsell-assistant'); ?></span>' : 
                    '<span class="smartsell-sync-badge not-synced"><?php esc_html_e('未同步', 'smartsell-assistant'); ?></span>';
                
                var statusBadge = product.status === 'publish' ? 
                    '<span class="smartsell-status-badge publish"><?php esc_html_e('已发布', 'smartsell-assistant'); ?></span>' : 
                    '<span class="smartsell-status-badge draft"><?php esc_html_e('草稿', 'smartsell-assistant'); ?></span>';
                
                // 如果已同步，禁用复选框和按钮
                var checkboxDisabled = isSynced ? 'disabled' : '';
                var buttonDisabled = isSynced ? 'disabled' : '';
                var buttonClass = isSynced ? 'smartsell-btn smartsell-btn-sm smartsell-btn-disabled' : 'smartsell-btn smartsell-btn-sm smartsell-sync-product-single';
                var buttonText = isSynced ? '<?php esc_html_e('已同步', 'smartsell-assistant'); ?>' : '<?php esc_html_e('同步', 'smartsell-assistant'); ?>';
                
                html += '<tr' + (isSynced ? ' class="smartsell-row-synced"' : '') + '>';
                html += '<td><input type="checkbox" class="smartsell-product-check" data-id="' + product.ID + '" ' + checkboxDisabled + '></td>';
                html += '<td><img src="' + (product.image || '<?php echo esc_url(plugins_url('assets/images/placeholder.png', dirname(__FILE__))); ?>') + '" class="smartsell-product-image"></td>';
                html += '<td>' + product.ID + '</td>';
                html += '<td><a href="' + product.edit_link + '" target="_blank">' + product.title + '</a></td>';
                html += '<td>' + (product.sku || '-') + '</td>';
                html += '<td>' + product.price + '</td>';
                html += '<td>' + product.categories + '</td>';
                html += '<td>' + statusBadge + '</td>';
                html += '<td>' + syncBadge + '</td>';
                html += '<td>';
                html += '<button type="button" class="' + buttonClass + '" data-id="' + product.ID + '" ' + buttonDisabled + '>' + buttonText + '</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#smartsell-products-list').html(html);
        
        // 渲染分页
        if (data.total_pages > 1) {
            renderPagination('.smartsell-pagination[data-type="products"]', currentPage, data.total_pages);
        } else {
            $('.smartsell-pagination[data-type="products"]').html('');
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
    $(document).on('click', '.smartsell-pagination[data-type="products"] .smartsell-page-btn', function() {
        var page = $(this).data('page');
        if (page) {
            loadProducts(page);
        }
    });
    
    // 全选/取消全选（只选中未禁用的复选框）
    $('#smartsell-check-all-products, #smartsell-select-all-products').on('click', function() {
        var isChecked = $(this).is(':checked') || $(this).is('button');
        if ($(this).is('button')) {
            isChecked = !$('#smartsell-check-all-products').prop('checked');
        }
        $('#smartsell-check-all-products').prop('checked', isChecked);
        // 只选中未禁用的复选框
        $('.smartsell-product-check:not(:disabled)').prop('checked', isChecked);
    });
    
    // 筛选按钮
    $('#smartsell-product-filter').on('click', function() {
        loadProducts(1);
    });
    
    // 单个同步（只处理未禁用的按钮）
    $(document).on('click', '.smartsell-sync-product-single', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        var productId = $(this).data('id');
        syncProducts([productId]);
    });
    
    // 批量同步（只同步未禁用的复选框）
    $('#smartsell-sync-selected-products').on('click', function() {
        var selectedIds = [];
        $('.smartsell-product-check:checked:not(:disabled)').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            alert('<?php esc_html_e('请选择要同步的商品', 'smartsell-assistant'); ?>');
            return;
        }
        
        syncProducts(selectedIds);
    });
    
    // 同步商品
    function syncProducts(productIds) {
        $('#smartsell-sync-products-modal').show();
        $('#smartsell-sync-products-total').text(productIds.length);
        $('#smartsell-sync-products-current').text(0);
        $('#smartsell-sync-products-progress').css('width', '0%');
        $('#smartsell-sync-products-status').html('');
        
        var completed = 0;
        
        function syncNext() {
            if (completed >= productIds.length) {
                setTimeout(function() {
                    $('#smartsell-sync-products-modal').hide();
                    loadProducts(currentPage);
                }, 1000);
                return;
            }
            
            var productId = productIds[completed];
            
            $.ajax({
                url: smartsellAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smartsell_sync_products',
                    nonce: smartsellAdmin.nonce,
                    product_ids: [productId]
                },
                success: function(response) {
                    completed++;
                    var progress = (completed / productIds.length * 100).toFixed(0);
                    $('#smartsell-sync-products-current').text(completed);
                    $('#smartsell-sync-products-progress').css('width', progress + '%');
                    
                    if (response.success) {
                        syncedProducts.push(productId);
                        $('#smartsell-sync-products-status').prepend('<div class="sync-item success">✓ <?php esc_html_e('商品ID', 'smartsell-assistant'); ?> ' + productId + ' <?php esc_html_e('同步成功', 'smartsell-assistant'); ?></div>');
                    } else {
                        $('#smartsell-sync-products-status').prepend('<div class="sync-item error">✗ <?php esc_html_e('商品ID', 'smartsell-assistant'); ?> ' + productId + ' <?php esc_html_e('同步失败', 'smartsell-assistant'); ?></div>');
                    }
                    
                    syncNext();
                },
                error: function() {
                    completed++;
                    $('#smartsell-sync-products-status').prepend('<div class="sync-item error">✗ <?php esc_html_e('商品ID', 'smartsell-assistant'); ?> ' + productId + ' <?php esc_html_e('同步失败', 'smartsell-assistant'); ?></div>');
                    syncNext();
                }
            });
        }
        
        syncNext();
    }
    
    // 初始加载
    loadProducts(1);
});
</script>
