(function() {
  // 从WordPress注入的配置中读取
  const config = window.smartsellChatbot || {};
  const TOKEN = config.token || '';
  const API_BASE_URL = config.apiUrl || '';
  const BOT_NAME = config.botName || 'SmartSell 智能客服';
  const BOT_AVATAR = config.botAvatar || '';
  const BOT_GREETING = config.greeting || '您好，我是SmartSell智能客服，有什么可以帮您解答的问题吗？';

  // 如果没有配置，不加载聊天机器人
  if (!TOKEN || !API_BASE_URL) {
    console.log('SmartSell Chatbot: 配置不完整，跳过加载');
    return;
  }

  // 聊天窗口HTML模板
  const chatHtml = `
    <div class="smartsell-chat-mask" id="smartsell-chat-mask"> 
      <div class="smartsell-chat-window" id="smartsell-chat-window">
        <div class="smartsell-chat-header">
          <div class="smartsell-chat-header-left">
            ${BOT_AVATAR ? `<img src="${BOT_AVATAR}" alt="Avatar" class="smartsell-chat-logo">` : '<div class="smartsell-chat-logo-placeholder">🤖</div>'}
            <span class="smartsell-chat-title">${BOT_NAME}</span>
          </div>
          <div class="smartsell-chat-header-right">
            <button class="smartsell-chat-maximize" id="smartsell-chat-maximize" title="最大化">
              <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M4,4H20V20H4V4M6,8V18H18V8H6Z"></path></svg>
            </button>
            <button class="smartsell-chat-restore" id="smartsell-chat-restore" title="恢复" style="display: none;">
              <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M4,8H8V4H20V16H16V20H4V8M16,8V14H18V6H10V8H16M6,12V18H14V12H6Z"></path></svg>
            </button>
            <button class="smartsell-chat-close" id="smartsell-chat-close" title="关闭">
              <svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path></svg>
            </button>
          </div>
        </div>
        <div class="smartsell-chat-messages" id="smartsell-chat-messages">
          <div class="smartsell-message smartsell-message-ai">
            <div class="smartsell-message-content">
              <div class="smartsell-message-text">${BOT_GREETING}</div>
            </div>
          </div>
        </div>
        <div class="smartsell-chat-input">
          <textarea class="smartsell-chat-textarea" id="smartsell-chat-textarea" placeholder="输入您的问题..."></textarea>
          <button class="smartsell-chat-send" id="smartsell-chat-send">
            <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z"></path></svg>
            发送
          </button>
        </div>
      </div>
    </div>
  `;

  // 聊天按钮HTML模板
  const chatButtonHtml = `
    <div class="smartsell-chat-button" id="smartsell-chat-button">
      <div class="smartsell-chat-button-icon">💬</div>
    </div>
  `;

  // CSS样式
  const css = `
    .smartsell-chat-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      cursor: pointer;
      z-index: 999998;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
      transition: all 0.3s ease;
    }
    .smartsell-chat-button:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }
    .smartsell-chat-button-icon {
      font-size: 28px;
    }
    .smartsell-chat-mask {
      position: fixed;
      bottom: 0;
      right: 0;
      z-index: 999999;
      display: none;
    }
    .smartsell-chat-window {
      display: flex;
      flex-direction: column;
      width: 380px;
      height: 550px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 40px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      margin: 0 20px 20px 0;
      transition: all 0.3s ease;
    }
    .smartsell-chat-window.maximized {
      width: 50vw;
      height: 100vh;
      margin: 0;
      border-radius: 0;
    }
    .smartsell-chat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 56px;
      padding: 0 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
    }
    .smartsell-chat-header-left {
      display: flex;
      align-items: center;
    }
    .smartsell-chat-logo {
      width: 36px;
      height: 36px;
      margin-right: 10px;
      border-radius: 50%;
      object-fit: cover;
    }
    .smartsell-chat-logo-placeholder {
      width: 36px;
      height: 36px;
      margin-right: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      background: rgba(255,255,255,0.2);
      border-radius: 50%;
    }
    .smartsell-chat-title {
      font-size: 15px;
      font-weight: 600;
    }
    .smartsell-chat-header-right {
      display: flex;
      gap: 8px;
    }
    .smartsell-chat-header-right button {
      background: none;
      border: none;
      color: #fff;
      cursor: pointer;
      padding: 6px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .smartsell-chat-header-right button:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }
    .smartsell-chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      background-color: #f8f9fa;
    }
    .smartsell-message {
      display: flex;
      margin-bottom: 16px;
    }
    .smartsell-message-ai {
      justify-content: flex-start;
    }
    .smartsell-message-user {
      justify-content: flex-end;
    }
    .smartsell-message-content {
      max-width: 80%;
      padding: 12px 16px;
      border-radius: 12px;
      position: relative;
    }
    .smartsell-message-ai .smartsell-message-content {
      background-color: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      border-top-left-radius: 4px;
    }
    .smartsell-message-user .smartsell-message-content {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      border-top-right-radius: 4px;
    }
    .smartsell-message-text {
      font-size: 14px;
      line-height: 1.5;
      word-break: break-word;
    }
    .smartsell-message-text p {
      margin: 0 0 8px 0;
    }
    .smartsell-message-text p:last-child {
      margin-bottom: 0;
    }
    /* 商品/资料卡片样式 */
    .smartsell-message-text card {
      display: block;
      max-width: 100%;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin: 12px 0;
      overflow: hidden;
      border: 1px solid #eaeaea;
    }
    .smartsell-message-text card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      display: block;
      border-bottom: 1px solid #f0f0f0;
    }
    .smartsell-message-text card div {
      padding: 10px;
    }
    .smartsell-message-text card span {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 6px;
    }
    .smartsell-message-text card time {
      display: inline-block;
      font-size: 11px;
      color: #999;
    }
    .smartsell-message-text card a {
      display: inline-block;
      float: right;
      font-size: 12px;
      color: #667eea;
      text-decoration: none;
    }
    .smartsell-message-text card a:hover {
      text-decoration: underline;
    }
    .smartsell-chat-input {
      display: flex;
      padding: 12px 16px;
      background-color: #fff;
      border-top: 1px solid #eee;
      gap: 10px;
    }
    .smartsell-chat-textarea {
      flex: 1;
      border: 1px solid #e0e0e0;
      border-radius: 20px;
      padding: 10px 16px;
      resize: none;
      max-height: 100px;
      min-height: 40px;
      outline: none;
      font-family: inherit;
      font-size: 14px;
      transition: border-color 0.2s;
    }
    .smartsell-chat-textarea:focus {
      border-color: #667eea;
    }
    .smartsell-chat-send {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      padding: 10px 20px;
      font-weight: 500;
      font-size: 14px;
      transition: opacity 0.2s, transform 0.2s;
    }
    .smartsell-chat-send:hover {
      opacity: 0.9;
      transform: scale(1.02);
    }
    .smartsell-chat-send svg {
      margin-right: 6px;
    }
    .smartsell-message-loading {
      padding: 16px;
      display: flex;
      justify-content: center;
    }
    .smartsell-dot {
      width: 8px;
      height: 8px;
      margin: 0 4px;
      background-color: #667eea;
      border-radius: 50%;
      display: inline-block;
      animation: smartsell-dot-pulse 1.5s infinite ease-in-out;
    }
    .smartsell-dot:nth-child(2) { animation-delay: 0.3s; }
    .smartsell-dot:nth-child(3) { animation-delay: 0.6s; }
    @keyframes smartsell-dot-pulse {
      0%, 100% { transform: scale(0.8); opacity: 0.5; }
      50% { transform: scale(1.2); opacity: 1; }
    }
    .smartsell-toast {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(-20px);
      background-color: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      z-index: 9999999;
      font-size: 14px;
      opacity: 0;
      transition: opacity 0.3s, transform 0.3s;
    }
    .smartsell-toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }
    @media (max-width: 480px) {
      .smartsell-chat-window {
        width: calc(100vw - 20px);
        height: calc(100vh - 100px);
        margin: 0 10px 10px 10px;
        border-radius: 12px;
      }
      .smartsell-chat-button {
        bottom: 15px;
        right: 15px;
        width: 55px;
        height: 55px;
      }
    }
  `;

  // WebSocket 相关变量
  let socket = null;
  let websocketConfig = null;
  let isWebSocketConnected = false;
  let reconnectTimer = null;
  let heartbeatTimer = null;

  // 添加CSS样式
  function addStyles() {
    const styleEl = document.createElement('style');
    styleEl.id = 'smartsell-chatbot-styles';
    styleEl.textContent = css;
    document.head.appendChild(styleEl);
  }

  // 生成UUID
  function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0;
      const v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  // 获取设备类型
  function getDeviceType() {
    const ua = navigator.userAgent;
    if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) return 'pad';
    if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) return 'mobile';
    return 'pc';
  }

  // 获取浏览器信息
  function getBrowserInfo() {
    const ua = navigator.userAgent;
    let browser = 'Unknown', os = 'Unknown';
    if (ua.includes('Firefox/')) browser = 'Firefox';
    else if (ua.includes('Chrome/')) browser = 'Chrome';
    else if (ua.includes('Safari/') && !ua.includes('Chrome/')) browser = 'Safari';
    else if (ua.includes('Edge/') || ua.includes('Edg/')) browser = 'Edge';
    if (ua.includes('Windows')) os = 'Windows';
    else if (ua.includes('Mac OS X')) os = 'Mac OS';
    else if (ua.includes('Linux')) os = 'Linux';
    else if (ua.includes('Android')) os = 'Android';
    else if (ua.includes('iOS')) os = 'iOS';
    return { browser, os };
  }

  // 安全转义HTML
  function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
  }

  // 格式化消息（支持card标签）
  function formatMessage(text) {
    if (!text) return '';
    
    try {
      // 匹配 <card>...</card> 标签
      const cardRegex = /<card>[\s\S]*?<\/card>/g;
      const parts = [];
      let lastIndex = 0;
      let match;
      
      while ((match = cardRegex.exec(text)) !== null) {
        // 卡片前的文本需要转义
        if (match.index > lastIndex) {
          const beforeCard = text.substring(lastIndex, match.index);
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

  // 滚动到底部
  function scrollToBottom() {
    const chatMessages = document.getElementById('smartsell-chat-messages');
    if (chatMessages) {
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
  }

  // 显示加载动画
  function showLoading() {
    const chatMessages = document.getElementById('smartsell-chat-messages');
    if (!chatMessages) return;
    removeLoading();
    const loadingEl = document.createElement('div');
    loadingEl.className = 'smartsell-message-loading';
    loadingEl.innerHTML = '<div class="smartsell-dot"></div><div class="smartsell-dot"></div><div class="smartsell-dot"></div>';
    chatMessages.appendChild(loadingEl);
    scrollToBottom();
  }

  // 移除加载动画
  function removeLoading() {
    const chatMessages = document.getElementById('smartsell-chat-messages');
    if (!chatMessages) return;
    const loadingEls = chatMessages.querySelectorAll('.smartsell-message-loading');
    loadingEls.forEach(el => el.remove());
  }

  // 显示Toast提示
  function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'smartsell-toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 2000);
    }, 10);
  }

  // 添加消息到聊天窗口
  function addMessage(content, isUser) {
    const chatMessages = document.getElementById('smartsell-chat-messages');
    if (!chatMessages) return;
    
    const messageEl = document.createElement('div');
    messageEl.className = 'smartsell-message ' + (isUser ? 'smartsell-message-user' : 'smartsell-message-ai');
    messageEl.innerHTML = `
      <div class="smartsell-message-content">
        <div class="smartsell-message-text">${formatMessage(content)}</div>
      </div>
    `;
    chatMessages.appendChild(messageEl);
    scrollToBottom();
  }

  // 发送统计数据并获取WebSocket配置
  function sendStatistics() {
    const browserInfo = getBrowserInfo();
    let sessionId = sessionStorage.getItem('smartsell_session_id');

    const statsData = {
      visit_time: new Date().toISOString(),
      ip_address: '',
      source: document.referrer || null,
      entry_page: window.location.href,
      browser: browserInfo.browser,
      browser_language: navigator.language,
      screen_resolution: `${window.screen.width}x${window.screen.height}`,
      os: browserInfo.os,
      device_type: getDeviceType(),
      token: TOKEN,
      session_id: sessionId || null
    };

    fetch(`${API_BASE_URL}/bot/stat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(statsData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.code === 0) {
        const newSessionId = data.data.session_id;
        sessionStorage.setItem('smartsell_session_id', newSessionId);
        
        if (data.data.websocket_host && data.data.websocket_port && data.data.websocket_path) {
          websocketConfig = {
            host: data.data.websocket_host,
            port: data.data.websocket_port,
            path: data.data.websocket_path,
            session_id: newSessionId
          };
          connectWebSocket();
        }
      }
    })
    .catch(error => console.error('SmartSell: 统计发送失败', error));
  }

  // 连接WebSocket
  function connectWebSocket() {
    if (!websocketConfig) return;

    try {
      const wsUrl = `${websocketConfig.host}:${websocketConfig.port}${websocketConfig.path}?session_id=${websocketConfig.session_id}`;
      
      if (socket) {
        try { socket.close(); } catch(e) {}
      }

      socket = new WebSocket(wsUrl);

      socket.onopen = function() {
        isWebSocketConnected = true;
        if (reconnectTimer) {
          clearTimeout(reconnectTimer);
          reconnectTimer = null;
        }
        startHeartbeat();
      };

      socket.onmessage = function(event) {
        try {
          const data = JSON.parse(event.data);
          handleWebSocketMessage(data);
        } catch(e) {
          console.error('SmartSell: 消息解析失败', e);
        }
      };

      socket.onerror = function() {
        isWebSocketConnected = false;
        startReconnect();
      };

      socket.onclose = function() {
        isWebSocketConnected = false;
        stopHeartbeat();
        startReconnect();
      };

    } catch (error) {
      console.error('SmartSell: WebSocket连接失败', error);
      startReconnect();
    }
  }

  // 开始重连
  function startReconnect() {
    if (reconnectTimer) return;
    reconnectTimer = setTimeout(() => {
      reconnectTimer = null;
      if (websocketConfig) {
        connectWebSocket();
      } else {
        sendStatistics();
      }
    }, 5000);
  }

  // 心跳
  function startHeartbeat() {
    stopHeartbeat();
    heartbeatTimer = setInterval(() => {
      if (socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
          type: 'health',
          data: { session_id: sessionStorage.getItem('smartsell_session_id'), timestamp: Date.now() }
        }));
      }
    }, 30000);
  }

  function stopHeartbeat() {
    if (heartbeatTimer) {
      clearInterval(heartbeatTimer);
      heartbeatTimer = null;
    }
  }

  // 处理WebSocket消息
  function handleWebSocketMessage(data) {
    removeLoading();
    
    if (data.code === 0 && data.data && data.data.output) {
      addMessage(data.data.output, false);
    } else if (data.code !== 0) {
      addMessage(data.msg || '抱歉，获取回答失败，请稍后再试', false);
    }
  }

  // 发送消息
  function sendMessage() {
    const textarea = document.getElementById('smartsell-chat-textarea');
    const message = textarea.value.trim();
    if (!message) return;

    // 添加用户消息
    addMessage(message, true);
    textarea.value = '';
    textarea.style.height = 'auto';

    // 显示加载动画
    showLoading();

    // 获取session_id
    let sessionId = sessionStorage.getItem('smartsell_session_id');
    if (!sessionId) {
      sessionId = generateUUID();
      sessionStorage.setItem('smartsell_session_id', sessionId);
    }

    const browserInfo = getBrowserInfo();
    const messageData = {
      token: TOKEN,
      session_id: sessionId,
      input: message,
      ip_address: '',
      entry_page: window.location.href,
      browser: browserInfo.browser,
      browser_language: navigator.language,
      screen_resolution: `${window.screen.width}x${window.screen.height}`,
      os: browserInfo.os,
      device_type: getDeviceType()
    };

    // 优先使用WebSocket
    if (isWebSocketConnected && socket && socket.readyState === WebSocket.OPEN) {
      socket.send(JSON.stringify({ type: 'input', data: messageData }));
      // 通知后台有新消息
      notifyBackendNewMessage(sessionId, message);
    } else {
      // 降级使用HTTP API
      fetch(`${API_BASE_URL}/bot/input`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(messageData)
      })
      .then(response => response.json())
      .then(data => {
        removeLoading();
        if (data.code === 0 && data.data && data.data.output) {
          addMessage(data.data.output, false);
        } else {
          addMessage(data.msg || '抱歉，获取回答失败，请稍后再试', false);
        }
        // 通知后台有新消息
        notifyBackendNewMessage(sessionId, message);
      })
      .catch(error => {
        removeLoading();
        addMessage('网络错误，请稍后再试', false);
        console.error('SmartSell: 发送消息失败', error);
      });
    }
  }
  
  // 通知后台有新消息（触发后台实时推送）
  function notifyBackendNewMessage(sessionId, message) {
    // 使用WordPress AJAX通知后台
    if (typeof window.smartsellChatbot !== 'undefined' && window.smartsellChatbot.ajaxUrl) {
      fetch(window.smartsellChatbot.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'smartsell_notify_message',
          session_id: sessionId,
          message: message
        })
      }).catch(err => {
        console.log('SmartSell: 消息通知失败', err);
      });
    }
  }

  // 嵌入聊天机器人
  function embedChatbot() {
    addStyles();

    // 添加聊天按钮
    const buttonContainer = document.createElement('div');
    buttonContainer.innerHTML = chatButtonHtml;
    document.body.appendChild(buttonContainer);

    // 添加聊天窗口
    const chatContainer = document.createElement('div');
    chatContainer.innerHTML = chatHtml;
    document.body.appendChild(chatContainer);

    // 获取DOM元素
    const chatButton = document.getElementById('smartsell-chat-button');
    const chatMask = document.getElementById('smartsell-chat-mask');
    const chatWindow = document.getElementById('smartsell-chat-window');
    const chatMaximize = document.getElementById('smartsell-chat-maximize');
    const chatRestore = document.getElementById('smartsell-chat-restore');
    const chatClose = document.getElementById('smartsell-chat-close');
    const chatTextarea = document.getElementById('smartsell-chat-textarea');
    const chatSend = document.getElementById('smartsell-chat-send');

    // 打开聊天窗口
    chatButton.addEventListener('click', () => {
      chatButton.style.display = 'none';
      chatMask.style.display = 'block';
    });

    // 关闭聊天窗口
    chatClose.addEventListener('click', () => {
      chatMask.style.display = 'none';
      chatButton.style.display = 'flex';
      chatWindow.classList.remove('maximized');
      chatMaximize.style.display = 'block';
      chatRestore.style.display = 'none';
    });

    // 最大化
    chatMaximize.addEventListener('click', () => {
      chatWindow.classList.add('maximized');
      chatMaximize.style.display = 'none';
      chatRestore.style.display = 'block';
    });

    // 恢复
    chatRestore.addEventListener('click', () => {
      chatWindow.classList.remove('maximized');
      chatMaximize.style.display = 'block';
      chatRestore.style.display = 'none';
    });

    // 自动调整textarea高度
    chatTextarea.addEventListener('input', () => {
      chatTextarea.style.height = 'auto';
      chatTextarea.style.height = Math.min(chatTextarea.scrollHeight, 100) + 'px';
    });

    // 发送按钮
    chatSend.addEventListener('click', sendMessage);

    // Enter键发送
    chatTextarea.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  }

  // 页面加载完毕后执行
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    embedChatbot();
    sendStatistics();
  }

  // 页面卸载前关闭WebSocket
  window.addEventListener('beforeunload', () => {
    if (socket && socket.readyState === WebSocket.OPEN) {
      socket.close();
    }
  });
})();
