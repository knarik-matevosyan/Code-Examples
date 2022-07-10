<?php

namespace App\Domains\VideoChat\Services;

use App\Domains\User\Models\User;
use App\Domains\VideoChat\Models\VideoChat;
use App\Domains\VideoChat\Models\VideoChatMember;
use App\Domains\VideoChat\Notifications\VideoChatCreated;
use App\Domains\VideoChat\Notifications\VideoChatReminder;
use App\Domains\VideoChat\Notifications\VideoChatReminderDB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class VideoChatReminderServices
{
    public function queue(): void
    {
        $this->getMembers()->each(function(VideoChatMember $videoChatMember) {
             $videoChatMember->user->notify(new VideoChatReminder($videoChatMember->videoChat));
             $videoChatMember->user->notify(new VideoChatReminderDB($videoChatMember->videoChat));
        });
    }

    private function getMembers(): Collection
    {
        $chats = VideoChat::select('video_chats.id')
            ->whereRaw("start_time >= CONVERT_TZ( NOW(), 'UTC', users.timezone )" )
            ->whereRaw("start_time <= ( CONVERT_TZ( NOW(), 'UTC', users.timezone) + INTERVAL 30 MINUTE ) " )
            ->leftJoin('users', 'users.id', '=', 'admin_id')
            ->get();
        $chatIds = $chats->pluck('id')->all();
        $members = VideoChatMember::whereIn('video_chat_id', $chatIds)
            ->with(['user', 'videoChat'])
            ->get();
        return $members;
    }
}
