<?php

namespace App\Models;

use App\Models\Like;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'parent_id', 
        'body'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = ['user', 'comments'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'liked', 'disliked', 'timeAgo'
    ];

    public function getTimeAgoAttribute() {
        return $this->created_at->diffForHumans();
    }

    public function getLikedAttribute() {
        return $this->likes()->where('like', 1)
            ->where('likeable_id', $this->id)
            ->where('likeable_type', get_class($this))
            ->count();
    }

    // public function getdislikedAttribute() {
    //     return $this->likes()->where('dislike', 1)
    //         ->where('likeable_id', $this->id)
    //         ->where('likeable_type', get_class($this))
    //         ->count();
    // }

    // public function scopeAllPosts($query) {
    //     return $query->where('user_id', auth()->id())
    //     ->orWhereIn('user_id', auth()->user()->friends_ids());
    // }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
