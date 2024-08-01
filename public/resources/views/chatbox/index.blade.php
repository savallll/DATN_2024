<style>
    .main-chat {
        position: fixed;
        bottom: 10px;
        right: 10px;
        z-index: 9999;
        width: 100%;
        max-width: 400px;
        opacity: 1;
        display: none
    }

    .chat-box {
        height: 400px;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
    }

    .message {
        margin-bottom: 15px;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }

    .message .message-content {
        display: inline-block;
        padding: 10px;
        border-radius: 10px;
        max-width: 80%;
    }

    .message.user .message-content {
        background-color: #007bff;
        color: white;
        margin-left: auto;
    }

    .message.other .message-content {
        background-color: #f1f1f1;
        color: black;
    }

    #chat-form {
        display: flex;
        align-items: center;
    }

    #message-input {
        flex: 1;
        margin-right: 5px;
    }

    .input-group-append {
        flex-shrink: 0;
    }

    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }

    .chat-header .chat-title {
        font-weight: bold;
    }

    .chat-header .chat-actions {
        display: flex;
        gap: 5px;
    }

    .chat-header .chat-actions button {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 16px;
    }

    .bubble-chat {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        bottom: 10px;
        right: 10px;
        z-index: 9999;
        cursor: pointer;
        display: none;
    }

    #bubble-button {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<div class="main-chat" id="main-chat">
    <div class="card">
        <div class="chat-header card-header bg-primary d-flex">
            <img id="chat-avatar" class="rounded-circle" src="" alt="" height="30px" width="30px">
            <span class="chat-title ms-3 text-white" id="chat-title">Name</span>
            <div class="chat-actions ms-auto">
                <button id="minimize-chat" title="Minimize">-</button>
                <button id="close-chat" title="Close">×</button>
            </div>
        </div>
        <div class="card-body chat-box" id="chat-box">
            <!-- Messages will be appended here -->
        </div>
        <div class="card-footer">
            <form id="chat-form">
                <div class="input-group">
                    <input type="text" id="messageInput" class="form-control" placeholder="Type a message">
                    <input type="hidden" id="receiver-id">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="bubble-chat" id="bubble-chat">
    <button type="button" class="btn btn-secondary" id="bubble-button">
        💬
    </button>
</div>

<div class="bubble-chat" id="bubble-chat">
    <button type="button" class="btn btn-secondary" id="bubble-button">
        💬
    </button>
</div>





{{-- <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.min.js"></script> --}}



{{-- <script>
    @php
        $token = $_COOKIE['jwt_token'] ?? 'default_value';
    @endphp
    // Ensure Echo and Socket.io are loaded
    if (typeof io !== 'undefined' && typeof Echo !== 'undefined') {
        console.log('Socket.io and Laravel Echo are loaded');

        var echo = new Echo({
            broadcaster: 'socket.io',
            host: window.location.hostname + ':6001',
            auth: {
                headers: {
                    Authorization: 'Bearer {{ $token }}'
                }
            }
        });

    } else {
        console.error('Socket.io or Laravel Echo failed to load');
    }

    $(document).ready(function() {
        document.querySelectorAll('.open-chat').forEach(button => {
            button.addEventListener('click', function() {

                const chatBox = $('#chat-box');
                const chatForm = $('#chat-form');
                const messageInput = $('#message-input');

                const userId = '{{ Auth::id() }}';
                const receiverId = this.getAttribute('data-id');


                const participants = [userId, receiverId];
                participants.sort();
                const channelName = `chat.${participants[0]}.${participants[1]}`;

                // echo.join(channelName)
                //     .here((users) => {
                //         console.log('Users in channel:', users);
                //     })
                //     .joining((user) => {
                //         console.log('User joined:', user);
                //     })
                //     .leaving((user) => {
                //         console.log('User left:', user);
                //     })

                // // Example of joining a channel and listening to an event
                // echo.channel('channelName')
                //     .listen('MessageSent', (e) => {
                //         // Lắng nghe sự kiện MessageSent
                //         console.log('Event data received:', e);
                //         const messageElement = `
                //             <div class="message">
                //                 <div class="message-content">${e.message}</div>
                //                 <div class="clearfix"></div>
                //             </div>`
                //         ;
                //         chatBox.append(messageElement);
                //         chatBox.scrollTop(chatBox[0].scrollHeight);
                //     });



                // Lấy tin nhắn cũ khi tải trang
                // $.ajax({
                //     url: `/get-messages/${receiverId}`,
                //     type: 'GET',
                //     success: function(messages) {
                //         messages.forEach(message => {
                //             const messageElement = `
                //             <div class="message ${message.sender_id == userId ? 'align-self-start' : 'align-self-end'}">
                //                 <div class="message-content">${message.message}</div>
                //                 <div class="clearfix"></div>
                //             </div>`;
                //             chatBox.append(messageElement);
                //         });
                //         chatBox.scrollTop(chatBox[0].scrollHeight);
                //     }
                // });

                // Khởi tạo kênh chat và lắng nghe sự kiện
                // window.Echo.join('chat')
                //     .here((users) => {
                //         // Hiển thị danh sách người dùng hiện có trong kênh chat
                //         console.log('Users in chat:', users);
                //     })
                //     .joining((user) => {
                //         // Khi một người dùng tham gia kênh
                //         console.log('User joined:', user);
                //     })
                //     .leaving((user) => {
                //         // Khi một người dùng rời khỏi kênh
                //         console.log('User left:', user);
                //     })
                //     .listen('MessageSent', (e) => {
                //         // Lắng nghe sự kiện MessageSent
                //         console.log('Event data received:', e);
                //         const messageElement = `
                //     <div class="message ${e.sender.id == userId ? 'align-self-start' : 'align-self-end'}">
                //         <div class="message-content">${e.message.message}</div>
                //         <div class="clearfix"></div>
                //     </div>`;
                //         chatBox.append(messageElement);
                //         chatBox.scrollTop(chatBox[0].scrollHeight);
                //     });

                // Gửi tin nhắn khi form được gửi
                chatForm.submit(function(event) {
                    event.preventDefault();
                    const message = messageInput.val();
                    if (message.trim() !== '') {
                        // Gửi tin nhắn tới server
                        $.ajax({
                            url: '/send-message',
                            type: 'POST',
                            data: {
                                message: message,
                                receiverId: receiverId,
                                _token: $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(response) {
                                messageInput.val('');
                            }
                        });
                    }
                });
            });
        });

    });
</script> --}}
