<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subject extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'subjects';

    const UPDATED_AT = null;

    protected $fillable = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $dispatchesEvents = [];

    public function currentDetails()
    {
        return $this->hasOne(SubjectDetail::class)
            ->latest('created_at');
    }

    public function details()
    {
        return $this->hasMany(SubjectDetail::class);
    }
}
