// Floating chatbot widget - injected on every page via footer.php
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.createElement('div');
    root.id = 'agriChatRoot';
    root.innerHTML = `
      <button id="agriChatToggle" title="Ask the Agri Assistant">🌱💬</button>
      <div id="agriChatWindow" class="d-none">
        <div id="agriChatHeader">
          <span>🌱 Agri Assistant</span>
          <button id="agriChatClose" aria-label="Close">✕</button>
        </div>
        <div id="agriChatMessages"></div>
        <form id="agriChatForm">
          <input type="text" id="agriChatInput" placeholder="Ask about crops, weather, reports..." autocomplete="off">
          <button type="submit">➤</button>
        </form>
      </div>`;
    document.body.appendChild(root);

    const toggle = document.getElementById('agriChatToggle');
    const win = document.getElementById('agriChatWindow');
    const messages = document.getElementById('agriChatMessages');
    const form = document.getElementById('agriChatForm');
    const input = document.getElementById('agriChatInput');

    function addMessage(text, sender) {
      const bubble = document.createElement('div');
      bubble.className = 'agri-chat-bubble ' + sender;
      bubble.textContent = text;
      messages.appendChild(bubble);
      messages.scrollTop = messages.scrollHeight;
    }

    let greeted = false;
    toggle.addEventListener('click', function () {
      win.classList.toggle('d-none');
      if (!win.classList.contains('d-none') && !greeted) {
        addMessage("Hi! I can help with crop info, weather, planting logs and yield reports. What do you need?", 'bot');
        greeted = true;
      }
    });
    document.getElementById('agriChatClose').addEventListener('click', () => win.classList.add('d-none'));

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      addMessage(text, 'user');
      input.value = '';
      const typing = document.createElement('div');
      typing.className = 'agri-chat-bubble bot';
      typing.textContent = '…';
      typing.id = 'agriTyping';
      messages.appendChild(typing);
      messages.scrollTop = messages.scrollHeight;

      fetch(BASE_URL + '/api/chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: text })
      })
        .then(res => res.json())
        .then(data => {
          document.getElementById('agriTyping')?.remove();
          addMessage(data.reply || "Sorry, I couldn't process that.", 'bot');
        })
        .catch(() => {
          document.getElementById('agriTyping')?.remove();
          addMessage("Something went wrong reaching the assistant.", 'bot');
        });
    });
  });
})();
