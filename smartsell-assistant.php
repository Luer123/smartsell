<?php
/**
 * Plugin Name: SmartSell客服
 * Plugin URI: https://smartsell.cloud
 * Description: 一个用于在 WordPress 网站上接入AI助手的插件
 * Version: 1.0.0
 * Author: 数字探索
 * Author URI: https://smartsell.cloud
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smartsell-assistant
 * Domain Path: /languages
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('SMARTSELL_VERSION', '1.0.0');
define('SMARTSELL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMARTSELL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SMARTSELL_PLUGIN_BASENAME', plugin_basename(__FILE__));

// 默认 API 地址（线上环境）
define('SMARTSELL_DEFAULT_API_URL', 'https://app.daxiang.cloud/api');

/**
 * 主插件类
 */
class SmartSell_Assistant {
    
    /**
     * 单例实例
     */
    private static $instance = null;
    
    /**
     * API URL
     */
    private $api_url;
    
    /**
     * 是否已登录
     */
    private $is_logged_in = false;
    
    /**
     * 获取单例实例
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 构造函数
     */
    private function __construct() {
        // API URL 直接使用常量配置，不再从数据库读取
        $this->api_url = SMARTSELL_DEFAULT_API_URL;
        $this->is_logged_in = !empty(get_option('smartsell_api_token', ''));
        
        // 加载文本域
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // 初始化管理菜单
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // 注册设置
        add_action('admin_init', array($this, 'register_settings'));
        
        // 加载管理样式和脚本
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // 在前端加载聊天脚本
        add_action('wp_footer', array($this, 'load_chat_script'));
        
        // 注册 AJAX 处理
        add_action('wp_ajax_smartsell_api_request', array($this, 'handle_api_request'));
        add_action('wp_ajax_smartsell_sync_posts', array($this, 'handle_sync_posts'));
        add_action('wp_ajax_smartsell_sync_products', array($this, 'handle_sync_products'));
        add_action('wp_ajax_smartsell_load_posts', array($this, 'handle_load_posts'));
        add_action('wp_ajax_smartsell_load_products', array($this, 'handle_load_products'));
        add_action('wp_ajax_smartsell_save_bot_settings', array($this, 'handle_save_bot_settings'));
        add_action('wp_ajax_smartsell_save_api_settings', array($this, 'handle_save_api_settings'));
        add_action('wp_ajax_smartsell_save_login', array($this, 'handle_save_login'));
        add_action('wp_ajax_smartsell_logout', array($this, 'handle_logout'));
        add_action('wp_ajax_smartsell_get_posts', array($this, 'handle_get_posts'));
        add_action('wp_ajax_smartsell_get_products', array($this, 'handle_get_products'));
        
        // 实时会话管理 AJAX 处理
        add_action('wp_ajax_smartsell_check_new_messages', array($this, 'handle_check_new_messages'));
        add_action('wp_ajax_smartsell_update_session_status', array($this, 'handle_update_session_status'));
        add_action('wp_ajax_nopriv_smartsell_notify_message', array($this, 'handle_notify_message'));
        
        // 激活和停用钩子
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * 检查是否已登录
     */
    public function is_logged_in() {
        return $this->is_logged_in;
    }
    
    /**
     * 检查登录状态，未登录则渲染登录页面
     */
    private function require_login() {
        if (!$this->is_logged_in) {
            include SMARTSELL_PLUGIN_DIR . 'templates/login.php';
            return false;
        }
        return true;
    }
    
    /**
     * 加载文本域
     */
    public function load_textdomain() {
        load_plugin_textdomain('smartsell-assistant', false, dirname(SMARTSELL_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        // 主菜单
        add_menu_page(
            __('SmartSell客服', 'smartsell-assistant'),
            __('SmartSell客服', 'smartsell-assistant'),
            'manage_options',
            'smartsell-assistant',
            $this->is_logged_in ? '' : array($this, 'render_login_page'),  // 登录后主菜单不需要回调
            'dashicons-format-chat',
            30
        );
        
        // 如果未登录，只显示登录菜单
        if (!$this->is_logged_in) {
            // 移除重复的主菜单项
            remove_submenu_page('smartsell-assistant', 'smartsell-assistant');
            
            // 登录菜单
            add_submenu_page(
                'smartsell-assistant',
                __('账户登录', 'smartsell-assistant'),
                __('账户登录', 'smartsell-assistant'),
                'manage_options',
                'smartsell-login',
                array($this, 'render_login_page')
            );
            return;
        }
        
        // 已登录状态显示所有菜单
        // 移除 WordPress 自动创建的与主菜单同名的子菜单
        remove_submenu_page('smartsell-assistant', 'smartsell-assistant');
        
        // 会话管理作为第一个子菜单
        add_submenu_page(
            'smartsell-assistant',
            __('会话管理', 'smartsell-assistant'),
            __('会话管理', 'smartsell-assistant'),
            'manage_options',
            'smartsell-chat',
            array($this, 'render_chat_page')
        );
        
        add_submenu_page(
            'smartsell-assistant',
            __('线索追踪', 'smartsell-assistant'),
            __('线索追踪', 'smartsell-assistant'),
            'manage_options',
            'smartsell-inquiry',
            array($this, 'render_inquiry_page')
        );
        
        add_submenu_page(
            'smartsell-assistant',
            __('线索跟进日志', 'smartsell-assistant'),
            __('线索跟进日志', 'smartsell-assistant'),
            'manage_options',
            'smartsell-inquiry-follow',
            array($this, 'render_inquiry_follow_page')
        );
        
        // 客户区子菜单
        add_submenu_page(
            'smartsell-assistant',
            __('客户管理', 'smartsell-assistant'),
            __('客户管理', 'smartsell-assistant'),
            'manage_options',
            'smartsell-customer',
            array($this, 'render_customer_page')
        );
        
        add_submenu_page(
            'smartsell-assistant',
            __('客户跟进日志', 'smartsell-assistant'),
            __('客户跟进日志', 'smartsell-assistant'),
            'manage_options',
            'smartsell-customer-follow',
            array($this, 'render_customer_follow_page')
        );
        
        // 标签管理
        add_submenu_page(
            'smartsell-assistant',
            __('标签管理', 'smartsell-assistant'),
            __('标签管理', 'smartsell-assistant'),
            'manage_options',
            'smartsell-tags',
            array($this, 'render_tags_page')
        );
        
        // AI知识库子菜单
        add_submenu_page(
            'smartsell-assistant',
            __('文章同步', 'smartsell-assistant'),
            __('文章同步', 'smartsell-assistant'),
            'manage_options',
            'smartsell-posts-sync',
            array($this, 'render_posts_sync_page')
        );
        
        add_submenu_page(
            'smartsell-assistant',
            __('商品同步', 'smartsell-assistant'),
            __('商品同步', 'smartsell-assistant'),
            'manage_options',
            'smartsell-products-sync',
            array($this, 'render_products_sync_page')
        );
        
        // 机器人配置
        add_submenu_page(
            'smartsell-assistant',
            __('机器人配置', 'smartsell-assistant'),
            __('机器人配置', 'smartsell-assistant'),
            'manage_options',
            'smartsell-bot-settings',
            array($this, 'render_bot_settings_page')
        );
        
        // 个人中心
        add_submenu_page(
            'smartsell-assistant',
            __('个人中心', 'smartsell-assistant'),
            __('个人中心', 'smartsell-assistant'),
            'manage_options',
            'smartsell-profile',
            array($this, 'render_profile_page')
        );
        
        // 退出登录
        add_submenu_page(
            'smartsell-assistant',
            __('退出登录', 'smartsell-assistant'),
            __('退出登录', 'smartsell-assistant'),
            'manage_options',
            'smartsell-logout',
            array($this, 'render_logout_page')
        );
        
        // 移除重复的主菜单项
        remove_submenu_page('smartsell-assistant', 'smartsell-assistant');
    }
    
    /**
     * 注册设置
     */
    public function register_settings() {
        // API 设置
        register_setting('smartsell_settings', 'smartsell_api_url');
        register_setting('smartsell_settings', 'smartsell_api_token');
        
        // 机器人设置
        register_setting('smartsell_bot_settings', 'smartsell_bot_avatar');
        register_setting('smartsell_bot_settings', 'smartsell_bot_name');
        register_setting('smartsell_bot_settings', 'smartsell_bot_greeting');
        register_setting('smartsell_bot_settings', 'smartsell_bot_enabled');
    }
    
    /**
     * 加载管理资源
     */
    public function enqueue_admin_assets($hook) {
        // 只在插件页面加载
        if (strpos($hook, 'smartsell') === false) {
            return;
        }
        
        // 加载 CSS
        wp_enqueue_style(
            'smartsell-admin',
            SMARTSELL_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SMARTSELL_VERSION
        );
        
        // 加载 JS
        wp_enqueue_script(
            'smartsell-admin',
            SMARTSELL_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            SMARTSELL_VERSION,
            true
        );
        
        // 本地化脚本
        wp_localize_script('smartsell-admin', 'smartsellAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('smartsell_nonce'),
            'apiUrl' => $this->api_url,
            'token' => get_option('smartsell_api_token', ''),
            'i18n' => array(
                'loading' => __('加载中...', 'smartsell-assistant'),
                'error' => __('操作失败', 'smartsell-assistant'),
                'success' => __('操作成功', 'smartsell-assistant'),
                'confirm' => __('确定要执行此操作吗？', 'smartsell-assistant'),
                'syncSuccess' => __('同步成功', 'smartsell-assistant'),
                'syncFailed' => __('同步失败', 'smartsell-assistant'),
            )
        ));
        
        // 媒体上传器
        wp_enqueue_media();
    }
    
    /**
     * 在前端加载聊天脚本
     */
    public function load_chat_script() {
        $app_token = get_option('smartsell_app_token', '');
        
        // 只需要有 app_token 即可，插件启用后自动注入
        if (empty($app_token)) {
            return;
        }
        
        // 获取机器人设置
        $bot_settings = get_option('smartsell_bot_settings', array());
        $bot_name = isset($bot_settings['name']) ? $bot_settings['name'] : get_option('smartsell_bot_name', 'SmartSell 智能客服');
        $bot_avatar = isset($bot_settings['avatar']) ? $bot_settings['avatar'] : get_option('smartsell_bot_avatar', '');
        $bot_greeting = isset($bot_settings['greeting']) ? $bot_settings['greeting'] : get_option('smartsell_bot_greeting', '您好，我是SmartSell智能客服，有什么可以帮您解答的问题吗？');
        
        // 加载本地 chatbot.js
        wp_enqueue_script(
            'smartsell-chatbot',
            SMARTSELL_PLUGIN_URL . 'assets/js/chatbot.js',
            array(),
            SMARTSELL_VERSION,
            true
        );
        
        // 传递配置到 JavaScript
        wp_localize_script('smartsell-chatbot', 'smartsellChatbot', array(
            'token' => $app_token,  // 使用 app_token（应用token）而不是 api_token（用户token）
            'apiUrl' => $this->api_url,
            'botName' => $bot_name,
            'botAvatar' => $bot_avatar,
            'greeting' => $bot_greeting,
            'ajaxUrl' => admin_url('admin-ajax.php'),  // 添加 AJAX URL
        ));
    }
    
    /**
     * 渲染仪表板页面
     */
    public function render_dashboard_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/dashboard.php';
    }
    
    /**
     * 渲染会话管理页面
     */
    public function render_chat_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/chat.php';
    }
    
