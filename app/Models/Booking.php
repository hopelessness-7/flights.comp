<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_from',
        'flight_back',
        'date_from',
        'date_back',
        'code',
    ];

    public function flight_from_to_booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_from');
    }

    public function flight_back_to_booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_back');
    }

    public function passengers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Passenger::class);
    }
}
