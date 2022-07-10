<?php

namespace App\Domains\VideoChat\Notifications;

use App\Domains\Event\Models\Event;
use App\Domains\User\Models\User;
use App\Domains\VideoChat\Models\VideoChat;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VideoChatCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private $videoChat;

    public function __construct(VideoChat $videoChat)
    {
        $this->videoChat  = $videoChat;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * @param User $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject("Video chat notification")
            ->markdown(
                'mail.videoChatCreated',
                [
                    'event_link'  => config('front.url') . "/workspace/{$this->videoChat->event->workspace_id}/cases/{$this->videoChat->event->getKey()}",
                    'event_name'  => $this->videoChat->event->name,
                    'sender_name' => $this->videoChat->admin->getName(),
                    'video_chat' => $this->videoChat,
                ]
            );
    }

}
