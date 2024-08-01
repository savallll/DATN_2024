<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    //
    public function addFriend($receive_id){

        $result = Auth::user()->add_friend($receive_id);

        if($result == 0){
            return redirect()->back()->with(['error' => 'gửi yêu cầu kết bạn thất bại']);
        }

        return redirect()->back();

    }

    public function cancelFriendRequest(Request $request, User $user) {
        if (!$user) {
            return back()->withErrors(['message' => 'This user could not be found']);
        }
        auth()->user()->cancel_friend_request($user->id);
        return back()->with('message', 'Friend request canceled successfully');
    }

    public function acceptFriend(Request $request, $requesterId)
    {
        $user = Auth::user();
        $result = $user->accept_friend($requesterId);

        if ($result === 0) {
            return redirect()->back()->with(['error' => 'chấp nhận kết bạn thất bại']);
        }
        return redirect()->back();
    }

    // Từ chối yêu cầu kết bạn
    public function denyFriend(Request $request, $requesterId)
    {
        $user = Auth::user();
        $result = $user->deny_friend($requesterId);

        if ($result === 0) {
            return redirect()->back()->with(['error' => 'từ kết bạn thất bại']);
        }
        return redirect()->back();
    }

    // Xóa bạn bè
    public function deleteFriend(Request $request, $userRequestedId)
    {
        $user = Auth::user();
        $result = $user->delete_friend($userRequestedId);

        if ($result === 0) {
            return redirect()->back()->with(['error' => 'xóa kết bạn thất bại']);
        }
        return redirect()->back();
    }

    // Lấy danh sách bạn bè
    public function friends()
    {
        $user = Auth::user();
        $friends = $user->friends();

        return response()->json(['status' => 'success', 'friends' => $friends], 200);
    }

    // Lấy danh sách yêu cầu kết bạn đang chờ xử lý
    public function pendingFriendRequests()
    {
        $user = Auth::user();
        $pendingRequests = $user->pending_friend_requests();

        return response()->json(['status' => 'success', 'pending_requests' => $pendingRequests], 200);
    }
}
