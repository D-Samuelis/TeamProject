<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EntityAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Model $entity,
        protected string $role
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];

        /* if ($notifiable->notify_email) {
            $channels[] = 'mail';
        } */

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $entityName = $this->entity->name;
        $type = strtolower(class_basename($this->entity));

        return (new MailMessage)
            ->subject("Assigned to $entityName")
            ->line("You have been assigned as a " . ucfirst($this->role) . " to the $type: $entityName.")
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for using our platform!');
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
            'entity_type' => class_basename($this->entity),
            'role' => $this->role,
            'message' => "You were assigned as {$this->role} to {$this->entity->name}"
        ];
    }
}
