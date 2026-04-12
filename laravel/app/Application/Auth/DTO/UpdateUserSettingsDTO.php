<?php

namespace App\Application\Auth\DTO;

use Illuminate\Http\Request;

class UpdateUserSettingsDTO
{
    public function __construct(
        public readonly bool $notify_email = false,
        public readonly bool $notify_sms = false,
        public readonly bool $is_visible = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            notify_email:    $request->boolean('notify_email'),
            notify_sms:      $request->boolean('notify_sms'),
            is_visible: $request->boolean('is_visible'),
        );
    }

    public function toArray(): array
    {
        return [
            'notify_email'    => $this->notify_email,
            'notify_sms'      => $this->notify_sms,
            'is_visible' => $this->is_visible,
        ];
    }
}