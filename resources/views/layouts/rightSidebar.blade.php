@if ($pendingFriendRequests !== null && count($pendingFriendRequests) > 0)
    <div class="pb-4">
        <div class="border border-top-0 rounded px-3 py-3 bg-body-tertiary shadow">
            <div class="">
                <h3>Yêu cầu kết bạn</h3>
                <ol class="list-group list-group border border-0 mt-4">
                    @foreach ($pendingFriendRequests as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center border border-0">
                            <img class="profile-pic rounded-circle"
                                src="{{ $item->avatar ? asset($item->avatar) : $item->defaultAvatar() }}" height="60px"
                                width="60px">
                            <div class="ms-3 me-auto">
                                <div class="fw-bold">{{ $item->name }}</div>
                                {{-- Content for list item --}}
                            </div>
                            <span class="badge text-bg-primary rounded-pill">14</span>
                        </li>
                    @endforeach


                </ol>
            </div>
        </div>
    </div>
@endif

<div class="border border-top-0 rounded px-3 py-3 bg-body-tertiary shadow">
    <div class="">
        <h1>Bạn bè</h1>
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        <ol class="list-group list-group border border-0 mt-4">

            @foreach ($friendList as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center border border-0">
                    <a href="{{ route('profile.index', $item->id) }}"
                        class="text-decoration-none text-dark d-flex align-items-center">
                        <img class="profile-pic rounded-circle"
                            src="{{ $item->avatar ? asset($item->avatar) : $item->defaultAvatar() }}" height="60px"
                            width="60px">
                        <div class="ms-3 me-auto">
                            <div class="fw-bold">{{ $item->name }}</div>
                            {{-- Content for list item --}}
                        </div>
                    </a>
                    <button role="button" class="open-chat border-0 bg-body" 
                        data-name="{{ $item->name }}"
                        data-avatar="{{ $item->avatar ? asset($item->avatar) : $item->defaultAvatar() }}"
                        data-id={{ $item->id}}>
                        <i class="bi bi-wechat"></i>
                    </button>
                </li>
            @endforeach


        </ol>
    </div>
</div>
