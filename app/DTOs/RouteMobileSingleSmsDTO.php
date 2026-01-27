<?php

namespace App\DTOs;

class RouteMobileSingleSmsDTO
{
    public function __construct(
        public string $destination,
        public string $message,
        public string $country = 'BD'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            destination: $data['destination'] ?? null,
            message: $data['message'] ?? null,
            country: $data['country'] ?? 'BD'
        );
    }
}