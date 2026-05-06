<?php

namespace App\Notifications;

use App\Models\Auth\User;
use App\Models\Business\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CategoryRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $requester,
        protected string $categoryName,
        protected ?Service $service = null,
        protected ?string $serviceName = null,
        protected ?int $businessId = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $serviceName = $this->service?->name ?? $this->serviceName;
        $servicePart = $serviceName ? " for service {$serviceName}" : '';

        return [
            'action' => 'category_requested',
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
            'requester_email' => $this->requester->email,
            'service_id' => $this->service?->id,
            'service_name' => $serviceName,
            'business_id' => $this->service?->business_id ?? $this->businessId,
            'category_name' => $this->categoryName,
            'message' => "{$this->requester->name} requested new category '{$this->categoryName}'{$servicePart}.",
        ];
    }
}
