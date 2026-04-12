<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EntityRemovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Model $entity)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_email) {
            $channels[] = 'mail';
        }

        if ($notifiable->notify_sms && $notifiable->phone_number) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Access Revoked: {$this->entity->name}")
            ->line("We are writing to inform you that your access to {$this->entity->name} has been revoked.")
            ->line('You will no longer be able to manage this business entity.')
            ->line('Thank you for your previous contributions.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "Your access to {$this->entity->name} has been revoked.",
            'business_id' => $this->entity->id,
            'action' => 'removed',
        ];
    }
}
