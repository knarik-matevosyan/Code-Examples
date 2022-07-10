<?php

namespace App\Domains\VideoChat\Services;


use App\Domains\VideoChat\Models\VideoChat;
use App\Domains\VideoChat\Models\VideoChatMember;

use App\Domains\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VideoChatMemberServices
{
    public function addUsersToChat(array $users, VideoChat $chat): bool
    {
        $users = array_unique($users);

        $chat->members()->attach($users);

        return true;
    }


    public function isUserBelongsToChat(User $user, VideoChat $chat): bool
    {
        return $chat->members_only($user->getKey())
            ->exists();
    }
}
