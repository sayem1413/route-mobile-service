<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class RmBulkSms extends Model
{
    use HasFactory;

    protected $table = 'rm_bulk_sms';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'to',
        'sender',
        'message',
        'message_id',
        'status',
        'response',
        'sent_at',
        'delivered_at',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'response'     => 'array',
        'sent_at'      => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Status constants (avoid magic strings)
     */
    public const STATUS_QUEUED    = 'queued';
    public const STATUS_SENT      = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED    = 'failed';

    /**
     * Model boot hooks
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->status ??= self::STATUS_QUEUED;
        });
    }

    /**
     * Mark SMS as sent
     */
    public function markAsSent(string|null $messageId = null, array|null $response = null): void
    {
        $this->update([
            'status'     => self::STATUS_SENT,
            'message_id' => $messageId ?? $this->message_id,
            'response'   => $response ?? $this->response,
            'sent_at'    => Carbon::now(),
        ]);
    }

    /**
     * Mark SMS as delivered
     */
    public function markAsDelivered(array|null $response = null): void
    {
        $this->update([
            'status'       => self::STATUS_DELIVERED,
            'response'     => $response ?? $this->response,
            'delivered_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark SMS as failed
     */
    public function markAsFailed(array|string|null $response = null): void
    {
        $this->update([
            'status'   => self::STATUS_FAILED,
            'response' => $response,
        ]);
    }
}
