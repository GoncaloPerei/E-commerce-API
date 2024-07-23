<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_number',
        'card_type_id',
        'card_expiration_date',
        'card_code',
        'user_id',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'card_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'card_code' => 'hashed',
        ];
    }

    /**
     * Accessor for card_number attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getCardNumberAttribute($value): string
    {
        $visibleDigits = 4;
        $maskedValue = str_pad(substr($value, -1 * $visibleDigits), strlen($value), '*', STR_PAD_LEFT);
        return $maskedValue;
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function types(): BelongsTo
    {
        return $this->belongsTo(CardType::class, 'card_type_id', 'id');
    }
}
