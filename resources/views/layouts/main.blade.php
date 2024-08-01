<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>

    {{-- <link rel="stylesheet" href="{{ asset('app/main.css') }} "> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"  rel="stylesheet" /> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.min.js"></script>

    
</head>

<body class="bg-body">
    <div class="px-4">
        @include('layouts.header')

        <div class="mt-3 row">
            <div class="col-md-3">
                @include('layouts.leftSidebar')


            </div>
            <div class="col-md-6">
                @yield('content')

            </div>
            <div class="col-md-3">
                @include('layouts.rightSidebar')

            </div>

        </div>

    </div>
    <div>
        @include('chatbox.index')
    </div>



    <script>
        @php
            $token = $_COOKIE['jwt_token'] ?? 'default_value';
        @endphp
        // Ensure Echo and Socket.io are loaded
        if (typeof io !== 'undefined' && typeof Echo !== 'undefined') {
            console.log('Socket.io and Laravel Echo are loaded');

            var Echo = new Echo({
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

        document.addEventListener('DOMContentLoaded', function() {
            let currentChannel = null; // Biến để lưu trữ kênh hiện tại
            let currentReceiverId = null; // Biến để lưu trữ receiverId hiện tại
            const userId = '{{ Auth::id() }}';

            // Kiểm tra sự tồn tại của phần tử chat-container
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

                    // Tạo hoặc lấy chatbox cho bạn bè
                    let chatBoxContainer = document.getElementById(`chat-box-${friendId}`);
                    if (!chatBoxContainer) {
                        chatBoxContainer = createChatBox(friendId, name, avatar);
                        chatContainer.appendChild(chatBoxContainer);
                    }

                    // Ẩn tất cả các chatbox khác và hiển thị chatbox hiện tại
                    document.querySelectorAll('.chat-box-container').forEach(box => box.style
                        .display = 'none');
                    chatBoxContainer.style.display = 'block';

                    // Xác nhận phần tử tồn tại và lấy phần tử chat-box và chat-form
                    const chatBox = chatBoxContainer.querySelector('.chat-box');
                    const chatForm = chatBoxContainer.querySelector(`#chat-form-${friendId}`);
                    const messageInput = chatBoxContainer.querySelector(
                        `#messageInput-${friendId}`);

                    if (!chatBox || !chatForm || !messageInput) {
                        console.error('One or more chat elements are missing!');
                        return;
                    }

                    // Lấy tin nhắn cũ từ server
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
                            chatBox.scrollTop = chatBox
                                .scrollHeight; // Cuộn xuống tin nhắn mới nhất
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching messages:', error);
                        }
                    });

                    const participants = [userId, friendId];
                    participants.sort();
                    const channelName = `chat.${participants[0]}.${participants[1]}`;

                    // Rời khỏi kênh hiện tại nếu có
                    if (currentChannel) {
                        Echo.leave(currentChannel);
                    }

                    // Tham gia kênh mới
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

                    // Cập nhật kênh và receiverId hiện tại
                    currentChannel = channelName;
                    currentReceiverId = friendId;

                    // Gửi tin nhắn khi form được gửi
                    chatForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const message = messageInput.value;
                        if (message.trim() !== '') {
                            // Gửi tin nhắn tới server
                            $.ajax({
                                url: '/send-message',
                                type: 'POST',
                                data: {
                                    message: message,
                                    receiverId: currentReceiverId,
                                    _token: $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(response) {
                                    messageInput.value = '';
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error sending message:',
                                        error);
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

                // Cập nhật bong bóng chat với avatar và tên
                const bubbleButton = document.getElementById('bubble-button');
                bubbleButton.innerHTML =
                    `<img src="${avatar}" alt="${name}" class="rounded-circle" width="40" height="40">`;
                bubbleButton.setAttribute('data-bs-title', name);
                bubbleButton.setAttribute('data-bs-toggle', 'tooltip');
                bubbleButton.setAttribute('data-bs-placement', 'left');


                // Khởi tạo tooltip
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
    </script>
    {{-- <script src="{{ asset('js\chatBox.js') }}"></script> --}}
</body>

</html>
