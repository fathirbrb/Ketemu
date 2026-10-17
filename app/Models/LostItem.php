<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostItem extends Model
{
    public const STATUSES = [
        'hilang' => 'Masih hilang',
        'ditemukan' => 'Sudah ditemukan',
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'event_date',
        'contact',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : null;
    }
}