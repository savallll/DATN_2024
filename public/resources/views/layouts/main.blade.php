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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentChannel = null; // Biến để lưu trữ kênh hiện tại

            document.querySelectorAll('.open-chat').forEach(button => {
                button.addEventListener('click', function() {
                    const friendId = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const avatar = this.getAttribute('data-avatar');

                    // Cập nhật tiêu đề chatbox
                    document.getElementById('chat-title').innerText = name;
                    document.getElementById('chat-avatar').src = avatar;

                    // Cập nhật bong bóng chat với avatar và tên
                    const bubbleButton = document.getElementById('bubble-button');
                    bubbleButton.innerHTML =
                        `<img src="${avatar}" alt="${name}" class="rounded-circle" width="40" height="40">`;
                    bubbleButton.setAttribute('data-bs-title', name);
                    bubbleButton.setAttribute('data-bs-toggle', 'tooltip');
                    bubbleButton.setAttribute('data-bs-placement', 'left');

                    // Hiển thị chatbox và ẩn bong bóng chat
                    document.getElementById('main-chat').style.display = 'block';
                    document.getElementById('bubble-chat').style.display = 'none';

                    // Khởi tạo tooltip
                    new bootstrap.Tooltip(bubbleButton);

                    // Kiểm tra sự tồn tại của các phần tử chat
                    const chatBox = $('#chat-box');
                    const chatForm = $('#chat-form');
                    const messageInput = $('#messageInput');

                    if (!chatBox.length || !chatForm.length || !messageInput.length) {
                        console.error('One or more chat elements are missing!');
                        return;
                    }

                    const userId = '{{ Auth::id() }}';
                    const receiverId = this.getAttribute('data-id');

                    const participants = [userId, receiverId];
                    participants.sort();
                    const channelName = `chat.${participants[0]}.${participants[1]}`;

                    // Rời khỏi kênh hiện tại nếu có
                    if (currentChannel) {
                        echo.leave(currentChannel);
                    }

                    // Tham gia kênh mới
                    echo.join(channelName)
                        .here((users) => {
                            console.log('Users in channel:', users);
                        })
                        .listen('MessageSent', (e) => {
                            // Lắng nghe sự kiện MessageSent
                            console.log('Event data received:', e);
                            const messageElement = `
                            <div class="message">
                                <div class="message-content">${e.message}</div>
                                <div class="clearfix"></div>
                            </div>`;
                            chatBox.append(messageElement);
                            chatBox.scrollTop(chatBox[0].scrollHeight);
                        });

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

                    // Cập nhật kênh hiện tại
                    currentChannel = channelName;
                });
            });

            document.getElementById('minimize-chat').addEventListener('click', function() {
                document.getElementById('main-chat').style.display = 'none';
                document.getElementById('bubble-chat').style.display = 'flex';
            });

            document.getElementById('close-chat').addEventListener('click', function() {
                if (currentChannel) {
                    echo.leave(currentChannel);
                    currentChannel = null;
                }
                document.getElementById('main-chat').style.display = 'none';
                document.getElementById('bubble-chat').style.display = 'none';
            });

            document.getElementById('bubble-chat').addEventListener('click', function() {
                document.getElementById('main-chat').style.display = 'block';
                document.getElementById('bubble-chat').style.display = 'none';
            });
        });
    </script>








</body>

</html>
