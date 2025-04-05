<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offers';

    protected $fillable = [
        'title',
        'description',
        'discount_percentage',
        'offer_amount',
        'valid_from',
        'valid_until',
        'merchant_id',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'offer_amount' => 'decimal:2',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function voucherCodes()
    {
        return $this->hasMany(VoucherCode::class);
    }
}
