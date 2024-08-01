@foreach ($posts as $item)
    <div class="mt-3 border border-top-0 rounded bg-body-tertiary shadow">
        <div class="d-flex pb-4">
            <img class="rounded-circle mx-2"
                src="{{ $item->user->avatar ? asset($item->user->avatar) : $item->user->defaultAvatar() }}" height="60px"
                width="60px">
            <div class="">
                <h5><a href="{{ route('profile.index', $item->user->id) }}">{{ $item->user->name }}</a>
                    @if (isset($item->parent_id))
                        @php
                            // Lấy người dùng được nhắc đến (nếu có)
                            $mentionedUser = \App\Models\User::find($item->parent_id);
                        @endphp
                        @if ($mentionedUser)
                            <i class="bi bi-caret-right-fill"></i> <a
                                href="{{ route('profile.index', $mentionedUser->id) }}">{{ $mentionedUser->name }}</a>
                        @endif
                    @endif
                </h5>
                <p>{{ $item->getTimeAgoAttribute() }}</p>
            </div>
            <div class="ms-auto me-3 mt-3">
                @if ($item->user_id == Auth::user()->id || $item->parent_id == Auth::user()->id)
                    <div class="dropdown">
                        <i class="bi bi-three-dots" role="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#editPost{{ $item->id }}" role="button">Sửa bài viết</a></li>
                            @if ($item->parent_id == Auth::user()->id)
                                <li><a class="dropdown-item" href="{{ route('removeTag', $item->id) }}">Gỡ thẻ</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('deletePost', $item->id) }}">xóa bài viết</a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="content container overflow-hidden w-100" style="max-height: 500px">
            {!! $item->body !!}
        </div>
        <div class="border-top border-bottom">
            <div class="d-flex justify-content-around py-1">
                <button
                    class="btn flex-fill btn btn-light fw-medium border-0 like-btn {{ Auth::user()->likedPosts->contains($item->id) ? 'text-primary' : 'text-dark ' }}"
                    data-post-id="{{ $item->id }}">
                    {{ $item->getLikedAttribute() }} <i class="bi bi-hand-thumbs-up"></i> Like
                </button>
                <button class="btn flex-fill btn btn-light text-dark fw-medium border-0 ">12 <i
                        class="bi bi-chat-left-dots"></i> comment</button>
                <button class="btn flex-fill btn btn-light text-dark fw-medium border-0 "><i class="bi bi-share"></i>
                    share</button>
            </div>
        </div>
        <div class="py-2">
            @foreach ($item->comments as $comment)
                <div class="d-flex align-items-top rounded bg-light">
                    <img class="rounded-circle mx-2"
                        src="{{ $comment->user->avatar ? asset($comment->user->avatar) : $comment->user->defaultAvatar() }}"
                        height="30px" width="30px">
                    <div class="">
                        <p class="bolder h6">{{ $comment->user->name }}</p>
                        <div class="d-flex align-items-center">
                            <form action="{{ route('comment.update', $comment->id) }}" method="post">
                                @csrf
                                <div class="comment-section d-flex align-items-center"
                                    id="comment-{{ $comment->id }}">
                                    <input class="form-control comment-body" type="text" name="body"
                                        value="{{ $comment->body }}" disabled>
                                    <button type="submit" class="btn d-none submit-comment"><i
                                            class="bi bi-send-fill mx-2"></i></button>
                                </div>
                            </form>
                            @if ($comment->user_id == Auth::user()->id)
                                <div class="dropdown ms-2">
                                    <i class="bi bi-three-dots" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false"></i>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item edit-comment" role="button"
                                                data-comment-id="{{ $comment->id }}">Sửa comment</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('comment.delete', $comment->id) }}">xóa
                                                comment</a></li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <small class="text-secondary fst-italic">{{ $comment->getTimeAgoAttribute() }}</small>
                            <button
                                class="btn ps-2 fw-bold mb-0 like-comment-btn {{ Auth::user()->likedComments->contains($comment->id) ? 'text-primary' : 'text-dark' }}"
                                data-comment-id="{{ $comment->id }}">{{ $comment->getLikedAttribute() }}
                                Like</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cmt py-2">
            <div class="d-flex align-items-center">
                <img class="rounded-circle mx-2"
                    src="{{ $item->user->avatar ? asset($item->user->avatar) : $item->user->defaultAvatar() }}"
                    height="30px" width="30px">
                <form action="{{ route('comment.store', $item->id) }}" method="POST" class="d-flex flex-grow-1">
                    @csrf
                    <input class="form-control" type="text" placeholder="Bình luận" name="body">
                    <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                    <button type="submit" class="btn"><i class="bi bi-send-fill mx-2"></i></button>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal EditPost-->
    <div class="modal fade" id="editPost{{ $item->id }}" tabindex="-1"
        aria-labelledby="editPostLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editPostLabel{{ $item->id }}">
                        <h5><a href="{{ route('profile.index', $item->user->id) }}">{{ $item->user->name }}</a>
                            @if (isset($item->parent_id))
                                @php
                                    // Lấy người dùng được nhắc đến (nếu có)
                                    $mentionedUser = \App\Models\User::find($item->parent_id);
                                @endphp
                                @if ($mentionedUser)
                                    <i class="bi bi-caret-right-fill"></i> <a
                                        href="{{ route('profile.index', $mentionedUser->id) }}">{{ $mentionedUser->name }}</a>
                                @endif
                            @endif
                        </h5>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('updatePost', $item->id) }}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                    <input type="hidden" name="parent_id" value="{{ $item->parent_id }}">
                    <div class="modal-body">
                        <div class="content container overflow-hidden w-100" style="max-height: 500px">
                            <textarea name="body" class="form-control" id="exampleFormControlTextarea1" rows="3">{!! $item->body !!}</textarea>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal EditPost-->
@endforeach
