const input = document.getElementById('chatInput')
const button = document.getElementById('sendBtn')
const chatBody = document.getElementById('chatBody')

// Enviar con botón
button.addEventListener('click', sendMessage)

// Enviar con Enter
input.addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    sendMessage()
  }
})

function sendMessage() {
  const message = input.value.trim()
  if (!message) return

  // Mostrar mensaje del usuario
  chatBody.innerHTML += `
    <div class="chat-msg user-msg shadow-sm">
      ${message}
    </div>
  `

  input.value = ''
  chatBody.scrollTop = chatBody.scrollHeight

  // Indicador "escribiendo..."
  const typing = document.createElement('div')
  typing.className = 'chat-msg bot-msg shadow-sm'
  typing.innerText = 'Escribiendo...'
  chatBody.appendChild(typing)
  chatBody.scrollTop = chatBody.scrollHeight

  fetch('../backend/chat.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message }),
  })
    .then((res) => res.json())
    .then((data) => {
      typing.remove()

      chatBody.innerHTML += `
        <div class="chat-msg bot-msg shadow-sm">
          ${data.reply}
        </div>
      `
      chatBody.scrollTop = chatBody.scrollHeight
    })
    .catch(() => {
      typing.remove()
      chatBody.innerHTML += `
        <div class="chat-msg bot-msg shadow-sm text-danger">
          Error de conexión. Intenta más tarde.
        </div>
      `
    })
}
