<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EntityRoleUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Model $entity, protected string $newRole) {}

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
    public function toMail($notifiable): MailMessage
    {
        $entityName = $this->entity->name;
        return (new MailMessage)
            ->subject("Role Updated for $entityName")
            ->line("Your role at $entityName has been changed to: " . ucfirst($this->newRole) . '.')
            ->action('View Business', url('/dashboard'))
            ->line('If you have questions about your new permissions, please contact your administrator.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'entity_id' => $this->entity->id,
            'entity_name' => $this->entity->name,
            'new_role' => $this->newRole,
            'message' => "Your role at {$this->entity->name} was updated to {$this->newRole}.",
        ];
    }
}
