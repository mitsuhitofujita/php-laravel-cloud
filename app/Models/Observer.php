<?php

namespace App\Models;

use App\Events\ObserverCreated;
use App\Jobs\CreateOrganizationForObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Observer extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'observers';

    const UPDATED_AT = null;

    protected $fillable = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * イベントをトランザクション完了後に発行する
     */
    protected $afterCommit = true;

    protected $dispatchesEvents = [
        'created' => ObserverCreated::class,
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ObserverDetail::class);
    }

    public function latestDetail(): HasOne
    {
        return $this->hasOne(ObserverDetail::class)->latest('created_at');
    }

    public function getNameAttribute(): string
    {
        return $this->latestDetail?->name;
    }

    public function getDescriptionAttribute(): string
    {
        return $this->latestDetail?->description;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withTimestamps();
    }

    /**
     * Get the default private organization associated with the observer.
     * If no organization exists, create one.
     */
    public function getDefaultOrganization(): ?Organization
    {
        return $this->organizations()->first();
    }

    /**
     * 関連する個人用Organizationを作成するジョブをディスパッチします。
     * イベント以外の場所からでも再利用可能です。
     */
    public function createOrganization(): void
    {
        CreateOrganizationForObserver::dispatch($this);
    }
}
