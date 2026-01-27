<?php
namespace App\Contracts;

interface SMSDriverContract
{
    public function parseRmDlrCallbackData(array $payload): array;
}
