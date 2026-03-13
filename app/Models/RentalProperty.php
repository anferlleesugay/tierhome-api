<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'city',
        'barangay',
        'monthly_rent',
        'deposit',
        'type',
        'bedrooms',
        'bathrooms',
        'area_sqm',
        'has_wifi',
        'has_parking',
        'has_aircon',
        'has_water',
        'has_electricity',
        'is_pet_friendly',
        'is_furnished',
        'is_available',
    ];

    protected $casts = [
        'has_wifi'         => 'boolean',
        'has_parking'      => 'boolean',
        'has_aircon'       => 'boolean',
        'has_water'        => 'boolean',
        'has_electricity'  => 'boolean',
        'is_pet_friendly'  => 'boolean',
        'is_furnished'     => 'boolean',
        'is_available'     => 'boolean',
        'monthly_rent'     => 'decimal:2',
        'deposit'          => 'decimal:2',
    ];

    // A property belongs to a landlord
    public function landlord()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}