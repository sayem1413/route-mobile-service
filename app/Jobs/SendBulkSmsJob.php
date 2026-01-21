<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\DTOs\SmsMessageDTO;
use App\Services\Sms\SmsManager;

class SendBulkSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private SmsMessageDTO $dto)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SmsManager $smsManager): void
    {
        $response = $smsManager->driver()->sendBulk($this->dto);

        // Store message_id, provider response
    }
}
