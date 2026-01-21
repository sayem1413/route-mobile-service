<?php

namespace App\DTOs;

class SmsDTO
{
    public function __construct(
        public string $mobile,
        public string $message,
        public string $country = 'BD'
    ) {}
}
