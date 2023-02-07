<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Flight extends Model
{
    use HasFactory, QueryCacheable, SoftDeletes;

    protected int $cacheFor = 180;

    protected $fillable = [
        'flight_code',
        'from_id',
        'to_id',
        'time_from',
        'time_to',
        'cost',
        'date',
        'count_passengers'
    ];

    public function from_airport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Airport::class, 'from_id');
    }

    public function to_airport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Airport::class, 'to_id');
    }

    public function from_booking(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class, 'flight_from');
    }

    public function back_booking(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class, 'flight_back');
    }
}
