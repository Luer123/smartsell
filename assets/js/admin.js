/**
 * SmartSell Assistant Admin JavaScript
 */
(function($) {
    'use strict';

    // 主对象
    var SmartSellAdmin = {
        
        // API 配置
        apiUrl: '',
        token: '',
        
        /**
         * 初始化
         */
        init: function() {
            // 从 WordPress 获取配置
            this.apiUrl = smartsellAdmin.apiUrl || '';
            this.token = smartsellAdmin.token || '';
            
            this.bindEvents();
            this.initTabs();
        },
        
        /**
         * 绑定事件
         */
        bindEvents: function() {
            var self = this;
            
            // 全选/取消全选
            $(document).on('change', '.smartsell-select-all', function() {
                var checked = $(this).prop('checked');
                $(this).closest('table').find('.smartsell-select-item').prop('checked', checked);
            });
            
            // 单个选择
            $(document).on('change', '.smartsell-select-item', function() {
                var table = $(this).closest('table');
                var total = table.find('.smartsell-select-item').length;
                var checked = table.find('.smartsell-select-item:checked').length;
                table.find('.smartsell-select-all').prop('checked', total === checked);
            });
            
            // 模态框关闭
            $(document).on('click', '.smartsell-modal-close, .smartsell-modal-cancel', function() {
                $(this).closest('.smartsell-modal').removeClass('active');
            });
            
            // 点击模态框外部关闭
            $(document).on('click', '.smartsell-modal', function(e) {
                if ($(e.target).hasClass('smartsell-modal')) {
                    $(this).removeClass('active');
                }
            });
            
            // API 请求按钮
            $(document).on('click', '[data-api-action]', function(e) {
                e.preventDefault();
                var action = $(this).data('api-action');
                var endpoint = $(this).data('endpoint');
                var method = $(this).data('method') || 'GET';
                var data = $(this).data('params') || {};
                
                self.apiRequest(endpoint, method, data, function(response) {
                    if (response.success) {
                        self.showNotice('success', smartsellAdmin.i18n.success);
                        if (typeof window['smartsell_' + action + '_callback'] === 'function') {
                            window['smartsell_' + action + '_callback'](response.data);
                        }
                    } else {
                        self.showNotice('error', response.data.message || smartsellAdmin.i18n.error);
                    }
                });
            });
            
            // 同步文章
            $(document).on('click', '#smartsell-sync-posts', function(e) {
                e.preventDefault();
                self.syncPosts();
            });
            
            // 同步商品
            $(document).on('click', '#smartsell-sync-products', function(e) {
                e.preventDefault();
                self.syncProducts();
            });
            
            // 加载文章列表
            $(document).on('click', '#smartsell-load-posts', function(e) {
                e.preventDefault();
                self.loadPosts(1);
            });
            
            // 加载商品列表
            $(document).on('click', '#smartsell-load-products', function(e) {
                e.preventDefault();
                self.loadProducts(1);
            });
            
            // 搜索
            $(document).on('submit', '.smartsell-search-form', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                if (type === 'posts') {
                    self.loadPosts(1);
                } else if (type === 'products') {
                    self.loadProducts(1);
                }
            });
            
            // 分页点击
            $(document).on('click', '.smartsell-pagination-links a', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                var type = $(this).closest('.smartsell-pagination').data('type');
                if (type === 'posts') {
                    self.loadPosts(page);
                } else if (type === 'products') {
                    self.loadProducts(page);
                } else if (type === 'chat') {
                    self.loadChats(page);
                } else if (type === 'inquiry') {
                    self.loadInquiries(page);
                } else if (type === 'customer') {
                    self.loadCustomers(page);
                }
            });
            
            // 头像上传
            $(document).on('click', '#smartsell-upload-avatar', function(e) {
                e.preventDefault();
                self.uploadAvatar();
            });
            
            // 查看聊天记录
            $(document).on('click', '.smartsell-view-chat', function(e) {
                e.preventDefault();
                var chatId = $(this).data('id');
                self.viewChatLog(chatId);
            });
            
            // 查看线索详情
            $(document).on('click', '.smartsell-view-inquiry', function(e) {
                e.preventDefault();
                var inquiryId = $(this).data('id');
                self.viewInquiry(inquiryId);
            });
            
            // 查看客户详情
            $(document).on('click', '.smartsell-view-customer', function(e) {
                e.preventDefault();
                var customerId = $(this).data('id');
                self.viewCustomer(customerId);
            });
        },
        
        /**
         * 初始化标签页
         */
        initTabs: function() {
            $(document).on('click', '.smartsell-tab', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                
                $('.smartsell-tab-content').hide();
                $(target).show();
            });
        },
        
        /**
         * API 请求 - 直接调用后端 API
         * 用于：登录、会话、线索、客户等所有非 WordPress 数据
         */
        apiRequest: function(endpoint, method, data, callback) {
            var self = this;
            
            // 构建完整 URL
            var url = this.apiUrl + endpoint;
            
            // 请求配置
            var ajaxOptions = {
                url: url,
                type: method || 'GET',
                contentType: 'application/json',
                dataType: 'json',
                beforeSend: function(xhr) {
                    self.showLoading();
                    // 如果有 token，添加认证头
                    if (self.token) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + self.token);
                    }
                },
                success: function(response) {
                    self.hideLoading();
                    if (callback) {
                        callback({ success: true, data: response });
                    }
                },
                error: function(xhr) {
                    self.hideLoading();
                    var errorMsg = smartsellAdmin.i18n.error;
                    if (xhr.responseJSON && xhr.responseJSON.detail) {
                        errorMsg = xhr.responseJSON.detail;
                    }
                    if (callback) {
                        callback({ success: false, data: { message: errorMsg } });
                    }
                }
            };
            
            // 处理请求数据
            if (method === 'GET' && data) {
                ajaxOptions.url += '?' + $.param(data);
            } else if (data) {
                ajaxOptions.data = JSON.stringify(data);
            }
            
            $.ajax(ajaxOptions);
        },
        
        /**
         * WordPress AJAX 请求
         * 仅用于：文章同步、商品同步等需要访问 WordPress 数据的功能
         */
        wpAjaxRequest: function(action, data, callback) {
            var self = this;
            
            data.action = action;
            data.nonce = smartsellAdmin.nonce;
            
            $.ajax({
                url: smartsellAdmin.ajaxUrl,
                type: 'POST',
                data: data,
                beforeSend: function() {
                    self.showLoading();
                },
                success: function(response) {
                    self.hideLoading();
                    if (callback) {
                        callback(response);
                    }
                },
                error: function() {
                    self.hideLoading();
                    self.showNotice('error', smartsellAdmin.i18n.error);
                }
            });
        },
        
        /**
         * 更新 Token
         */
        setToken: function(token) {
            this.token = token;
        },
        
        /**
         * 更新 API URL
         */
        setApiUrl: function(url) {
            this.apiUrl = url;
        },
        
        /**
         * 同步文章 - 使用 WordPress AJAX
         */
        syncPosts: function() {
            var self = this;
            var postIds = [];
            
            $('.smartsell-select-item:checked').each(function() {
                postIds.push($(this).val());
            });
            
            if (postIds.length === 0) {
                self.showNotice('warning', '请选择要同步的文章');
                return;
            }
            
            this.wpAjaxRequest('smartsell_sync_posts', {
                post_ids: postIds
            }, function(response) {
                if (response.success) {
                    self.showNotice('success', response.data.message);
                    self.loadPosts(1);
                } else {
                    self.showNotice('error', response.data.message || smartsellAdmin.i18n.syncFailed);
                }
            });
        },
        
        /**
         * 同步商品 - 使用 WordPress AJAX
         */
        syncProducts: function() {
            var self = this;
            var productIds = [];
            
            $('.smartsell-select-item:checked').each(function() {
                productIds.push($(this).val());
            });
            
            if (productIds.length === 0) {
                self.showNotice('warning', '请选择要同步的商品');
                return;
            }
            
            this.wpAjaxRequest('smartsell_sync_products', {
                product_ids: productIds
            }, function(response) {
                if (response.success) {
                    self.showNotice('success', response.data.message);
                    self.loadProducts(1);
                } else {
                    self.showNotice('error', response.data.message || smartsellAdmin.i18n.syncFailed);
                }
            });
        },
        
        /**
         * 加载文章列表 - 使用 WordPress AJAX
         */
        loadPosts: function(page) {
            var self = this;
            var search = $('#smartsell-posts-search').val() || '';
            
            this.wpAjaxRequest('smartsell_get_posts', {
                page: page,
                per_page: 20,
                search: search
            }, function(response) {
                if (response.success) {
                    self.renderPostsList(response.data);
                } else {
                    self.showNotice('error', response.data.message);
                }
            });
            
            // 显示加载状态
            $('#smartsell-posts-list').html('<tr><td colspan="6" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
        },
        
        /**
         * 渲染文章列表
         */
        renderPostsList: function(data) {
            var html = '';
            
            if (data.posts.length === 0) {
                html = '<tr><td colspan="6" class="smartsell-empty"><div class="smartsell-empty-icon">📝</div><div class="smartsell-empty-text">暂无文章</div></td></tr>';
            } else {
                $.each(data.posts, function(i, post) {
                    var syncStatus = post.synced ? 
                        '<span class="smartsell-status synced">已同步</span>' : 
                        '<span class="smartsell-status not-synced">未同步</span>';
                    
                    html += '<tr>';
                    html += '<td class="checkbox-col"><input type="checkbox" class="smartsell-select-item" value="' + post.id + '"></td>';
                    html += '<td>' + post.id + '</td>';
                    html += '<td>' + self.escapeHtml(post.title) + '</td>';
                    html += '<td>' + post.author + '</td>';
                    html += '<td>' + post.date + '</td>';
                    html += '<td>' + syncStatus + '</td>';
                    html += '</tr>';
                });
            }
            
            $('#smartsell-posts-list').html(html);
            
            // 更新分页
            this.renderPagination('posts', data.total, data.pages, 1);
        },
        
        /**
         * 加载商品列表 - 使用 WordPress AJAX
         */
        loadProducts: function(page) {
            var self = this;
            var search = $('#smartsell-products-search').val() || '';
            
            // 显示加载状态
            $('#smartsell-products-list').html('<tr><td colspan="7" class="smartsell-loading"><div class="smartsell-spinner"></div></td></tr>');
            
            this.wpAjaxRequest('smartsell_get_products', {
                page: page,
                per_page: 20,
                search: search
            }, function(response) {
                if (response.success) {
                    self.renderProductsList(response.data);
                } else {
                    self.showNotice('error', response.data.message);
                }
            });
        },
        
        /**
         * 渲染商品列表
         */
        renderProductsList: function(data) {
            var html = '';
            var self = this;
            
            if (data.products.length === 0) {
                html = '<tr><td colspan="7" class="smartsell-empty"><div class="smartsell-empty-icon">🛒</div><div class="smartsell-empty-text">暂无商品</div></td></tr>';
            } else {
                $.each(data.products, function(i, product) {
                    var syncStatus = product.synced ? 
                        '<span class="smartsell-status synced">已同步</span>' : 
                        '<span class="smartsell-status not-synced">未同步</span>';
                    
                    var image = product.image ? 
                        '<img src="' + product.image + '" width="40" height="40" style="object-fit:cover;border-radius:4px;">' : 
                        '-';
                    
                    html += '<tr>';
                    html += '<td class="checkbox-col"><input type="checkbox" class="smartsell-select-item" value="' + product.id + '"></td>';
                    html += '<td>' + image + '</td>';
                    html += '<td>' + self.escapeHtml(product.name) + '</td>';
                    html += '<td>' + (product.sku || '-') + '</td>';
                    html += '<td>' + (product.price || '-') + '</td>';
                    html += '<td>' + syncStatus + '</td>';
                    html += '</tr>';
                });
            }
            
            $('#smartsell-products-list').html(html);
            
            // 更新分页
            this.renderPagination('products', data.total, data.pages, 1);
        },
        
        /**
         * 渲染分页
         */
        renderPagination: function(type, total, pages, currentPage) {
            var html = '';
            html += '<div class="smartsell-pagination-info">共 ' + total + ' 条记录</div>';
            html += '<div class="smartsell-pagination-links">';
            
            if (currentPage > 1) {
                html += '<a href="#" data-page="' + (currentPage - 1) + '">上一页</a>';
            }
            
            for (var i = 1; i <= pages; i++) {
                if (i === currentPage) {
                    html += '<span class="current">' + i + '</span>';
                } else {
                    html += '<a href="#" data-page="' + i + '">' + i + '</a>';
                }
            }
            
            if (currentPage < pages) {
                html += '<a href="#" data-page="' + (currentPage + 1) + '">下一页</a>';
            }
            
            html += '</div>';
            
            $('.smartsell-pagination[data-type="' + type + '"]').html(html);
        },
        
        /**
         * 查看聊天记录 - 直接调用后端 API
         */
        viewChatLog: function(chatId) {
            var self = this;
            
            this.apiRequest('/chat/chat_log', 'GET', { chat_id: chatId }, function(response) {
                if (response.success && response.data.data) {
                    var data = response.data.data;
                    var html = '<div class="smartsell-chat-log">';
                    
                    $.each(data.chat_logs, function(i, log) {
                        var className = log.type === 1 ? 'user' : 'ai';
                        html += '<div class="smartsell-chat-message ' + className + '">';
                        html += '<div class="smartsell-chat-bubble">';
                        html += '<div class="smartsell-chat-text">' + self.escapeHtml(log.content) + '</div>';
                        html += '<div class="smartsell-chat-time">' + log.create_time + '</div>';
                        html += '</div>';
                        html += '</div>';
                    });
                    
                    html += '</div>';
                    
                    self.openModal('聊天记录', html);
                } else {
                    self.showNotice('error', '获取聊天记录失败');
                }
            });
        },
        
        /**
         * 查看线索详情 - 直接调用后端 API
         */
        viewInquiry: function(inquiryId) {
            var self = this;
            
            this.apiRequest('/inquiry/show', 'GET', { id: inquiryId }, function(response) {
                if (response.success && response.data.data) {
                    var data = response.data.data;
                    var html = '<div class="smartsell-detail">';
                    html += '<p><strong>联系人：</strong>' + (data.contact_name || '-') + '</p>';
                    html += '<p><strong>联系方式：</strong>' + (data.contact_info || '-') + '</p>';
                    html += '<p><strong>线索信息：</strong>' + (data.inquiry_info || '-') + '</p>';
                    html += '<p><strong>国家/地区：</strong>' + (data.country || '-') + ' / ' + (data.region || '-') + '</p>';
                    html += '<p><strong>标签：</strong>' + (data.tags || '-') + '</p>';
                    html += '<p><strong>备注：</strong>' + (data.remark || '-') + '</p>';
                    html += '<p><strong>创建时间：</strong>' + data.create_time + '</p>';
                    html += '</div>';
                    
                    self.openModal('线索详情', html);
                } else {
                    self.showNotice('error', '获取线索详情失败');
                }
            });
        },
        
        /**
         * 加载会话列表 - 直接调用后端 API
         */
        loadChats: function(page) {
            var self = this;
            page = page || 1;
            
            this.apiRequest('/chat/list', 'GET', { page: page, per_page: 20 }, function(response) {
                if (response.success && response.data.data) {
                    self.renderChatsList(response.data.data, page);
                } else {
                    self.showNotice('error', response.data.message || '获取会话列表失败');
                }
            });
        },
        
        /**
         * 渲染会话列表
         */
        renderChatsList: function(data, currentPage) {
            var self = this;
            var html = '';
            var items = data.items || data.list || [];
            
            if (items.length === 0) {
                html = '<tr><td colspan="6" class="smartsell-empty"><div class="smartsell-empty-icon">💬</div><div class="smartsell-empty-text">暂无会话</div></td></tr>';
            } else {
                $.each(items, function(i, chat) {
                    html += '<tr>';
                    html += '<td>' + chat.id + '</td>';
                    html += '<td>' + self.escapeHtml(chat.visitor_name || chat.session_id || '-') + '</td>';
                    html += '<td>' + (chat.message_count || 0) + '</td>';
                    html += '<td>' + (chat.last_message || '-') + '</td>';
                    html += '<td>' + (chat.create_time || '-') + '</td>';
                    html += '<td><button type="button" class="button smartsell-view-chat" data-id="' + chat.id + '">查看</button></td>';
                    html += '</tr>';
                });
            }
            
            $('#smartsell-chats-list').html(html);
            
            // 更新分页
            var total = data.total || 0;
            var pages = data.pages || Math.ceil(total / 20);
            this.renderPagination('chat', total, pages, currentPage);
        },
        
        /**
         * 加载线索列表 - 直接调用后端 API
         */
        loadInquiries: function(page) {
            var self = this;
            page = page || 1;
            
            this.apiRequest('/inquiry/list', 'GET', { page: page, per_page: 20 }, function(response) {
                if (response.success && response.data.data) {
                    self.renderInquiriesList(response.data.data, page);
                } else {
                    self.showNotice('error', response.data.message || '获取线索列表失败');
                }
            });
        },
        
        /**
         * 渲染线索列表
         */
        renderInquiriesList: function(data, currentPage) {
            var self = this;
            var html = '';
            var items = data.items || data.list || [];
            
            if (items.length === 0) {
                html = '<tr><td colspan="7" class="smartsell-empty"><div class="smartsell-empty-icon">📋</div><div class="smartsell-empty-text">暂无线索</div></td></tr>';
            } else {
                $.each(items, function(i, inquiry) {
                    html += '<tr>';
                    html += '<td>' + inquiry.id + '</td>';
                    html += '<td>' + self.escapeHtml(inquiry.contact_name || '-') + '</td>';
                    html += '<td>' + self.escapeHtml(inquiry.contact_info || '-') + '</td>';
                    html += '<td>' + self.escapeHtml(inquiry.country || '-') + '</td>';
                    html += '<td>' + (inquiry.tags || '-') + '</td>';
                    html += '<td>' + (inquiry.create_time || '-') + '</td>';
                    html += '<td><button type="button" class="button smartsell-view-inquiry" data-id="' + inquiry.id + '">查看</button></td>';
                    html += '</tr>';
                });
            }
            
            $('#smartsell-inquiries-list').html(html);
            
            // 更新分页
            var total = data.total || 0;
            var pages = data.pages || Math.ceil(total / 20);
            this.renderPagination('inquiry', total, pages, currentPage);
        },
        
        /**
         * 加载客户列表 - 直接调用后端 API
         */
        loadCustomers: function(page) {
            var self = this;
            page = page || 1;
            
            this.apiRequest('/customer/list', 'GET', { page: page, per_page: 20 }, function(response) {
                if (response.success && response.data.data) {
                    self.renderCustomersList(response.data.data, page);
                } else {
                    self.showNotice('error', response.data.message || '获取客户列表失败');
                }
            });
        },
        
        /**
         * 渲染客户列表
         */
        renderCustomersList: function(data, currentPage) {
            var self = this;
            var html = '';
            var items = data.items || data.list || [];
            
            if (items.length === 0) {
                html = '<tr><td colspan="7" class="smartsell-empty"><div class="smartsell-empty-icon">👥</div><div class="smartsell-empty-text">暂无客户</div></td></tr>';
            } else {
                $.each(items, function(i, customer) {
                    html += '<tr>';
                    html += '<td>' + customer.id + '</td>';
                    html += '<td>' + self.escapeHtml(customer.name || '-') + '</td>';
                    html += '<td>' + self.escapeHtml(customer.company || '-') + '</td>';
                    html += '<td>' + self.escapeHtml(customer.email || '-') + '</td>';
                    html += '<td>' + self.escapeHtml(customer.country || '-') + '</td>';
                    html += '<td>' + (customer.create_time || '-') + '</td>';
                    html += '<td><button type="button" class="button smartsell-view-customer" data-id="' + customer.id + '">查看</button></td>';
                    html += '</tr>';
                });
            }
            
            $('#smartsell-customers-list').html(html);
            
            // 更新分页
            var total = data.total || 0;
            var pages = data.pages || Math.ceil(total / 20);
            this.renderPagination('customer', total, pages, currentPage);
        },
        
        /**
         * 查看客户详情 - 直接调用后端 API
         */
        viewCustomer: function(customerId) {
            var self = this;
            
            this.apiRequest('/customer/show', 'GET', { id: customerId }, function(response) {
                if (response.success && response.data.data) {
                    var data = response.data.data;
                    var html = '<div class="smartsell-detail">';
                    html += '<p><strong>客户名称：</strong>' + (data.name || '-') + '</p>';
                    html += '<p><strong>公司：</strong>' + (data.company || '-') + '</p>';
                    html += '<p><strong>邮箱：</strong>' + (data.email || '-') + '</p>';
                    html += '<p><strong>电话：</strong>' + (data.phone || '-') + '</p>';
                    html += '<p><strong>国家/地区：</strong>' + (data.country || '-') + '</p>';
                    html += '<p><strong>备注：</strong>' + (data.remark || '-') + '</p>';
                    html += '<p><strong>创建时间：</strong>' + data.create_time + '</p>';
                    html += '</div>';
                    
                    self.openModal('客户详情', html);
                } else {
                    self.showNotice('error', '获取客户详情失败');
                }
            });
        },
        
        /**
         * 打开模态框
         */
        openModal: function(title, content) {
            var html = '<div class="smartsell-modal active">';
            html += '<div class="smartsell-modal-content">';
            html += '<div class="smartsell-modal-header">';
            html += '<h3 class="smartsell-modal-title">' + title + '</h3>';
            html += '<button type="button" class="smartsell-modal-close">&times;</button>';
            html += '</div>';
            html += '<div class="smartsell-modal-body">' + content + '</div>';
            html += '</div>';
            html += '</div>';
            
            $('body').append(html);
        },
        
        /**
         * 上传头像
         */
        uploadAvatar: function() {
            var frame = wp.media({
                title: '选择头像',
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#smartsell-bot-avatar').val(attachment.url);
                $('#smartsell-avatar-preview').attr('src', attachment.url);
            });
            
            frame.open();
        },
        
        /**
         * 显示加载
         */
        showLoading: function() {
            if ($('.smartsell-loading-overlay').length === 0) {
                $('body').append('<div class="smartsell-loading-overlay"><div class="smartsell-spinner"></div></div>');
            }
            $('.smartsell-loading-overlay').show();
        },
        
        /**
         * 隐藏加载
         */
        hideLoading: function() {
            $('.smartsell-loading-overlay').hide();
        },
        
        /**
         * 显示通知
         */
        showNotice: function(type, message) {
            var notice = $('<div class="smartsell-notice smartsell-notice-' + type + '">' + message + '</div>');
            $('.smartsell-wrap').prepend(notice);
            
            setTimeout(function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },
        
        /**
         * HTML 转义
         */
        escapeHtml: function(text) {
            if (!text) return '';
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    // 初始化
    $(document).ready(function() {
        SmartSellAdmin.init();
    });
    
    // 暴露到全局，供其他模块使用
    window.SmartSellAdmin = SmartSellAdmin;

})(jQuery);
