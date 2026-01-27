<?php
namespace App\DTOs;

class BulkNumberSmsDTO
{
    public function __construct(
        public array $recipients,
        public string $message,
        public string|null $senderId = null,
        public string|null $referenceId = null
    ) {}
}
