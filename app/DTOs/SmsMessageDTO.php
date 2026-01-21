<?php
namespace App\DTOs;

class SmsMessageDTO
{
    public function __construct(
        public array $recipients,
        public string $message,
        public string|null $senderId = null,
        public string|null $referenceId = null
    ) {}
}
