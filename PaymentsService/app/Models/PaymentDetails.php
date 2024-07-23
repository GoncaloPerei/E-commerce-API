<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_name',
        'card_number',
        'card_expiration_date',
        'card_code',
        'money',
    ];

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
