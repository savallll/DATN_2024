<nav class="navbar navbar-expand-lg bg-body-tertiary mt-3 border border-top-0 rounded shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Socio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-flex" id="navbarSupportedContent">
            {{-- <div class=""> --}}
            <div class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                    </li>

                </ul>
                <form class="d-flex ps-5" role="search" action="{{ route('search') }}" method="GET">
                    @csrf
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="key">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
            <div class="ms-auto">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index',auth()->user()->id ) }}">Trang cá nhân</a></li>
                            <li><a class="dropdown-item" href="">Cài đặt <i class="bi bi-gear"></i></a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" id="logout-button">Logout</a></li>
                        </ul>
                    </li>
                </ul>

            </div>
            {{-- </div> --}}
        </div>
    </div>
</nav>
<script>
    // Lấy button hoặc link đăng xuất từ HTML
    const logoutButton = document.getElementById('logout-button');

    // Xử lý sự kiện khi người dùng nhấn vào nút đăng xuất
    logoutButton.addEventListener('click', async () => {
        try {
            // Gửi yêu cầu đăng xuất đến API
            const response = await fetch('/api/auth/logout', {
                method: 'POST'
            });

            // Kiểm tra xem có lỗi không
            if (!response.ok) {
                // Xử lý lỗi
                const errorData = await response.json();
                alert(errorData.error); // Hiển thị thông báo lỗi
                return;
            }

            // Nếu đăng xuất thành công, chuyển hướng đến trang đăng nhập hoặc làm gì đó khác
            window.location.href =
            '/api/auth/index'; // Chuyển hướng đến trang đăng nhập sau khi đăng xuất thành công
        } catch (error) {
            console.error('Error:', error);
        }
    });
</script>
