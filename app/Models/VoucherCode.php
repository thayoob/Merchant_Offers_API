<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'voucher_codes';

    protected $fillable = [
        'code',
        'offer_id',
        'valid_date',
        'status',
    ];

    protected $casts = [
        'valid_date' => 'datetime',
        'status' => 'string',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_EXPIRED,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
