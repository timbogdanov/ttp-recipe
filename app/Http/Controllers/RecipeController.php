<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Models\Recipe;
use App\Services\RecipeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    protected RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(): JsonResponse
    {
        //----- use 'with' to eager load, preventing n+1 query issues
        //----- also a modification that could be done here if the resource becomes large, is adding a resource class to the recipe

        //----- also ignoring deleted rows by using 'use SoftDeletes' in Recipe model here
        $recipes = Recipe::with('ingredients')->orderBy('id', 'desc')->get();
        return response()->json($recipes);
    }

    //----- abstracted store and update into a service class, probably unnecessary here
    //----- but can become useful if the class grows in functionality.
    public function store(RecipeRequest $request): JsonResponse
    {
        try {
            $recipe = $this->recipeService->create($request->validated());
            return response()->json([
                'message' => 'Recipe created successfully.',
                'recipe'  => $recipe,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create recipe.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    //----- added an update function to handle the edit requests
    public function update(RecipeRequest $request, Recipe $recipe): JsonResponse
    {
        try {
            $recipe = $this->recipeService->update($recipe, $request->validated());
            return response()->json([
                'message' => 'Recipe updated successfully.',
                'recipe'  => $recipe,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update recipe.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Recipe $recipe): JsonResponse
    {
        DB::beginTransaction();
        try {
            $recipe->delete();

            DB::commit();

            return response()->json([
                'message' => 'Recipe deleted successfully.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete recipe.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
