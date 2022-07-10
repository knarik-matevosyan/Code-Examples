<?php

namespace App\Domains\VideoChat\Models;

use App\Domains\Event\Models\Event;
use App\Domains\User\Models\User;
use App\Domains\Workspace\Models\Workspace;
use App\Filters\Filter;
use App\Traits\Filterable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Builder
 * @method Builder filter(Filter $filter)
 */
class VideoChat extends Model
{
    use Filterable, Sortable;

    protected $guarded = [];

    protected $dates    = [
        'created_at',
        'updated_at',
    ];


    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function members()
    {
       return $this->belongsToMany(User::class, 'video_chat_members', 'video_chat_id', 'user_id')
           ->withTimestamps();
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            VideoChatMember::class,
            'video_chat_id',
            'id',
            null,
            'user_id'
        );
    }

    public function members_except(int $userId)
    {
        return $this->hasMany(VideoChatMember::class, 'video_chat_id', 'id')
            ->where('user_id', '!=', $userId);
    }

    public function members_only(int $userId)
    {
        return $this->hasMany(VideoChatMember::class, 'video_chat_id', 'id')
            ->where('user_id', $userId);
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
