<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'merchants';

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
