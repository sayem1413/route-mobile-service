<?php

namespace App\DTOs;

class RouteMobileBulkSmsDTO
{
    public function __construct(
        public string $destination,
        public string $message,
        public string $country = 'BD'
    ) {}
}
