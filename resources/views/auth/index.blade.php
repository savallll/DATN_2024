@extends('auth.layouts.main')
@section('content')
    {{-- <h2>Weekly Coding Challenge #1: Sign in/up Form</h2> --}}
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form id="formRegister" method="post" action="{{ route('register') }}">
                @csrf
                <h1>Đăng ký</h1>
                {{-- <div class="social-container">
                        <a href="#" class="social"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                        <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <span>or use your email for registration</span> --}}

                <input type="text" placeholder="Name" name="name" id="name">
                {{-- @error('name')
                            <small class="text-danger" id="nameError">{{ $errors->first('name')  }}</small>
                    @enderror --}}
                <input type="email" placeholder="Email" name="emailReg" id="emailReg">
                {{-- @error('emailReg')
                            <small class="text-danger" id="emailError">{{ $errors->first('emailReg')  }}</small>
                    @enderror --}}
                <input type="password" placeholder="Password" name="passwordReg" id="passwordReg">
                {{-- @error('passwordReg')
                            <small class="text-danger" id="passError">{{ $errors->first('passwordReg')  }}</small>
                    @enderror --}}
                <div id="error" class="text-danger pb-3"></div>

                <button type="submit" onclick="validateForm()"> Đăng ký</button>
            </form>
        </div>


        <div class="form-container sign-in-container">
            <form action="{{ route('login') }}" method="POST" id="formLogin">
                @csrf
                <h1>Đăng nhập</h1>
                <div class="social-container">
                    {{-- <a href="{{ route('auth.fb') }}" class="social"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ route('auth.google') }}" class="social"><i class="fab fa-google-plus-g"></i></a> --}}
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>or use your account</span>
                @if (session('success'))
                    <p class="text-success text-center">{{ session('success') }}</p>
                @endif
                <input type="email" placeholder="Email" name="email">
                {{-- @error('email')
                            <small class="text-danger">{{ $errors->first('email')  }}</small>
                    @enderror --}}
                <input type="password" placeholder="Password" name="password">
                {{-- @error('password')
                            <small class="text-danger">{{ $errors->first('password')  }}</small>
                    @enderror --}}
                @if (session('error'))
                    <p class="text-danger text-center">{{ session('error') }}</p>
                @endif
                {{-- <a href="{{ route('password.request') }}">Forgot your password?</a> --}}
                <button type="submit">Đăng nhập</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Lấy form đăng nhập và đăng ký từ HTML
        const loginForm = document.getElementById('formLogin');
        const registerForm = document.getElementById('formRegister');

        // Xử lý sự kiện khi submit form đăng nhập
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Ngăn chặn submit form mặc định

            // Lấy dữ liệu từ form
            const formData = new FormData(loginForm);

            try {
                // Gửi yêu cầu đăng nhập đến API
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    body: formData
                    // body: {
                    //     email: manhhungd68@gmail.com,
                    //     password: 123
                    // }
                });

                // Kiểm tra xem có lỗi không
                if (!response.ok) {
                    // Xử lý lỗi
                    const errorData = await response.json();
                    alert(errorData.error); // Hiển thị thông báo lỗi
                    return;
                }

                // Nếu đăng nhập thành công, chuyển hướng đến trang khác hoặc làm gì đó khác
                window.location.href =
                '/'; // Chuyển hướng đến trang dashboard sau khi đăng nhập thành công
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Xử lý sự kiện khi submit form đăng ký
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Ngăn chặn submit form mặc định

            // Lấy dữ liệu từ form
            const formData = new FormData(registerForm);

            try {
                // Gửi yêu cầu đăng ký đến API
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    body: formData
                });

                // Kiểm tra xem có lỗi không
                if (!response.ok) {
                    // Xử lý lỗi
                    const errorData = await response.json();
                    alert(errorData.error); // Hiển thị thông báo lỗi
                    return;
                }

                // Nếu đăng ký thành công, chuyển hướng đến trang đăng nhập hoặc làm gì đó khác
                window.location.href = '/api/auth/index'; // Chuyển hướng đến trang đăng nhập sau khi đăng ký thành công
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
@endsection
