<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Organization extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'organizations';

    const UPDATED_AT = null;

    protected $fillable = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $dispatchesEvents = [];

    public function details(): HasMany
    {
        return $this->hasMany(OrganizationDetail::class);
    }

    public function latestDetail(): HasOne
    {
        return $this->hasOne(OrganizationDetail::class)->latest('created_at');
    }

    public function getNameAttribute(): string
    {
        return $this->latestDetail?->name;
    }

    public function getDescriptionAttribute(): string
    {
        return $this->latestDetail?->description;
    }

    public function observers(): BelongsToMany
    {
        return $this->belongsToMany(Observer::class)->withTimestamps();
    }
}