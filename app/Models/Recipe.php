<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    //----- using soft deletes in case we wanted to track history
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
