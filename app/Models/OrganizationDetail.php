<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationDetail extends Model
{
    use HasFactory;

    protected $table = 'organization_details';

    const UPDATED_AT = null;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
