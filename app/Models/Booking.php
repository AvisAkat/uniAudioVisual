<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'booking_date',
        'start_time',
        'end_time',
        'quantity',
        'purpose',
        'additional_notes',
        'status',
        'rejection_reason',
        'returned_by',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'quantity' => 'integer',
            'returned_at' => 'datetime',
        ];
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

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}
