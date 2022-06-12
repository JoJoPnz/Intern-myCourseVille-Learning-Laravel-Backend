<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function telephones()
    {
        return $this->hasMany(Telephone::class);
    }
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function owners()
    {
        return $this->belongsToMany(User::class);
    }
}
