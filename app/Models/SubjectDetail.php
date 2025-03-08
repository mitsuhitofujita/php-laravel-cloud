<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectDetail extends Model
{
    use HasFactory;

    protected $table = 'subject_details';

    const UPDATED_AT = null;

    protected $fillable = [
        'subject_id',
        'name',
        'description',
    ];

    protected $casts = [
        'subject_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
