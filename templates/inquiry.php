<?php
/**
 * 线索追踪模板
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="smartsell-wrap">
    <div class="smartsell-header">
        <h1><?php esc_html_e('线索追踪', 'smartsell-assistant'); ?></h1>
        <p><?php esc_html_e('管理和跟进销售线索', 'smartsell-assistant'); ?></p>
    </div>
    
    <div class="smartsell-card">
        <!-- 筛选区域 -->
        <div class="smartsell-filters">
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('搜索', 'smartsell-assistant'); ?></label>
                <input type="text" id="smartsell-inquiry-search" class="smartsell-form-input" placeholder="<?php esc_attr_e('搜索联系人、线索信息...', 'smartsell-assistant'); ?>" style="width: 200px;">
            </div>
            <div class="smartsell-filter-item">
                <label class="smartsell-filter-label"><?php esc_html_e('状态', 'smartsell-assistant'); ?></label>
                <select id="smartsell-inquiry-status" class="smartsell-filter-select">
                    <option value=""><?php esc_html_e('全部', 'smartsell-assistant'); ?></option>
                    <option value="1"><?php esc_html_e('新线索', 'smartsell-assistant'); ?></option>
                    <option value="2"><?php esc_html_e('跟进中', 'smartsell-assistant'); ?></option>
                    <option value="3"><?php esc_html_e('无价值', 'smartsell-assistant'); ?></option>
                </select>
            </div>
            <div class="smartsell-filter-item">
                <button type="button" id="smartsell-inquiry-filter" class="smartsell-btn smartsell-btn-primary">
                    <?php esc_html_e('筛选', 'smartsell-assistant'); ?>
                </button>
            </div>
            <div class="smartsell-filter-item" style="margin-left: auto;">
                <button type="button" id="smartsell-inquiry-add" class="smartsell-btn smartsell-btn-success">
                    + <?php esc_html_e('新增线索', 'smartsell-assistant'); ?>
                </button>
            </div>
        </div>
        
        <!-- 表格 -->
        <table class="smartsell-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('联系人', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('联系方式', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('线索信息', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('地区', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('标签', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('状态', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('更新时间', 'smartsell-assistant'); ?></th>
                    <th><?php esc_html_e('操作', 'smartsell-assistant'); ?></th>
                </tr>
            </thead>
            <tbody id="smartsell-inquiry-list">
                <tr>
                    <td colspan="8" class="smartsell-loading">
                        <div class="smartsell-spinner"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- 分页 -->
        <div class="smartsell-pagination" data-type="inquiry"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // 加载线索列表
    function loadInquiries(page) {
        page = page || 1;
        var search = $('#smartsell-inquiry-search').val();
        var status = $('#smartsell-inquiry-status').val();

        var requestData = {
            page: page,
            page_size: 10,
            search_text: search
        };

        if (status && status !== '') {
            requestData.status = parseInt(status);
        }

        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/list',
                method: 'GET',
                data: requestData
            },
            beforeSend: function() {
                $('#smartsell-inquiry-list').html('<tr><td colspan="8" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderInquiries(response.data.data);
                } else {
                    $('#smartsell-inquiry-list').html('<tr><td colspan="8" class="smartsell-empty"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></td></tr>');
                    $('.smartsell-pagination[data-type="inquiry"]').html('');
                }
            }
        });
    }
    
    // 渲染线索列表
    function renderInquiries(data) {
        var html = '';
        var statusMap = {
            1: {text: '<?php esc_html_e('新线索', 'smartsell-assistant'); ?>', class: 'new'},
            2: {text: '<?php esc_html_e('跟进中', 'smartsell-assistant'); ?>', class: 'following'},
            3: {text: '<?php esc_html_e('无价值', 'smartsell-assistant'); ?>', class: 'invalid'}
        };
        
        if (!data.items || data.items.length === 0) {
            html = '<tr><td colspan="8" class="smartsell-empty"><div class="smartsell-empty-icon">🔍</div><div class="smartsell-empty-text"><?php esc_html_e('暂无线索', 'smartsell-assistant'); ?></div></td></tr>';
        } else {
            $.each(data.items, function(i, inquiry) {
                var status = statusMap[inquiry.status] || {text: '-', class: ''};
                var inquiryData = JSON.stringify(inquiry).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                
                // 处理标签显示
                var tagsHtml = '';
                if (inquiry.tags && inquiry.tags.trim() !== '') {
                    var tags = inquiry.tags.split(',');
                    tags.forEach(function(tag) {
                        var tagName = tag.trim();
                        if (tagName) {
                            tagsHtml += '<span class="smartsell-tag">' + escapeHtml(tagName) + '</span>';
                        }
                    });
                }
                if (!tagsHtml) {
                    tagsHtml = '-';
                } else {
                    tagsHtml = '<div class="smartsell-tags">' + tagsHtml + '</div>';
                }
                
                html += '<tr>';
                html += '<td>' + escapeHtml(inquiry.contact_name || '-') + '</td>';
                html += '<td>' + escapeHtml(inquiry.contact_info || '-') + '</td>';
                html += '<td>' + escapeHtml(inquiry.inquiry_info ? inquiry.inquiry_info.substring(0, 50) + '...' : '-') + '</td>';
                html += '<td>' + escapeHtml(inquiry.country || '-') + '</td>';
                html += '<td>' + tagsHtml + '</td>';
                html += '<td><span class="smartsell-status ' + status.class + '">' + status.text + '</span></td>';
                html += '<td>' + (inquiry.update_time || '-') + '</td>';
                html += '<td class="smartsell-actions-cell">';
                html += '<div class="smartsell-dropdown">';
                html += '<button type="button" class="smartsell-btn smartsell-btn-sm smartsell-btn-secondary smartsell-dropdown-toggle"><?php esc_html_e('操作', 'smartsell-assistant'); ?> ▼</button>';
                html += '<div class="smartsell-dropdown-menu">';
                html += '<a href="#" class="smartsell-dropdown-item smartsell-follow-inquiry" data-id="' + inquiry.id + '"><?php esc_html_e('跟进', 'smartsell-assistant'); ?></a>';
                html += '<a href="#" class="smartsell-dropdown-item smartsell-tags-inquiry" data-id="' + inquiry.id + '" data-tags="' + escapeHtml(inquiry.tags || '') + '"><?php esc_html_e('标签', 'smartsell-assistant'); ?></a>';
                html += '<a href="#" class="smartsell-dropdown-item smartsell-status-inquiry" data-id="' + inquiry.id + '" data-status="' + inquiry.status + '"><?php esc_html_e('状态', 'smartsell-assistant'); ?></a>';
                html += '<a href="#" class="smartsell-dropdown-item smartsell-edit-inquiry" data-inquiry="' + inquiryData + '"><?php esc_html_e('编辑', 'smartsell-assistant'); ?></a>';
                html += '<a href="#" class="smartsell-dropdown-item smartsell-convert-inquiry" data-id="' + inquiry.id + '"><?php esc_html_e('转客户', 'smartsell-assistant'); ?></a>';
                html += '</div>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
        }
        
        $('#smartsell-inquiry-list').html(html);
        // 渲染分页
        if (data && typeof data.total !== 'undefined') {
            renderPagination(data.total, data.total_pages, data.page);
        }
    }
    
    // HTML转义
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // 筛选按钮点击
    $('#smartsell-inquiry-filter').on('click', function() {
        loadInquiries(1);
    });
    
    // 下拉菜单切换
    $(document).on('click', '.smartsell-dropdown-toggle', function(e) {
        e.stopPropagation();
        var $menu = $(this).next('.smartsell-dropdown-menu');
        $('.smartsell-dropdown-menu').not($menu).hide();
        $menu.toggle();
    });
    
    // 点击其他地方关闭下拉菜单
    $(document).on('click', function() {
        $('.smartsell-dropdown-menu').hide();
    });
    
    // 跟进按钮点击
    $(document).on('click', '.smartsell-follow-inquiry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.smartsell-dropdown-menu').hide();
        
        var inquiryId = $(this).data('id');
        currentInquiryId = inquiryId;
        
        // 加载跟进记录
        loadFollowList(inquiryId);
        $('#smartsell-follow-modal').show();
    });
    
    // 当前操作的线索ID
    var currentInquiryId = null;
    
    // 加载跟进记录
    function loadFollowList(inquiryId) {
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry_follow/list',
                method: 'GET',
                data: {
                    inquiry_id: inquiryId,
                    page: 1,
                    page_size: 50
                }
            },
            beforeSend: function() {
                $('#smartsell-follow-list').html('<div class="smartsell-loading"><div class="smartsell-spinner"></div></div>');
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderFollowList(response.data.data);
                } else {
                    $('#smartsell-follow-list').html('<div class="smartsell-empty-text"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></div>');
                }
            }
        });
    }
    
    // 渲染跟进记录
    function renderFollowList(data) {
        var html = '';
        // data 是数组，不是对象
        if (!data || data.length === 0) {
            html = '<div class="smartsell-empty-text"><?php esc_html_e('暂无跟进记录', 'smartsell-assistant'); ?></div>';
        } else {
            $.each(data, function(i, item) {
                html += '<div class="smartsell-follow-item">';
                html += '<div class="smartsell-follow-header">';
                html += '<span class="smartsell-follow-method">' + escapeHtml(item.follow_method || '') + '</span>';
                html += '<span class="smartsell-follow-person">' + escapeHtml(item.follow_person || '') + '</span>';
                html += '<span class="smartsell-follow-time">' + (item.create_time || '') + '</span>';
                html += '</div>';
                html += '<div class="smartsell-follow-content">' + escapeHtml(item.follow_info || '') + '</div>';
                html += '</div>';
            });
        }
        $('#smartsell-follow-list').html(html);
    }
    
    // 快捷设置跟进方式
    $(document).on('click', '.smartsell-quick-method', function() {
        var method = $(this).data('method');
        $('#follow-method').val(method);
    });
    
    // 提交跟进记录
    $(document).on('click', '#smartsell-follow-submit', function() {
        var method = $('#follow-method').val().trim();
        var info = $('#follow-info').val().trim();
        
        if (!method) {
            alert('<?php esc_html_e('请输入沟通方式', 'smartsell-assistant'); ?>');
            return;
        }
        if (!info) {
            alert('<?php esc_html_e('请输入跟进信息', 'smartsell-assistant'); ?>');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('提交中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry_follow/add',
                method: 'POST',
                contentType: 'form',
                data: {
                    inquiry_id: currentInquiryId,
                    follow_method: method,
                    follow_info: info
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('添加跟进记录成功', 'smartsell-assistant'); ?>');
                    $('#follow-method').val('');
                    $('#follow-info').val('');
                    loadFollowList(currentInquiryId);
                    loadInquiries(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('添加失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('添加失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('提交', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 关闭跟进模态框
    $(document).on('click', '#smartsell-follow-modal .smartsell-modal-close, #smartsell-follow-cancel', function() {
        $('#smartsell-follow-modal').hide();
    });
    
    // 标签按钮点击
    $(document).on('click', '.smartsell-tags-inquiry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.smartsell-dropdown-menu').hide();
        
        var inquiryId = $(this).data('id');
        var currentTags = $(this).data('tags') || '';
        currentInquiryId = inquiryId;
        
        // 加载标签列表
        loadTagsList(currentTags);
        $('#smartsell-tags-modal').show();
    });
    
    // 加载标签列表
    function loadTagsList(currentTags) {
        var selectedTags = currentTags ? currentTags.split(',') : [];
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/tags/list',
                method: 'GET',
                data: {
                    page: 1,
                    page_size: 100
                }
            },
            beforeSend: function() {
                $('#smartsell-tags-list').html('<div class="smartsell-loading"><div class="smartsell-spinner"></div></div>');
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    renderTagsList(response.data.data, selectedTags);
                } else {
                    $('#smartsell-tags-list').html('<div class="smartsell-empty-text"><?php esc_html_e('加载失败', 'smartsell-assistant'); ?></div>');
                }
            }
        });
    }
    
    // 渲染标签列表
    function renderTagsList(data, selectedTags) {
        var html = '';
        var tags = (data.items || []).filter(function(tag) { return tag.status === 1; });
        
        if (tags.length === 0) {
            html = '<div class="smartsell-empty-text"><?php esc_html_e('暂无可用标签', 'smartsell-assistant'); ?></div>';
        } else {
            html = '<div class="smartsell-tags-checkbox-group">';
            $.each(tags, function(i, tag) {
                var checked = selectedTags.indexOf(tag.name) > -1 ? 'checked' : '';
                html += '<label class="smartsell-tag-checkbox">';
                html += '<input type="checkbox" name="inquiry_tags" value="' + escapeHtml(tag.name) + '" ' + checked + '>';
                html += '<span>' + escapeHtml(tag.name) + '</span>';
                html += '</label>';
            });
            html += '</div>';
        }
        $('#smartsell-tags-list').html(html);
    }
    
    // 保存标签
    $(document).on('click', '#smartsell-tags-save', function() {
        var selectedTags = [];
        $('input[name="inquiry_tags"]:checked').each(function() {
            selectedTags.push($(this).val());
        });
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('保存中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/tags',
                method: 'POST',
                contentType: 'form',
                data: {
                    id: currentInquiryId,
                    tags: selectedTags.join(',')
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('标签更新成功', 'smartsell-assistant'); ?>');
                    $('#smartsell-tags-modal').hide();
                    loadInquiries(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('更新失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('更新失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('保存', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 关闭标签模态框
    $(document).on('click', '#smartsell-tags-modal .smartsell-modal-close, #smartsell-tags-cancel', function() {
        $('#smartsell-tags-modal').hide();
    });
    
    // 状态按钮点击
    $(document).on('click', '.smartsell-status-inquiry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.smartsell-dropdown-menu').hide();
        
        var inquiryId = $(this).data('id');
        var currentStatus = $(this).data('status');
        currentInquiryId = inquiryId;
        
        // 设置当前状态
        $('input[name="inquiry_status"][value="' + currentStatus + '"]').prop('checked', true);
        $('#smartsell-status-modal').show();
    });
    
    // 保存状态
    $(document).on('click', '#smartsell-status-save', function() {
        var selectedStatus = $('input[name="inquiry_status"]:checked').val();
        
        if (!selectedStatus) {
            alert('<?php esc_html_e('请选择状态', 'smartsell-assistant'); ?>');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php esc_html_e('保存中...', 'smartsell-assistant'); ?>');
        
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/update',
                method: 'POST',
                contentType: 'form',
                data: {
                    id: currentInquiryId,
                    status: selectedStatus
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('状态更新成功', 'smartsell-assistant'); ?>');
                    $('#smartsell-status-modal').hide();
                    loadInquiries(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('更新失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('更新失败，请检查网络', 'smartsell-assistant'); ?>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('<?php esc_html_e('保存', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 关闭状态模态框
    $(document).on('click', '#smartsell-status-modal .smartsell-modal-close, #smartsell-status-cancel', function() {
        $('#smartsell-status-modal').hide();
    });
    
    // 新增/编辑模式标记
    var isAddMode = false;
    
    // 新增线索按钮点击
    $('#smartsell-inquiry-add').on('click', function() {
        isAddMode = true;
        currentInquiryId = null;
        // 清空表单和错误状态
        $('#edit-contact-name').val('').removeClass('smartsell-input-error');
        $('#edit-contact-info').val('').removeClass('smartsell-input-error');
        $('#edit-inquiry-info').val('').removeClass('smartsell-input-error');
        $('#edit-country').val('').removeClass('smartsell-input-error');
        $('#edit-region').val('').removeClass('smartsell-input-error');
        $('#edit-remark').val('').removeClass('smartsell-input-error');
        $('.smartsell-form-error').remove();
        $('#smartsell-edit-modal .smartsell-modal-header h3').text('<?php esc_html_e('新增线索', 'smartsell-assistant'); ?>');
        $('#smartsell-edit-modal').show();
    });
    
    // 输入框输入时清除错误状态
    $(document).on('input', '#edit-inquiry-info', function() {
        $(this).removeClass('smartsell-input-error');
        $(this).closest('.smartsell-form-group').find('.smartsell-form-error').remove();
    });
    
    // 编辑按钮点击
    $(document).on('click', '.smartsell-edit-inquiry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.smartsell-dropdown-menu').hide();
        
        isAddMode = false;
        var inquiryData = $(this).data('inquiry');
        if (typeof inquiryData === 'string') {
            inquiryData = JSON.parse(inquiryData.replace(/&quot;/g, '"'));
        }
        currentInquiryId = inquiryData.id;
        
        // 清空错误状态
        $('.smartsell-input, .smartsell-textarea').removeClass('smartsell-input-error');
        $('.smartsell-form-error').remove();
        
        // 填充表单
        $('#edit-contact-name').val(inquiryData.contact_name || '');
        $('#edit-contact-info').val(inquiryData.contact_info || '');
        $('#edit-inquiry-info').val(inquiryData.inquiry_info || '');
        $('#edit-country').val(inquiryData.country || '');
        $('#edit-region').val(inquiryData.region || '');
        $('#edit-remark').val(inquiryData.remark || '');
        $('#smartsell-edit-modal .smartsell-modal-header h3').text('<?php esc_html_e('编辑线索', 'smartsell-assistant'); ?>');
        $('#smartsell-edit-modal').show();
    });
    
    // 保存编辑/新增
    $(document).on('click', '#smartsell-edit-save', function() {
        var $btn = $(this);
        
        // 表单验证
        var inquiryInfo = $('#edit-inquiry-info').val().trim();
        
        // 清除之前的错误样式
        $('.smartsell-input, .smartsell-textarea').removeClass('smartsell-input-error');
        $('.smartsell-form-error').remove();
        
        // 验证必填字段
        var hasError = false;
        if (!inquiryInfo) {
            $('#edit-inquiry-info').addClass('smartsell-input-error');
            $('#edit-inquiry-info').closest('.smartsell-form-group').append('<span class="smartsell-form-error"><?php esc_html_e('线索信息不能为空', 'smartsell-assistant'); ?></span>');
            hasError = true;
        }
        
        if (hasError) {
            return;
        }
        
        $btn.prop('disabled', true).text('<?php esc_html_e('保存中...', 'smartsell-assistant'); ?>');
        
        var endpoint = isAddMode ? '/inquiry/add' : '/inquiry/update';
        var requestData = {
            contact_name: $('#edit-contact-name').val().trim(),
            contact_info: $('#edit-contact-info').val().trim(),
            inquiry_info: inquiryInfo,
            country: $('#edit-country').val().trim(),
            region: $('#edit-region').val().trim(),
            remark: $('#edit-remark').val().trim()
        };
        
        if (!isAddMode) {
            requestData.id = currentInquiryId;
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
                    $('#smartsell-edit-modal').hide();
                    loadInquiries(1);
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
    
    // 关闭编辑模态框
    $(document).on('click', '#smartsell-edit-modal .smartsell-modal-close, #smartsell-edit-cancel', function() {
        $('#smartsell-edit-modal').hide();
    });
    
    // 转客户按钮点击
    $(document).on('click', '.smartsell-convert-inquiry', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.smartsell-dropdown-menu').hide();
        
        var inquiryId = $(this).data('id');
        
        if (!confirm('<?php esc_html_e('确定要将此线索转为客户吗？', 'smartsell-assistant'); ?>')) {
            return;
        }
        
        // 先获取线索详情
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/inquiry/show',
                method: 'GET',
                data: {
                    id: inquiryId
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    var inquiry = response.data.data;
                    // 添加客户
                    addCustomerFromInquiry(inquiry);
                } else {
                    alert('<?php esc_html_e('获取线索信息失败', 'smartsell-assistant'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('获取线索信息失败', 'smartsell-assistant'); ?>');
            }
        });
    });
    
    // 从线索添加客户
    function addCustomerFromInquiry(inquiry) {
        $.ajax({
            url: smartsellAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'smartsell_api_request',
                nonce: smartsellAdmin.nonce,
                endpoint: '/customer/add',
                method: 'POST',
                contentType: 'form',
                data: {
                    chat_id: inquiry.chat_id || 0,
                    contact_person: inquiry.contact_name || '',
                    contact_info: inquiry.contact_info || '',
                    country: inquiry.country || '',
                    region: inquiry.region || '',
                    remark: inquiry.remark || '',
                    type: 1
                }
            },
            success: function(response) {
                if (response.success && response.data.code === 0) {
                    alert('<?php esc_html_e('转客户成功', 'smartsell-assistant'); ?>');
                    loadInquiries(1);
                } else {
                    var errMsg = response.data && response.data.msg ? response.data.msg : '<?php esc_html_e('转客户失败', 'smartsell-assistant'); ?>';
                    alert(errMsg);
                }
            },
            error: function() {
                alert('<?php esc_html_e('转客户失败，请检查网络', 'smartsell-assistant'); ?>');
            }
        });
    }
    
    // 渲染分页（通用）
    function renderPagination(total, pages, current) {
        var html = '<div class="smartsell-pagination-info" style="font-size: 12px; color: #6b7280;">' + '<?php esc_html_e('共', 'smartsell-assistant'); ?> ' + total + ' ' + '<?php esc_html_e('条', 'smartsell-assistant'); ?>' + '</div>';
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

        $('.smartsell-pagination[data-type="inquiry"]').html(html);
    }

    // 分页点击事件
    $(document).on('click', '.smartsell-pagination[data-type="inquiry"] a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            loadInquiries(page);
        }
    });

    // 初始加载
    loadInquiries(1);
});
</script>

<!-- 下拉菜单样式 -->
<style>
.smartsell-btn-success {
    background-color: #52c41a;
    border-color: #52c41a;
    color: #fff;
}
.smartsell-btn-success:hover {
    background-color: #73d13d;
    border-color: #73d13d;
}
.smartsell-actions-cell {
    position: relative;
}
.smartsell-dropdown {
    position: relative;
    display: inline-block;
}
.smartsell-dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    min-width: 100px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 1000;
}
.smartsell-dropdown-item {
    display: block;
    padding: 8px 12px;
    color: #333;
    text-decoration: none;
    white-space: nowrap;
}
.smartsell-dropdown-item:hover {
    background: #f5f5f5;
    color: #1890ff;
}
.smartsell-follow-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.smartsell-follow-item:last-child {
    border-bottom: none;
}
.smartsell-follow-header {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 12px;
    color: #666;
}
.smartsell-follow-method {
    background: #e6f7ff;
    color: #1890ff;
    padding: 2px 8px;
    border-radius: 4px;
}
.smartsell-follow-content {
    color: #333;
    line-height: 1.6;
}
.smartsell-follow-form {
    border-top: 1px solid #eee;
    padding-top: 15px;
    margin-top: 15px;
}
.smartsell-quick-methods {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}
.smartsell-quick-method {
    padding: 4px 10px;
    font-size: 12px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}
.smartsell-quick-method:hover {
    background: #e6f7ff;
    border-color: #1890ff;
    color: #1890ff;
}
.smartsell-tags-checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.smartsell-tag-checkbox {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}
.smartsell-tag-checkbox:hover {
    background: #e6f7ff;
    border-color: #1890ff;
}
.smartsell-tag-checkbox input[type="checkbox"] {
    margin: 0;
}
.smartsell-tag-checkbox input[type="checkbox"]:checked + span {
    color: #1890ff;
}
.smartsell-status-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.smartsell-radio-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}
.smartsell-radio-item:hover {
    background: #e6f7ff;
    border-color: #1890ff;
}
.smartsell-radio-item input[type="radio"] {
    margin: 0;
}
.smartsell-radio-item input[type="radio"]:checked + span {
    color: #1890ff;
    font-weight: 500;
}
.smartsell-modal-sm {
    max-width: 400px;
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

<!-- 跟进模态框 -->
<div id="smartsell-follow-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content smartsell-modal-lg">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('跟进记录', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <div id="smartsell-follow-list" style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                <div class="smartsell-loading"><div class="smartsell-spinner"></div></div>
            </div>
            
            <div class="smartsell-follow-form">
                <div class="smartsell-form-group">
                    <label><?php esc_html_e('沟通方式', 'smartsell-assistant'); ?></label>
                    <input type="text" id="follow-method" class="smartsell-input" placeholder="<?php esc_attr_e('请输入沟通方式', 'smartsell-assistant'); ?>">
                    <div class="smartsell-quick-methods">
                        <span class="smartsell-quick-method" data-method="电话"><?php esc_html_e('电话', 'smartsell-assistant'); ?></span>
                        <span class="smartsell-quick-method" data-method="邮件"><?php esc_html_e('邮件', 'smartsell-assistant'); ?></span>
                        <span class="smartsell-quick-method" data-method="短信"><?php esc_html_e('短信', 'smartsell-assistant'); ?></span>
                        <span class="smartsell-quick-method" data-method="微信"><?php esc_html_e('微信', 'smartsell-assistant'); ?></span>
                    </div>
                </div>
                <div class="smartsell-form-group">
                    <label><?php esc_html_e('跟进信息', 'smartsell-assistant'); ?></label>
                    <textarea id="follow-info" class="smartsell-textarea" rows="3" placeholder="<?php esc_attr_e('请输入跟进信息', 'smartsell-assistant'); ?>"></textarea>
                </div>
            </div>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-follow-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-follow-submit"><?php esc_html_e('提交', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>

<!-- 标签模态框 -->
<div id="smartsell-tags-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('线索标签', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <div id="smartsell-tags-list">
                <div class="smartsell-loading"><div class="smartsell-spinner"></div></div>
            </div>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-tags-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-tags-save"><?php esc_html_e('保存', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>

<!-- 状态模态框 -->
<div id="smartsell-status-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content smartsell-modal-sm">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('修改状态', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <div class="smartsell-status-options">
                <label class="smartsell-radio-item">
                    <input type="radio" name="inquiry_status" value="1">
                    <span><?php esc_html_e('新线索', 'smartsell-assistant'); ?></span>
                </label>
                <label class="smartsell-radio-item">
                    <input type="radio" name="inquiry_status" value="2">
                    <span><?php esc_html_e('跟进中', 'smartsell-assistant'); ?></span>
                </label>
                <label class="smartsell-radio-item">
                    <input type="radio" name="inquiry_status" value="3">
                    <span><?php esc_html_e('无价值', 'smartsell-assistant'); ?></span>
                </label>
            </div>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-status-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-status-save"><?php esc_html_e('保存', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>

<!-- 编辑模态框 -->
<div id="smartsell-edit-modal" class="smartsell-modal" style="display:none;">
    <div class="smartsell-modal-content smartsell-modal-lg">
        <div class="smartsell-modal-header">
            <h3><?php esc_html_e('编辑线索', 'smartsell-assistant'); ?></h3>
            <span class="smartsell-modal-close">&times;</span>
        </div>
        <div class="smartsell-modal-body">
            <form id="smartsell-edit-form">
                <div class="smartsell-form-row">
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('联系人', 'smartsell-assistant'); ?></label>
                        <input type="text" id="edit-contact-name" class="smartsell-input">
                    </div>
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('联系方式', 'smartsell-assistant'); ?></label>
                        <input type="text" id="edit-contact-info" class="smartsell-input">
                    </div>
                </div>
                <div class="smartsell-form-row">
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('国家', 'smartsell-assistant'); ?></label>
                        <input type="text" id="edit-country" class="smartsell-input">
                    </div>
                    <div class="smartsell-form-group">
                        <label><?php esc_html_e('地区', 'smartsell-assistant'); ?></label>
                        <input type="text" id="edit-region" class="smartsell-input">
                    </div>
                </div>
                <div class="smartsell-form-group smartsell-form-group-full">
                    <label><?php esc_html_e('线索信息', 'smartsell-assistant'); ?><span class="smartsell-required">*</span></label>
                    <textarea id="edit-inquiry-info" class="smartsell-textarea" rows="3" required></textarea>
                </div>
                <div class="smartsell-form-group smartsell-form-group-full">
                    <label><?php esc_html_e('备注', 'smartsell-assistant'); ?></label>
                    <textarea id="edit-remark" class="smartsell-textarea" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="smartsell-modal-footer">
            <button type="button" class="smartsell-btn smartsell-btn-secondary" id="smartsell-edit-cancel"><?php esc_html_e('取消', 'smartsell-assistant'); ?></button>
            <button type="button" class="smartsell-btn smartsell-btn-primary" id="smartsell-edit-save"><?php esc_html_e('保存', 'smartsell-assistant'); ?></button>
        </div>
    </div>
</div>
