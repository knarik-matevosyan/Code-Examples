<?php

namespace App\Domains\VideoChat\Models;

use App\Domains\User\Models\User;
use App\Filters\Filter;
use App\Traits\Filterable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @method Builder filter(Filter $filter)
 *
 * @property User $user
 */
class VideoChatMember extends Model
{
    use Filterable, Sortable;

    /* Sortable columns */
    public    $sortables = ['video_chat_members.updated_at'];

    protected $table     = 'video_chat_members';

    protected $guarded  = [];

    protected $dates     = [
        'created_at',
        'updated_at',
    ];

    public function videoChat()
    {
        return $this->belongsTo(VideoChat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
