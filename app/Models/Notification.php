<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_type',
        'related_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public static function createForUser(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?Model $related = null,
    ): self {
        return static::query()->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_type' => $related?->getMorphClass(),
            'related_id' => $related?->getKey(),
        ]);
    }

    /**
     * @param  list<string>  $roles
     */
    public static function sendToRoles(
        array $roles,
        string $title,
        string $message,
        string $type = 'info',
        ?Model $related = null,
        ?User $except = null,
    ): void {
        User::query()
            ->select('id')
            ->whereIn('role', $roles)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->when($except !== null, fn ($query) => $query->where('id', '<>', $except->getKey()))
            ->each(function (User $recipient) use ($title, $message, $type, $related): void {
                self::createForUser((int) $recipient->getKey(), $title, $message, $type, $related);
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
