<?php

// use App\Events\MessageSent;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['jwtAuth']], function() {

    Route::get('/', [User\HomeController::class, 'index'])->name('home.index');

    Route::group(['prefix' => 'profile'], function() {

        Route::get('/{id}', [User\ProfileController::class, 'index'])->name('profile.index');
        
        Route::get('update/{id}', [User\ProfileController::class, 'edit']) ->name('profile.update');
        Route::post('update/{id}', [User\ProfileController::class, 'update']);
        Route::post('updateImage/{id}', [User\ProfileController::class, 'updateImage'])->name('profile.updateImage');
        Route::get('changePassword/{id}', [User\ProfileController::class, 'changePassword']) ->name('profile.changePassword');

    });

    Route::get('/search', [User\SearchController::class, 'index' ]) ->name('search');

    Route::group(['namespace' => 'friendShip', 'prefix' => 'friends'], function(){
        Route::post('/add/{userRequestedId}', [User\FriendController::class, 'addFriend'])->name('addFriend');
        Route::post('/cancelFriendRequest/{user}', [User\FriendController::class, 'cancelFriendRequest'])->name('cancelFriendRequest');
        Route::post('/accept/{requesterId}', [User\FriendController::class, 'acceptFriend'])->name('acceptFriend');
        Route::post('/deny/{requesterId}', [User\FriendController::class, 'denyFriend'])->name('denyFriend');
        Route::post('/delete/{userRequestedId}', [User\FriendController::class, 'deleteFriend'])->name('deleteFriend');
        Route::get('', [User\FriendController::class, 'friends'])->name('friends');
        Route::get('/pending', [User\FriendController::class, 'pendingFriendRequests'])->name('pendingFriendRequests');
    });

    Route::Group(['namespace' => 'post', 'prefix' => 'post'], function(){
        Route::get('/', [User\PostController::class, 'loadMorePosts'])->name('home.load_more_posts');

        Route::post('/store', [User\PostController::class, 'store'])->name('createPost');
        Route::post('/update/{id}', [User\PostController::class, 'update'])->name('updatePost');

        Route::get('/delete/{id}', [User\PostController::class, 'delete'])->name('deletePost');
        Route::get('/removeTag/{id}', [User\PostController::class, 'removeTag'])->name('removeTag');

        Route::post('/{post}/like', [User\PostController::class, 'like'])->name('post.like');

        
    });

    Route::Group(['prefix' => 'comment'], function(){
        Route::post('/{post}', [User\CommentController::class, 'store'])->name('comment.store');
        Route::post('/{comment}/like', [User\CommentController::class, 'like'])->name('comment.like');

        Route::post('/{comment}/update', [User\CommentController::class, 'update'])->name('comment.update');

        Route::get('/{comment}/delete', [User\CommentController::class, 'delete'])->name('comment.delete');

    });


    Route::get('/demo', function(){
        return view('demo');    
    });
    
    Route::post('/send-message', [User\ChatController::class, 'sendMessage']);
    Route::get('/get-messages/{receiver_id}', [User\ChatController::class, 'getMessages']);



    // Route::post('/test', [User\ChatController::class, 'sendTestMessage']);


    // Route::post('send-message', function(Request $request){
    //     broadcast(new MessageSent('abc', Auth::user()));

    //     return response()->json(['status' => 'Message Sent!', 'message' => 'abc', 'sender' => Auth::id()]);

    // });

});



// Route::get('/chat', function(){
//     return view('chat');
// });

// Route::post('send-message', function(Request $request){
//         broadcast(new MessageSent($request->message, Auth::user()));

//         return response()->json(['status' => 'Message Sent!', 'message' => $request->message, 'sender' => Auth::id()]);

// });