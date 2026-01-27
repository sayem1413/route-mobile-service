<?php

namespace App\DTOs;

use InvalidArgumentException;

class RouteMobileSingleSmsDTO
{
    public function __construct(
        public string $destination,
        public string $message,
        public string $country = 'BD'
    ) {}

    public static function fromArray(array $data): self
    {
        if (empty($data['destination']) || empty($data['message'])) {
            throw new InvalidArgumentException('Destination and message are required.');
        }

        return new self(
            destination: $data['destination'] ?? null,
            message: $data['message'] ?? null,
            country: $data['country'] ?? 'BD'
        );
    }
}