<?php

namespace App\Domains\VideoChat\Notifications;

use App\Domains\Event\Models\Event;
use App\Domains\Notification\Models\NotificationSetting;
use App\Domains\Notification\Services\NotificationSettingsServices;
use App\Domains\User\Models\User;
use App\Domains\VideoChat\Models\VideoChat;
use App\Notifications\NotificationDb;


class VideoChatCreatedDB extends NotificationDb
{
    private $event;
    private $sender;
    private $videoChat;

    public function __construct(VideoChat $videoChat)
    {
        $this->videoChat = $videoChat;
        $this->event  = $videoChat->event;
        $this->sender = $videoChat->admin;

    }

    public function getName(): string
    {
        return 'Video chat created';
    }

    public function getUser(): User
    {
        return $this->sender;
    }

    public function getNotice(): ?NotificationSetting
    {
        return NotificationSetting::filterBySlug(NotificationSettingsServices::NOTIFY_EVENT_UPDATED)->first();
    }

    public function getData(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->getName(),
            'workspace_id' => $this->sender->getCurrentWorkspaceId(),
            'event'        => [
                'id'           => $this->event->id,
                'name'         => $this->event->name,
                'workspace_id' => $this->event->workspace_id,
            ],
            'sender'       => [
                'id'      => $this->sender->getKey(),
                'name'    => $this->sender->getName(),
                'avatar'  => $this->sender->getAvatar(),
                'message' => "<strong>{$this->sender->name}</strong> created a video chat. Start time at <strong>"
                              . date('F jS, g:i A', strtotime($this->videoChat->start_time)) ."</strong>",
            ],
        ];
    }

}
