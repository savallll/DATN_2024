@extends('profile.layouts.main')
@section('content')
    <div class="container mt-5">

        <div class="row">
            <div class="col-3">
                <h2>Cài đặt</h2>

                <nav id="navbar-example3" class="h-100 flex-column align-items-stretch pe-4 border-end mt-3">
                    <nav class="nav nav-pills flex-column">
                        <a class="nav-link" href="#item-1">Giới thiệu</a>
                        <nav class="nav nav-pills flex-column">
                            <a class="nav-link ms-3 my-1" href="#item-1-1">Tiểu sử</a>
                            <a class="nav-link ms-3 my-1" href="#item-1-2">Liên hệ</a>
                        </nav>
                        <a class="nav-link" href="#item-3">Bảo mật</a>
                        <nav class="nav nav-pills flex-column">
                            <a class="nav-link ms-3 my-1" href="#item-3-1">Thay đổi mật khẩu</a>
                            <a class="nav-link ms-3 my-1" href="#item-3-2">Thay đổi email</a>
                        </nav>
                        <a class="nav-link" href="#item-2">Quyền riêng tư</a>

                    </nav>
                </nav>
            </div>

            <div class="col-9">
                <h4 class="mt-3">Hồ sơ của bạn</h4>

                <div data-bs-spy="scroll" data-bs-target="#navbar-example3" data-bs-smooth-scroll="true"
                    class="scrollspy-example-2 mt-4" tabindex="0">
                    <div class="py-4">
                        <div class="border-end-0 rounded px-4 py-4 bg-body-tertiary shadow  ">
                            <div id="item-1">
                                <h4>Giới thiệu</h4>
                            </div>
                            <div class="ps-4 pt-4">
                                <div id="item-1-1 " class="">
                                    <h5>Tiểu sử</h5>
                                    <form action="{{ route('profile.update', Auth::user()->id) }}" method="post">
                                        @csrf
                                        <textarea class="form-control" id="editor" rows="30" name="description">{{ $user->description }}</textarea>
                                        <div class="text-end pt-3">
                                            <button type="submit" class="btn btn-outline-primary">Save</button>
                                        </div>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Tìm phần tử textarea với id="editor"
                                            const editorTextarea = document.querySelector('#editor');
                                            // Tăng chiều cao của textarea lên 500px
                                            editorTextarea.style.height = '5000px';
                                            ClassicEditor
                                                .create(document.querySelector('#editor'))
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                        });
                                    </script>
                                </div>
                                <div id="item-1-2" class="pt-4">
                                    <h5>Liên hệ:</h5>
                                    <form action="{{ route('profile.update', Auth::user()->id) }}" method="post"
                                        class="pt-2">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="Address" placeholder=""
                                                name='address' value="{{ $user->address }}">
                                            <label for="Address">Address</label>
                                        </div>
                                        {{-- <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="Email" placeholder="" name="email">
                                            <label for="Email">Email</label>
                                        </div> --}}
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="Phone" placeholder=""
                                                name="phone" value="{{ $user->phone }}">
                                            <label for="Phone">Phone</label>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-outline-primary">Save</button>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="py-4">

                        <div class="border-end-0 rounded px-4 py-4 bg-body-tertiary shadow  ">
                            <div id="item-3">
                                <h4>Bảo mật</h4>
                            </div>
                            <div class="ps-4 pt-4">
                                <div id="item-3-1">
                                    @if (session('success'))
                                        <p class="text-success py-4">{{ session('success') }}</p>
                                    @endif
                                    <a class="h5" data-bs-toggle="collapse" href="#changePassWord" role="button"
                                        aria-expanded="false" aria-controls="changePassWord">
                                        Thay đổi mật khẩu

                                    </a>
                                    <div class="collapse pt-3" id="changePassWord">
                                        <div class="card card-body">
                                            <form action="{{ route('profile.changePassword', Auth::user()->id) }}"
                                                class="pt-2">
                                                @csrf
                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" id="oldPassWord" name="oldPassWord"
                                                        placeholder="">
                                                    <label for="oldPassWord">Mật khẩu cũ</label>
                                                    @error('oldPassWord')
                                                        <small class="text-danger">{{ $errors->first('oldPassWord') }}</small>
                                                    @enderror
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" id="newPassWord" name="newPassWord"
                                                        placeholder="">
                                                    <label for="newPassWord">Mật khẩu mới</label>
                                                    @error('newPassWord')
                                                        <small class="text-danger">{{ $errors->first('newPassWord') }}</small>
                                                    @enderror
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" id="confirmPassWord" name="confirmPassWord"
                                                        placeholder="">
                                                    <label for="confirmPassWord">Xác nhận mật khẩu</label>
                                                    @error('confirmPassWord')
                                                        <small class="text-danger">{{ $errors->first('confirmPassWord') }}</small>
                                                    @enderror
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-outline-primary">Change</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div id="item-3-2" class="py-4">
                                    <a class="h5" data-bs-toggle="collapse" href="#changeEmail" role="button"
                                        aria-expanded="false" aria-controls="changeEmail">
                                        Thay đổi Email
                                    </a>
                                    <div class="collapse pt-3" id="changeEmail">
                                        <div class="card card-body text-center">
                                            ...Updating...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="py-4">
                        <div class="border-end-0 rounded px-4 py-4 bg-body-tertiary shadow  ">
                            <div id="item-2">
                                <h4>Riêng tư</h4>
                                <p class="text-center">...Updating...</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
