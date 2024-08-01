@extends('layouts.main')
@section('content')
    @if ($users->isEmpty())
        <h1 class="mt-3">Không tìm thấy kết quả!!!</h1>
    @else
        @foreach ($users as $item)
            <div class="py-2">
                <div class="shadow px-3 border border-top-0 rounded bg-body-tertiary py-3">
                    <div class="row">
                        <div class="col-7">
                            <div class="d-flex ">
                                <img src="{{ $item->avatar ? asset($item->avatar) : $item->defaultAvatar() }} " alt=""
                                    height="60px" width="60px" class="border rounded-circle">
                                <div class="ps-3">
                                    <p class="fw-medium mb-2">{{ $item->name }}</p>
                                    <p class="fw-lighter">{{ $item->address }}</p>

                                </div>
                            </div>
                        </div>
                        <div class="col-5 text-end">
                            @switch($item->friendStatus)
                                @case('friends')
                                    <div class="dropdown wa">
                                        <a class="btn btn-info dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Bạn bè <i class="bi bi-people-fill"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="{{ route('deleteFriend', $item->id) }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn ">Xóa bạn <i class="bi bi-person-fill-x"></i></button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @break

                                @case('sent')
                                    {{-- <button type="button" class="btn btn-outline-secondary">Đã gửi yêu cầu <i
                                            class="bi bi-person-fill-up"></i></button> --}}
                                    <div class="dropdown wa">
                                        <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Đã gửi<i class="bi bi-person-fill-up"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="{{ route('cancelFriendRequest', $item->id) }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn ">Hủy yêu cầu <i class="bi bi-person-fill-x"></i></button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                @break

                                @case('received')
                                    <div class="d-flex flex-row-reverse">

                                        <form action="{{ route('denyFriend', $item->id) }}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">Từ chối</button>
                                        </form>
                                        <form action="{{ route('acceptFriend', $item->id) }}" method="post" class="px-4">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success">Chấp nhận</button>
                                        </form>
                                    </div>
                                @break

                                @default
                                    <form action="{{ route('addFriend', $item->id) }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary">Thêm bạn bè<i
                                                class="bi bi-person-fill-add"></i></button>
                                    </form>
                            @endswitch


                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection
