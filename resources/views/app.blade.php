@extends('layouts.main')
@section('content')
    <div class="px-3 border border-top-0 rounded bg-body-tertiary shadow">
        <h3>Đăng bài</h3>
        <form action="{{ route('createPost') }}" method="POST" class="py-4">
            @csrf
            <div class="mb-3">
                <textarea name="body" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Đăng bài</button>
        </form>
    </div>


    <div id="posts">
        @include('layouts.posts', ['posts' => $posts])
    </div>
    <div id="loading" class="text-center my-3 d-flex justify-content-center" style="display: none;">
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function handleLike(button, url) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.liked) {
                            button.removeClass('text-dark').addClass('text-primary');
                        } else {
                            button.removeClass('text-primary').addClass('text-dark');
                        }
                        button.html(response.likes_count + ' <i class="bi bi-hand-thumbs-up"></i> ' + (
                            button.hasClass('like-comment-btn') ? 'Like' : 'Like'));
                    }
                });
            }

            // Like button for posts
            $('.like-btn').on('click', function() {
                var button = $(this);
                var postId = button.data('post-id');
                var url = '/post/' + postId + '/like';
                handleLike(button, url);
            });

            // Like button for comments
            $('.like-comment-btn').on('click', function() {
                var button = $(this);
                var commentId = button.data('comment-id');
                var url = '/comment/' + commentId + '/like';
                handleLike(button, url);
            });


            $('.edit-comment').on('click', function() {
                var commentId = $(this).data('comment-id');
                var commentSection = $('#comment-' + commentId);

                commentSection.find('.comment-body').prop('disabled', false);
                commentSection.find('.submit-comment').removeClass('d-none');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var page = 1;

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    page++;
                    loadMoreData(page);
                }
            });

            function loadMoreData(page) {
                $.ajax({
                        url: '{{ route('home.load_more_posts') }}?page=' + page,
                        type: 'get',
                        beforeSend: function() {
                            $('#loading').show();
                        }
                    })
                    .done(function(data) {
                        if (data.trim().length == 0) {
                            $('#loading').html('No more records found');
                            return;
                        }
                        $('#loading').hide();
                        $('#posts').append(data);
                    })
                    .fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('Something went wrong.');
                    });
            }
        });
    </script>

@endsection
