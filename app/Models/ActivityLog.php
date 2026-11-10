<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'module',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
    ];

    public static function record(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?User $user = null,
        ?Request $request = null,
    ): self {
        $request ??= request();

        return static::query()->create([
            'user_id' => $user?->getKey() ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'module' => $module,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