    /**
     * 渲染线索追踪页面
     */
    public function render_inquiry_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/inquiry.php';
    }
    
    /**
     * 渲染线索跟进日志页面
     */
    public function render_inquiry_follow_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/inquiry-follow.php';
    }
    
    /**
     * 渲染客户管理页面
     */
    public function render_customer_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/customer.php';
    }
    
    /**
     * 渲染客户跟进日志页面
     */
    public function render_customer_follow_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/customer-follow.php';
    }
    
    /**
     * 渲染标签管理页面
     */
    public function render_tags_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/tags.php';
    }
    
    /**
     * 渲染文章同步页面
     */
    public function render_posts_sync_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/posts-sync.php';
    }
    
    /**
     * 渲染商品同步页面
     */
    public function render_products_sync_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/products-sync.php';
    }
    
    /**
     * 渲染机器人配置页面
     */
    public function render_bot_settings_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/bot-settings.php';
    }
    
    /**
     * 渲染个人中心页面
     */
    public function render_profile_page() {
        if (!$this->require_login()) return;
        include SMARTSELL_PLUGIN_DIR . 'templates/profile.php';
    }
    
    /**
     * 渲染登录页面（不需要登录验证）
     */
    public function render_login_page() {
        include SMARTSELL_PLUGIN_DIR . 'templates/login.php';
    }
    
    /**
     * 渲染退出登录页面
     */
    public function render_logout_page() {
        // 清除登录信息
        delete_option('smartsell_api_token');
        delete_option('smartsell_app_token');
        delete_option('smartsell_user_id');
        delete_option('smartsell_app_id');
        delete_option('smartsell_username');
        delete_option('smartsell_real_name');
        delete_option('smartsell_login_time');
        
        // 重定向到登录页面
        wp_redirect(admin_url('admin.php?page=smartsell-assistant'));
        exit;
    }
    
    /**
     * 处理 API 请求
     */
    public function handle_api_request() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $endpoint = isset($_POST['endpoint']) ? sanitize_text_field($_POST['endpoint']) : '';
        $method = isset($_POST['method']) ? sanitize_text_field($_POST['method']) : 'GET';
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        $content_type = isset($_POST['contentType']) ? sanitize_text_field($_POST['contentType']) : 'json';
        
        if (empty($endpoint)) {
            wp_send_json_error(array('message' => __('无效的请求', 'smartsell-assistant')));
        }
        
        $response = $this->api_request($endpoint, $method, $data, $content_type);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        wp_send_json_success($response);
    }
    
    /**
     * 处理文章同步
     */
    public function handle_sync_posts() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : array();
        
        if (empty($post_ids)) {
            wp_send_json_error(array('message' => __('请选择要同步的文章', 'smartsell-assistant')));
        }
        
        $synced = array();
        $failed = array();
        
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            if (!$post) {
                $failed[] = $post_id;
                continue;
            }
            
            // 准备同步数据
            $sync_data = array(
                'title' => $post->post_title,
                'content' => wp_strip_all_tags($post->post_content),
                'url' => get_permalink($post_id),
                'type' => 'article',
                'source' => 'wordpress',
                'source_id' => $post_id,
            );
            
            // 调用 API 同步
            $response = $this->api_request('/documents/add', 'POST', $sync_data);
            
            // 部分后端会返回字符串 "0"，显式转为 int 再判断
            $response_code = isset($response['code']) ? intval($response['code']) : null;
            if (is_wp_error($response) || ($response_code !== null && $response_code !== 0)) {
                $failed[] = $post_id;
            } else {
                $synced[] = $post_id;
                // 标记已同步（按app_id存储）
                $app_id = get_option('smartsell_app_id', 0);
                if ($app_id > 0) {
                    update_post_meta($post_id, '_smartsell_synced_' . $app_id, 1);
                    update_post_meta($post_id, '_smartsell_sync_time_' . $app_id, current_time('mysql'));
                }
            }
        }
        
        wp_send_json_success(array(
            'synced' => $synced,
            'failed' => $failed,
            'message' => sprintf(__('成功同步 %d 篇文章，失败 %d 篇', 'smartsell-assistant'), count($synced), count($failed))
        ));
    }
    
    /**
     * 处理商品同步
     */
    public function handle_sync_products() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $product_ids = isset($_POST['product_ids']) ? array_map('intval', $_POST['product_ids']) : array();
        
        if (empty($product_ids)) {
            wp_send_json_error(array('message' => __('请选择要同步的商品', 'smartsell-assistant')));
        }
        
        // 检查 WooCommerce 是否激活
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('需要安装并激活 WooCommerce', 'smartsell-assistant')));
        }
        
        $synced = array();
        $failed = array();
        $products_to_sync = array();
        
        // 收集所有商品数据
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) {
                $failed[] = $product_id;
                continue;
            }
            
            // 获取图片URL，如果没有则为null
            $image_url = wp_get_attachment_url($product->get_image_id());
            $product_url = get_permalink($product_id);
            
            // 准备同步数据 - 符合后端 ProductSyncItem 模型
            $products_to_sync[] = array(
                'product_code' => (string)$product_id,  // 使用商品ID作为编号
                'product_name' => $product->get_name(),
                'product_description' => wp_strip_all_tags($product->get_description() . ' ' . $product->get_short_description()),
                'product_price' => $product->get_price() ? floatval($product->get_price()) : null,
                'product_image' => $image_url ? $image_url : null,
                'product_url' => $product_url ? $product_url : null,
            );
        }
        
        if (!empty($products_to_sync)) {
            // 调用批量同步 API（JSON 格式）
            $sync_data = array('products' => $products_to_sync);
            $response = $this->api_request('/product/sync', 'POST', $sync_data, 'json');
            
            // 调试日志
            error_log('SmartSell Sync Request: ' . json_encode($sync_data));
            error_log('SmartSell Sync Response: ' . json_encode($response));
            
            // 部分后端会返回字符串 "0"，显式转为 int 再判断
            $response_code = isset($response['code']) ? intval($response['code']) : null;
            
            if (is_wp_error($response)) {
                // WP 错误
                $failed = array_merge($failed, $product_ids);
                error_log('SmartSell Sync WP Error: ' . $response->get_error_message());
            } elseif ($response_code !== null && $response_code !== 0) {
                // API 返回错误
                $failed = array_merge($failed, $product_ids);
                error_log('SmartSell Sync API Error: ' . json_encode($response));
            } else {
                // 同步成功，标记所有商品（按app_id存储）
                $app_id = get_option('smartsell_app_id', 0);
                foreach ($product_ids as $product_id) {
                    if (!in_array($product_id, $failed)) {
                        $synced[] = $product_id;
                        if ($app_id > 0) {
                            update_post_meta($product_id, '_smartsell_synced_' . $app_id, 1);
                            update_post_meta($product_id, '_smartsell_sync_time_' . $app_id, current_time('mysql'));
                        }
                    }
                }
            }
        }
        
        wp_send_json_success(array(
            'synced' => $synced,
            'failed' => $failed,
            'message' => sprintf(__('成功同步 %d 个商品，失败 %d 个', 'smartsell-assistant'), count($synced), count($failed))
        ));
    }
    
    /**
     * 获取文章列表
     */
    public function handle_get_posts() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 20;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            's' => $search,
        );
        
        $query = new WP_Query($args);
        $posts = array();
        
        // 获取当前app_id
        $app_id = get_option('smartsell_app_id', 0);
        
        foreach ($query->posts as $post) {
            // 按app_id查询同步状态
            $synced = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_synced_' . $app_id, true) : false;
            $sync_time = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_sync_time_' . $app_id, true) : '';
            
            $posts[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'date' => $post->post_date,
                'author' => get_the_author_meta('display_name', $post->post_author),
                'synced' => $synced ? true : false,
                'sync_time' => $sync_time ? $sync_time : '',
            );
        }
        
        wp_send_json_success(array(
            'posts' => $posts,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ));
    }
    
    /**
     * 获取商品列表
     */
    public function handle_get_products() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        // 检查 WooCommerce 是否激活
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('需要安装并激活 WooCommerce', 'smartsell-assistant')));
        }
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 20;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            's' => $search,
        );
        
        $query = new WP_Query($args);
        $products = array();
        
        // 获取当前app_id
        $app_id = get_option('smartsell_app_id', 0);
        
        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);
            // 按app_id查询同步状态
            $synced = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_synced_' . $app_id, true) : false;
            $sync_time = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_sync_time_' . $app_id, true) : '';
            
            $products[] = array(
                'id' => $post->ID,
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'sku' => $product->get_sku(),
                'image' => wp_get_attachment_url($product->get_image_id()),
                'synced' => $synced ? true : false,
                'sync_time' => $sync_time ? $sync_time : '',
            );
        }
        
        wp_send_json_success(array(
            'products' => $products,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ));
    }
    
    /**
     * 加载文章列表（用于同步页面）
     */
    public function handle_load_posts() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
        $per_page = 20;
        
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
        );
        
        if ($category > 0) {
            $args['cat'] = $category;
        }
        
        if (!empty($keyword)) {
            $args['s'] = $keyword;
        }
        
        $query = new WP_Query($args);
        $posts = array();
        
        // 获取当前app_id
        $app_id = get_option('smartsell_app_id', 0);
        
        foreach ($query->posts as $post) {
            $categories = get_the_category($post->ID);
            $category_names = array_map(function($cat) {
                return $cat->name;
            }, $categories);
            
            // 按app_id获取同步状态
            $synced = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_synced_' . $app_id, true) : false;
            $sync_time = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_sync_time_' . $app_id, true) : '';
            
            $posts[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'date' => get_the_date('Y-m-d H:i', $post),
                'author' => get_the_author_meta('display_name', $post->post_author),
                'categories' => implode(', ', $category_names),
                'edit_link' => get_edit_post_link($post->ID, 'raw'),
                'synced' => $synced ? true : false,
                'sync_time' => $sync_time ? $sync_time : '',
            );
        }
        
        wp_send_json_success(array(
            'posts' => $posts,
            'total' => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $page,
        ));
    }
    
    /**
     * 加载商品列表（用于同步页面）
     */
    public function handle_load_products() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        // 检查 WooCommerce 是否激活
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('需要安装并激活 WooCommerce', 'smartsell-assistant')));
        }
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $category = isset($_POST['category']) ? intval($_POST['category']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
        $per_page = 20;
        
        $args = array(
            'post_type' => 'product',
            'post_status' => !empty($status) ? $status : array('publish', 'draft'),
            'posts_per_page' => $per_page,
            'paged' => $page,
        );
        
        if ($category > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category,
                ),
            );
        }
        
        if (!empty($keyword)) {
            $args['s'] = $keyword;
        }
        
        $query = new WP_Query($args);
        $products = array();
        
        // 获取当前app_id
        $app_id = get_option('smartsell_app_id', 0);
        
        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);
            $categories = wc_get_product_category_list($post->ID, ', ');
            
            // 按app_id获取同步状态
            $synced = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_synced_' . $app_id, true) : false;
            $sync_time = $app_id > 0 ? get_post_meta($post->ID, '_smartsell_sync_time_' . $app_id, true) : '';
            
            $products[] = array(
                'ID' => $post->ID,
                'title' => $product->get_name(),
                'sku' => $product->get_sku(),
                'price' => $product->get_price_html(),
                'categories' => strip_tags($categories),
                'image' => wp_get_attachment_url($product->get_image_id()),
                'status' => $post->post_status,
                'edit_link' => get_edit_post_link($post->ID, 'raw'),
                'synced' => $synced ? true : false,
                'sync_time' => $sync_time ? $sync_time : '',
            );
        }
        
        wp_send_json_success(array(
            'products' => $products,
            'total' => $query->found_posts,
            'total_pages' => $query->max_num_pages,
            'current_page' => $page,
        ));
    }
    
    /**
     * 保存机器人设置
     */
    public function handle_save_bot_settings() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        
        // 保存机器人外观设置
        $bot_settings = array(
            'avatar' => isset($settings['avatar']) ? esc_url_raw($settings['avatar']) : '',
            'name' => isset($settings['name']) ? sanitize_text_field($settings['name']) : 'SmartSell客服',
            'greeting' => isset($settings['greeting']) ? sanitize_textarea_field($settings['greeting']) : '',
        );
        
        update_option('smartsell_bot_settings', $bot_settings);
        
        wp_send_json_success(array('message' => __('设置已保存', 'smartsell-assistant')));
    }
    
    /**
     * 保存 API 设置
     */
    public function handle_save_api_settings() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : SMARTSELL_DEFAULT_API_URL;
        $api_token = isset($_POST['api_token']) ? sanitize_text_field($_POST['api_token']) : '';
        
        update_option('smartsell_api_url', $api_url);
        update_option('smartsell_api_token', $api_token);
        
        // 更新实例中的 API URL
        $this->api_url = $api_url;
        
        wp_send_json_success(array('message' => __('设置已保存', 'smartsell-assistant')));
    }
    
    /**
     * 保存登录信息
     */
    public function handle_save_login() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
        $app_token = isset($_POST['app_token']) ? sanitize_text_field($_POST['app_token']) : '';
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
        $real_name = isset($_POST['real_name']) ? sanitize_text_field($_POST['real_name']) : '';
        
        if (empty($token)) {
            wp_send_json_error(array('message' => __('Token不能为空', 'smartsell-assistant')));
        }
        
        // 临时保存token以获取用户信息
        $old_token = get_option('smartsell_api_token', '');
        update_option('smartsell_api_token', $token);
        
        // 获取用户信息（包含app_id）
        $user_info = $this->api_request('/user/me', 'GET');
        $app_id = 0;
        
        if (!is_wp_error($user_info) && isset($user_info['code']) && $user_info['code'] === 0 && isset($user_info['data']['app_id'])) {
            $app_id = intval($user_info['data']['app_id']);
        }
        
        // 如果获取失败，恢复旧token
        if (is_wp_error($user_info) || !isset($user_info['code']) || $user_info['code'] !== 0) {
            update_option('smartsell_api_token', $old_token);
            wp_send_json_error(array('message' => __('获取用户信息失败', 'smartsell-assistant')));
        }
        
        // 保存登录信息（API URL 使用常量配置，不保存到数据库）
        update_option('smartsell_api_token', $token);
        update_option('smartsell_app_token', $app_token);  // 应用token，用于前端机器人
        update_option('smartsell_user_id', $user_id);
        update_option('smartsell_app_id', $app_id);  // 应用ID，用于区分同步状态
        update_option('smartsell_username', $username);
        update_option('smartsell_real_name', $real_name);
        update_option('smartsell_login_time', current_time('mysql'));
        
        wp_send_json_success(array('message' => __('登录成功', 'smartsell-assistant')));
    }
    
    /**
     * 退出登录
     */
    public function handle_logout() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        // 清除登录信息
        delete_option('smartsell_api_token');
        delete_option('smartsell_app_token');
        delete_option('smartsell_user_id');
        delete_option('smartsell_username');
        delete_option('smartsell_real_name');
        delete_option('smartsell_login_time');
        
        wp_send_json_success(array('message' => __('已退出登录', 'smartsell-assistant')));
    }
    
    /**
     * 发送 API 请求
     * @param string $endpoint API 端点
     * @param string $method HTTP 方法
     * @param array $data 请求数据
     * @param string $content_type 内容类型 'json' 或 'form'
     */
    private function api_request($endpoint, $method = 'GET', $data = array(), $content_type = 'json') {
        $token = get_option('smartsell_api_token', '');
        
        $url = $this->api_url . $endpoint;
        
        $args = array(
            'method' => $method,
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
            ),
            // 开发环境禁用 SSL 验证（生产环境请删除此行）
            'sslverify' => false,
        );
        
        // 根据内容类型设置请求格式
        if ($content_type === 'form') {
            $args['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
            if ($method !== 'GET' && !empty($data)) {
                $args['body'] = http_build_query($data);
            }
        } else {
            $args['headers']['Content-Type'] = 'application/json';
            if ($method !== 'GET' && !empty($data)) {
                $args['body'] = json_encode($data);
            }
        }
        
        if ($method === 'GET' && !empty($data)) {
            $url = add_query_arg($data, $url);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
    
    /**
     * 激活插件
     */
    public function activate() {
        // 设置默认选项（API URL 使用常量配置，不存入数据库）
        add_option('smartsell_bot_enabled', '0');
        add_option('smartsell_bot_name', 'SmartSell 智能客服');
        add_option('smartsell_bot_greeting', '您好，我是SmartSell智能客服，有什么可以帮您解答的问题吗？');
        
        // 刷新重写规则
        flush_rewrite_rules();
    }
    
    /**
     * 停用插件
     */
    public function deactivate() {
        // 刷新重写规则
        flush_rewrite_rules();
    }
    
    /**
     * 检查新消息
     */
    public function handle_check_new_messages() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $last_check_time = isset($_POST['last_check_time']) ? sanitize_text_field($_POST['last_check_time']) : '';
        $current_chat_id = isset($_POST['current_chat_id']) ? intval($_POST['current_chat_id']) : 0;
        
        // 调用API获取新消息
        $params = array();
        if (!empty($last_check_time)) {
            $params['since'] = $last_check_time;
        }
        
        $response = $this->api_request('/chat/check-updates', 'GET', $params);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        // 返回新消息和更新的会话列表
        wp_send_json_success(array(
            'updates' => isset($response['data']) ? $response['data'] : array(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * 更新会话状态（在线/离线）
     */
    public function handle_update_session_status() {
        check_ajax_referer('smartsell_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('权限不足', 'smartsell-assistant')));
        }
        
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'online';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => __('会话ID不能为空', 'smartsell-assistant')));
        }
        
        // 调用API更新状态
        $response = $this->api_request('/chat/update-status', 'POST', array(
            'session_id' => $session_id,
            'status' => $status
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        wp_send_json_success($response);
    }
    
    /**
     * 前台消息通知（无需登录）
     */
    public function handle_notify_message() {
        // 接收前台发送的消息通知
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => 'Invalid session'));
        }
        
        // 将消息通知存储到临时选项中（使用transient，30秒过期）
        $notification_key = 'smartsell_msg_notification_' . md5($session_id);
        set_transient($notification_key, array(
            'session_id' => $session_id,
            'message' => $message,
            'timestamp' => time()
        ), 30);
        
        wp_send_json_success(array('message' => 'Notification received'));
    }
}

// 初始化插件
SmartSell_Assistant::get_instance();
