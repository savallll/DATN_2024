<?php 

namespace App\Traits ;

use App\Models\User;
use App\Models\Friend;

trait FriendTrait {
    public function add_friend($receiver_id) {
        if($this->id === $receiver_id) {
			return 0;
        }
        if($this->is_friends_with($receiver_id) === 1) {
			// return "already friends";
			return 0;
        }
        if($this->has_pending_friend_request_sent_to($receiver_id) === 1){
			// return "already sent a friend request";
			return 0;
        }
        if($this->has_pending_friend_request_from($receiver_id) === 1){
			return $this->accept_friend($receiver_id);
		}
        $friendship = Friend::create([
            'sender_id' => $this->id,
            'receiver_id' => $receiver_id
        ]);
        if ($friendship) {
            return 1;
        }
        return 0;
    }

    public function cancel_friend_request($receiver_id) {
        if ($this->has_pending_friend_request_sent_to($receiver_id) === 0) {
            return 0;
        }
        $friendship = Friend::where('sender_id', $this->id)
            ->where('receiver_id', $receiver_id)
            ->where('status', 0)
            ->first();
        if ($friendship) {
            $friendship->delete();
            return 1;
        }
        return 0;
    }

    public function accept_friend($sender_id) {
        if($this->has_pending_friend_request_from($sender_id) === 0) {
            return 0;
        }
        $friendship = Friend::where('sender_id', $sender_id)
            ->where('receiver_id', $this->id)
            ->first();
        if($friendship) {
            $friendship->update([
                'status' => 1
            ]);
            return 1;
        }
        return 0;
    }

    public function deny_friend($sender_id) {
        if($this->has_pending_friend_request_from($sender_id) === 0) {
            return 0;
        }
        $friendship = Friend::where('sender_id', $sender_id)
            ->where('receiver_id', $this->id)
            ->first();
        if($friendship) {
            $friendship->delete();
            return 1;
        }
        return 0;
    }

    public function delete_friend($receiver_id_id) {
		if($this->id === $receiver_id_id) {
            return 0;
        }
		if($this->is_friends_with($receiver_id_id) === 1) {
            $Friendship1 = Friend::where('sender_id', $receiver_id_id)
            ->where('receiver_id', $this->id)
            ->first();
            if ($Friendship1) {
                $Friendship1->delete();
            }
            $Friendship2 = Friend::where('receiver_id', $receiver_id_id)
            ->where('sender_id', $this->id)
            ->first();
            if ($Friendship2) {
                $Friendship2->delete();
            }
        }
    }

    public function friends() {
        $friends = array();
        $f1 = Friend::where('status', 1)
            ->where('sender_id', $this->id)
            ->get();
        foreach($f1 as $friendship):
            array_push($friends, User::find($friendship->receiver_id));
        endforeach;
        $friends2 = array();
        $f2 = Friend::where('status', 1)
            ->where('receiver_id', $this->id)
            ->get();
        foreach($f2 as $friendship):
            array_push($friends2, User::find($friendship->sender_id));
        endforeach;
        return array_merge($friends, $friends2);
    }

    public function pending_friend_requests() {
		$users = array();
		$friendships = Friend::where('status', 0)
            ->where('receiver_id', $this->id)
            ->get();
		foreach($friendships as $friendship):
			array_push($users, User::find($friendship->sender_id));
		endforeach;
		return $users;
    }

    public function friends_ids() {
        return collect($this->friends())->pluck('id')->toArray();
    }

    public function is_friends_with($id) {
		if(in_array($id, $this->friends_ids())) {
            return 1;
        } else {
            return 0;
        }
    }

    public function pending_friend_requests_ids() {
		return collect($this->pending_friend_requests())->pluck('id')->toArray();
    }

    public function pending_friend_requests_sent() {
		$users = array();
		$friendships = Friend::where('status', 0)
            ->where('sender_id', $this->id)
            ->get();
		foreach($friendships as $friendship):
			// array_push($users, User::find($friendship->receiver_id)->load('profile'));
			array_push($users, User::find($friendship->receiver_id));
		endforeach;
		return $users;
	}

	public function pending_friend_requests_sent_ids() {
		return collect($this->pending_friend_requests_sent())->pluck('id')->toArray();
	}

	public function has_pending_friend_request_from($user_id) {
		if(in_array($user_id, $this->pending_friend_requests_ids())) {
			return 1;
		}
		else {
			return 0;
		}
    }

    public function has_pending_friend_request_sent_to($user_id) {
		if(in_array($user_id, $this->pending_friend_requests_sent_ids())) {
			return 1;
		}
		else {
			return 0;
		}
    }
}