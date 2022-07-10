<?php

namespace App\Domains\VideoChat\Services;

use App\Domains\Event\Models\Event;
use App\Domains\VideoChat\Filters\VideoChatsFilter;
use App\Domains\VideoChat\Models\VideoChat;
use App\Domains\Event\Models\EventMember;
use App\Domains\User\Models\User;
use App\Domains\VideoChat\Notifications\VideoChatCreated;
use App\Domains\VideoChat\Notifications\VideoChatCreatedDB;
use App\Domains\Workspace\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VideoChatServices
{
    private $videoChatMemberService;

    public function __construct(VideoChatMemberServices $videoChatMemberServices)
    {
        $this->videoChatMemberService = $videoChatMemberServices;
    }


    public function paginate(VideoChatsFilter $filter, array $params)
    {
        $perPage       = $params['per_page'] ?? null;
        $sortBy        = $params['sort_by'] ?? 'video_chats.created_at';
        $sortDirection = $params['sort_direction'] ?? 'desc';
        $query = $this->queryChat()
            ->filter($filter);

        return $query
            ->sort($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    private function queryChat(): Builder
    {
        $query = VideoChat::query()
            ->select(['video_chats.*']);
        return $query;
    }

    public function createVideoChat(Event $event, User $user, array $params)
    {
        $videoChat = VideoChat::create(
            [
                'workspace_id' => $event->workspace_id,
                'admin_id' => $user->id,
                'event_id' => $event->id,
                'start_time' => $params['start_time'],
                'end_time' => $params['end_time'] ?? date('Y-m-d H:i:s', strtotime($params['start_time'] . ' +30 minutes')),
            ]
        );
        $userIds = $params['members'] ?? [];

        if($userIds){
            $this->videoChatMemberService->addUsersToChat($userIds, $videoChat->fresh());

            $users = User::whereIn('id', $userIds)->get();
            foreach ($users as $userItem){
                if($userItem->id !== $user->id){
                    $userItem->notify(
                        new VideoChatCreated($videoChat)
                    );
                    $userItem->notify(
                        new VideoChatCreatedDB($videoChat)
                    );
                }
            }
        }
        return $videoChat;
    }

}
