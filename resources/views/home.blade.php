<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Retro Recipes</title>

    <link rel="preconnect" href="https://fonts.bunny.net"/>
    <link rel="stylesheet" href="{{ url('app.css') }}"/>

    <script src="{{ url('app.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
<h1 class="site-title">Retro Recipes</h1>

<div class="container" x-data="recipesApp()" x-init="init()">
    <div class="margin-y">
        <button class="primary" x-on:click="newRecipeVisible = !newRecipeVisible">New Recipe</button>

        <div class="card" x-show="newRecipeVisible" x-cloak>
            <fieldset>
                <label>
                    <p>Name</p>
                    <input type="text" x-model="newRecipe.name"/>
                </label>
                <label>
                    <p>Description</p>
                    <input type="text" x-model="newRecipe.description"/>
                </label>
                <label><p>Ingredients</p></label>
                <div class="box">
                    <template x-for="(ingredient, index) in newRecipe.ingredients" :key="index">
                        <p><input type="text" x-model="newRecipe.ingredients[index]"/></p>
                    </template>
                    <p>
                        <button type="button" x-on:click="addIngredient(newRecipe.ingredients)">Add ingredient</button>
                    </p>
                </div>

                {{-- changing save state here --}}
                <p>
                    <button class="primary" type="button" x-on:click="submitNewRecipe()" :disabled="newRecipe.submitting" x-text="newRecipe.submitting ? 'Saving...' : 'Save'"></button>
                </p>
            </fieldset>
        </div>
    </div>

    <div class="margin-y">
        {{-- added success and error messages --}}
        <div x-show="!!success" class="alert alert-success" x-cloak>
            <p x-text="success"></p>
        </div>

        <div x-show="!!error" class="alert alert-error" x-cloak>
            <p x-text="error"></p>
        </div>

        <div x-show="loading" class="loading-text">Loading recipes...</div>

        <div x-show="!loading" x-cloak>
            <template x-for="(recipe, index) in recipes" :key="index">
                <div class="card">
                    <div x-show="!recipe.editing">
                        <div class="flex">
                            <h3 class="recipe-title flex-grow" x-text="recipe.data.name"></h3>
                            <button x-on:click="startEditingExistingRecipe(recipe)">Edit</button>

                            {{-- added delete with confirmation to remove a recipe --}}
                            <button x-on:click="deleteRecipe(recipe)">Delete</button>
                        </div>
                        <p class="recipe-description" x-text="recipe.data.description"></p>
                        <ul>
                            <template x-for="(ingredient, index) in recipe.data.ingredients" :key="index">
                                <li x-text="ingredient.name"></li>
                            </template>
                        </ul>
                    </div>

                    <fieldset x-show="recipe.editing">
                        <label>
                            <p>Name</p>
                            <input type="text" x-model="recipe.editingData.name"/>
                        </label>
                        <label>
                            <p>Description</p>
                            <input type="text" x-model="recipe.editingData.description"/>
                        </label>
                        <label><p>Ingredients</p></label>
                        <div class="box">
                            <template x-for="(ingredient, index) in recipe.editingData.ingredients" :key="index">
                                <p><input type="text" x-model="ingredient.name"/></p>
                            </template>
                            <p>
                                <button type="button" x-on:click="addIngredient(recipe.editingData.ingredients, true)">Add ingredient</button>
                            </p>
                        </div>
                        <p class="flex">
                            <button x-on:click="recipe.editing = false">Cancel</button>
                            <button class="primary" x-on:click="saveExistingRecipe(recipe)">Save</button>
                        </p>

                        {{-- error messages per recipe creation --}}
                        <div x-show="!!recipe.error" class="error-text">
                            <p x-text="recipe.error"></p>
                        </div>
                    </fieldset>
                </div>
            </template>
        </div>
    </div>
</div>
