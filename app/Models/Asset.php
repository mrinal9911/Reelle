<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category',
        'subcategory',
        'name',
        'serial_number',
        'description',
        'value',
        'verification_level',
        'attachments',
        'blockchain_token_id',
        'transfer_history',
        'visibility',
        'is_reported_lost',
        'is_listed_for_sale',
        'is_visible',
    ];

    protected $casts = [
        'attachments' => 'array',
        'transfer_history' => 'array',
        'value' => 'decimal:2',
        'is_reported_lost' => 'boolean',
        'is_listed_for_sale' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
