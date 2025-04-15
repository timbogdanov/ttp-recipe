<?php

namespace App\Services;

use App\Models\Recipe;
use Exception;
use Illuminate\Support\Facades\DB;

class RecipeService
{
    /**
     * @throws Exception
     */
    public function create(array $data): Recipe
    {
        //----- using db transactions here because im working with manipulating relationships
        DB::beginTransaction();
        try {
            $recipe = Recipe::create([
                'name'        => $data['name'],
                'description' => $data['description'],
            ]);

            $recipe->ingredients()->createMany(
                array_map(fn($name) => ['name' => $name], $data['ingredients'])
            );

            //----- commit if successful
            DB::commit();

            return $recipe->refresh()->load('ingredients');
        } catch (Exception $e) {
            //----- rolling back if failed
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function update(Recipe $recipe, array $data): Recipe
    {
        DB::beginTransaction();
        try {
            $recipe->update([
                'name'        => $data['name'],
                'description' => $data['description'],
            ]);

            $recipe->ingredients()->delete();

            $recipe->ingredients()->createMany(
                array_map(fn($name) => ['name' => $name], $data['ingredients'])
            );

            DB::commit();

            return $recipe->refresh()->load('ingredients');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
