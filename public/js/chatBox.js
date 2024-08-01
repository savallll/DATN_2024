document.addEventListener('DOMContentLoaded', function() {
    if (typeof io !== 'undefined' && typeof Echo !== 'undefined') {
        console.log('Socket.io and Laravel Echo are loaded');

        var Echo = new Echo({
            broadcaster: 'socket.io',
            host: window.location.hostname + ':6001',
            auth: {
                headers: {
                    Authorization: 'Bearer ' + window.Laravel.jwtToken
                }
            }
        });

    } else {
        console.error('Socket.io or Laravel Echo failed to load');
    }

    let currentChannel = null; // Biến để lưu trữ kênh hiện tại
    let currentReceiverId = null; // Biến để lưu trữ receiverId hiện tại
    const userId = window.Laravel.userId;

    const chatContainer = document.getElementById('chat-container');
    if (!chatContainer) {
        console.error('Chat container element is missing!');
        return;
    }

    document.querySelectorAll('.open-chat').forEach(button => {
        button.addEventListener('click', function() {
            const friendId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const avatar = this.getAttribute('data-avatar');

            let chatBoxContainer = document.getElementById(`chat-box-${friendId}`);
            if (!chatBoxContainer) {
                chatBoxContainer = createChatBox(friendId, name, avatar);
                chatContainer.appendChild(chatBoxContainer);
            }

            document.querySelectorAll('.chat-box-container').forEach(box => box.style.display = 'none');
            chatBoxContainer.style.display = 'block';

            const chatBox = chatBoxContainer.querySelector('.chat-box');
            const chatForm = chatBoxContainer.querySelector(`#chat-form-${friendId}`);
            const messageInput = chatBoxContainer.querySelector(`#messageInput-${friendId}`);

            if (!chatBox || !chatForm || !messageInput) {
                console.error('One or more chat elements are missing!');
                return;
            }

            $.ajax({
                url: `/get-messages/${friendId}`,
                type: 'GET',
                success: function(messages) {
                    messages.forEach(message => {
                        const messageElement = `
                        <div class="message">
                            <div class="message-content border ${message.sender_id == userId ? 'ms-auto' : ''}">${message.message}</div>
                            <div class="clearfix"></div>
                        </div>`;
                        chatBox.innerHTML += messageElement;
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching messages:', error);
                }
            });

            const participants = [userId, friendId];
            participants.sort();
            const channelName = `chat.${participants[0]}.${participants[1]}`;

            if (currentChannel) {
                Echo.leave(currentChannel);
            }

            currentChannel = Echo.join(channelName)
                .here((users) => {
                    console.log('Users in channel:', users);
                })
                .listen('MessageSent', (e) => {
                    if (currentReceiverId === friendId) {
                        const messageElement = `
                        <div class="message">
                            <div class="message-content border ${e.sender == userId ? 'ms-auto' : ''}">${e.message}</div>
                            <div class="clearfix"></div>
                        </div>`;
                        chatBox.innerHTML += messageElement;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });

            currentChannel = channelName;
            currentReceiverId = friendId;

            chatForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const message = messageInput.value;
                if (message.trim() !== '') {
                    $.ajax({
                        url: '/send-message',
                        type: 'POST',
                        data: {
                            message: message,
                            receiverId: currentReceiverId,
                            _token: window.Laravel.csrfToken
                        },
                        success: function(response) {
                            messageInput.value = '';
                        },
                        error: function(xhr, status, error) {
                            console.error('Error sending message:', error);
                        }
                    });
                }
            });
        });
    });

    function createChatBox(friendId, name, avatar) {
        const chatBoxContainer = document.createElement('div');
        chatBoxContainer.id = `chat-box-${friendId}`;
        chatBoxContainer.classList.add('chat-box-container');
        chatBoxContainer.innerHTML = `
            <div class="card">
                <div class="chat-header card-header bg-primary d-flex">
                    <img id="chat-avatar-${friendId}" class="rounded-circle" src="${avatar}" alt="" height="30px" width="30px">
                    <span class="chat-title ms-3 text-white">${name}</span>
                    <div class="chat-actions ms-auto">
                        <button id="minimize-chat-${friendId}" title="Minimize">-</button>
                        <button id="close-chat-${friendId}" title="Close">×</button>
                    </div>
                </div>
                <div class="card-body chat-box">
                    <!-- Messages will be appended here -->
                </div>
                <div class="card-footer">
                    <form id="chat-form-${friendId}">
                        <div class="input-group">
                            <input type="text" id="messageInput-${friendId}" class="form-control" placeholder="Type a message">
                            <input type="hidden" id="receiver-id-${friendId}" value="${friendId}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        `;

        const bubbleButton = document.getElementById('bubble-button');
        bubbleButton.innerHTML = `<img src="${avatar}" alt="${name}" class="rounded-circle" width="40" height="40">`;
        bubbleButton.setAttribute('data-bs-title', name);
        bubbleButton.setAttribute('data-bs-toggle', 'tooltip');
        bubbleButton.setAttribute('data-bs-placement', 'left');

        new bootstrap.Tooltip(bubbleButton);

        chatBoxContainer.querySelector(`#minimize-chat-${friendId}`).addEventListener('click', function() {
            chatBoxContainer.style.display = 'none';
            document.getElementById('bubble-chat').style.display = 'flex';
        });

        chatBoxContainer.querySelector(`#close-chat-${friendId}`).addEventListener('click', function() {
            if (currentChannel) {
                Echo.leave(currentChannel);
                currentChannel = null;
            }
            chatBoxContainer.style.display = 'none';
            document.getElementById('bubble-chat').style.display = 'none';
        });

        document.getElementById('bubble-chat').addEventListener('click', function() {
            chatBoxContainer.style.display = 'block';
            document.getElementById('bubble-chat').style.display = 'none';
        });

        return chatBoxContainer;
    }
});
