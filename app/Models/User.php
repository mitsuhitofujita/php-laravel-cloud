<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Jobs\CreateObserverForUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * イベントをトランザクション完了後に発行する
     */
    protected $afterCommit = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\UserCreated::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the observers associated with the user.
     */
    public function observers(): BelongsToMany
    {
        return $this->belongsToMany(Observer::class)->withTimestamps();
    }

    /**
     * Get the default observer associated with the user.
     * If no observer exists, create one.
     */
    public function getDefaultObserver(): ?Observer
    {
        return $this->observers()->first();
    }

    /**
     * 関連するObserverを作成するジョブをディスパッチします。
     * イベント以外の場所からでも再利用可能です。
     *
     * @return void
     */
    public function createObserver(): void
    {
        CreateObserverForUser::dispatch($this);
    }
}
