<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObserverDetail extends Model
{
    use HasFactory;

    protected $table = 'observer_details';

    const UPDATED_AT = null;

    protected $fillable = [
        'observer_id',
        'name',
        'description',
    ];

    protected $casts = [
        'observer_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function observer(): BelongsTo
    {
        return $this->belongsTo(Observer::class);
    }
}