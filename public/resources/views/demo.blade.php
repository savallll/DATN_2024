<html>

<head>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css"
        rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('style.css') }}"> --}}
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.min.js"></script>
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
    </script> --}}


    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>
    <div class="container">
        <div>
            @foreach (\App\Models\User::all() as $item)
                <p><a href="/login/{{ $item->id }}">{{ $item->name }}</a></p>
            @endforeach
        </div>
        <div class="">
            <a href="logout">logout</a>
        </div>
        <h3 class=" text-center">Messaging: {{ optional(Auth::user())->name }}</h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent</h4>
                        </div>
                        <div class="srch_bar">
                            <div class="stylish-input-group">
                                <input type="text" class="search-bar" placeholder="Search">
                                <span class="input-group-addon">
                                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="inbox_chat">
                        <div class="chat_list active_chat">
                            <div class="chat_people">
                                <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png"
                                        alt="sunil"> </div>
                                <div class="chat_ib">
                                    <h5>Sunil Rajput <span class="chat_date">Dec 25</span></h5>
                                    <p>Test, which is a new approach to have all solutions
                                        astrology under one roof.</p>
                                </div>
                            </div>
                        </div>
                        <div class="chat_list">
                            <div class="chat_people">
                                <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png"
                                        alt="sunil"> </div>
                                <div class="chat_ib">
                                    <h5>Sunil Rajput <span class="chat_date">Dec 25</span></h5>
                                    <p>Test, which is a new approach to have all solutions
                                        astrology under one roof.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history" id="chat-box">

                    </div>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <form id="chat-form">
                                @csrf
                                <input type="text" class="write_msg" placeholder="Type a message" id="message-input">
                                <input type="hidden" id="receiver-id" value="3">
                                <button class="msg_send_btn" type="submit"><i class="fa fa-paper-plane-o"
                                        aria-hidden="true"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
        $(document).ready(function() {

            const chatBox = $('#chat-box');
            const chatForm = $('#chat-form');
            const messageInput = $('#message-input');
            const receiverId = $('#receiver-id').val();
            const userId = '{{ Auth::id() }}'; // ID của người dùng hiện tại

            // echo.channel('presence-chat');

            // console.log('Echo instance:', echo);

            // Example of joining a channel and listening to an event
            // echo.join('chat')
            //     .here((users) => {
            //         console.log('Users in channel:', users);
            //     })
            //     .joining((user) => {
            //         console.log('User joined:', user);
            //     })
            //     .leaving((user) => {
            //         console.log('User left:', user);
            //     })
            //     .listen('MessageSent', (e) => {
            //         // Lắng nghe sự kiện MessageSent
            //         console.log('Event data received:', e);
            //         const messageElement = `
            //                 <div class="message">
            //                     <div class="message-content">${e.message}</div>
            //                     <div class="clearfix"></div>
            //                 </div>`;
            //         chatBox.append(messageElement);
            //         chatBox.scrollTop(chatBox[0].scrollHeight);
            //     });

            // window.Echo.private(`chat.${userId}.${receiverId}`)
            //     .listen('MessageSent', (e) => {
            //         console.log('Event data received:', e);
            //         const messageElement = `
        //             <div class="message ${e.sender.id == userId ? 'align-self-start' : 'align-self-end'}">
        //                 <div class="message-content">${e.message.message}</div>
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
                            receiver_id: receiverId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            messageInput.val('');
                        }
                    }); 
                }
            });
        });
    </script>
</body>

</html>
