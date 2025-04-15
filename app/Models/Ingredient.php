<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;

    //----- making sure im only allowed to edit specified fields on mass assignments
    protected $fillable = [
        'recipe_id',
        'name'
    ];

    //----- its nice seeing devs specifying return types for relationships, kudos to Labinger
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
