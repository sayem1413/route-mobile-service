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
        'status_code',
        'response',
        'gateway_error',
        'sent_at',
        'delivered_at',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
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
     * Mark as sent (Route Mobile accepted)
     */
    public function markAsSent(
        ?string $messageId = null,
        ?string $statusCode = null,
        array|string|null $response = null,
        string|null $gatewayError = null
    ): void {
        $this->update([
            'status'      => self::STATUS_SENT,
            'message_id'  => $messageId ?? $this->message_id,
            'status_code' => $statusCode,
            'response'      => $response ?? $this->response,
            'gateway_error' => $gatewayError,
            'sent_at'     => Carbon::now(),
        ]);
    }

    /**
     * Mark as delivered (DLR)
     */
    public function markAsDelivered(
        ?string $statusCode = null,
        array|string|null $response = null
    ): void {
        $this->update([
            'status'       => self::STATUS_DELIVERED,
            'status_code'  => $statusCode,
            'response'     => $response,
            'delivered_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(
        ?string $statusCode = null,
        array|string|null $response = null,
        string|null $gatewayError = null
    ): void {
        $this->update([
            'status'      => self::STATUS_FAILED,
            'status_code' => $statusCode,
            'response'    => $response ?? $this->response,
            'gateway_error' => $gatewayError,
        ]);
    }

    /**
     * Map Route Mobile DLR status to internal status
     * 
     * 
     * Status of the message from DLR push API from route mobile. 
     * ('DELIVRD', 'ACKED', 'ENROUTE', 'ACCEPTED', 'UNKNOWN', 'EXPIRED', 'DELETED', 'UNDELIV', 'REJECTD')
     */
    public static function mapRouteMobileStatus(string $rmStatus): string
    {
        return match (strtoupper($rmStatus)) {
            'DELIVRD' => self::STATUS_DELIVERED,

            'ACKED',
            'ENROUTE',
            'ACCEPTED' => self::STATUS_SENT,

            'UNKNOWN',
            'EXPIRED',
            'DELETED',
            'UNDELIV',
            'REJECTD' => self::STATUS_FAILED,

            default => self::STATUS_QUEUED,
        };
    }
}
