<?php

namespace App\Models\Concerns;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait IsReport
{
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
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return static::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    /** Status terakhir dianggap "selesai" (sudah ketemu / sudah diklaim). */
    public function getIsDoneAttribute(): bool
    {
        return $this->status === array_key_last(static::STATUSES);
    }
}
