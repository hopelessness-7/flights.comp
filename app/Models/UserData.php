<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_data';

    protected $fillable = [
        'first_name',
        'last_name',
        'document_number',
        'birth_date'
    ];

    public function passengers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }
}
