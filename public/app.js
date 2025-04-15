async function apiRequest(path, method = 'GET', body = null) {
    try {
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        };

        if (body) {
            options.body = JSON.stringify(body);
        }

        const response = await fetch(path, options);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `HTTP error, status ${response.status}`);
        }

        return data;
    } catch (error) {
        console.error(`apiRequest(): ${error}`);
        throw error;
    }
}

window.recipesApp = function () {
    return {
        recipes: [],
        loading: true,
        error: null,
        success: null,
        newRecipe: {
            name: '',
            description: '',
            ingredients: [''],
            submitting: false,
        },
        newRecipeVisible: false,

        addIngredient(targetArray, isObjectFormat = false) {
            const newIngredient = isObjectFormat ? {id: null, name: ''} : '';
            targetArray.push(newIngredient);
        },

        startEditingExistingRecipe(recipe) {
            recipe.editingData = JSON.parse(JSON.stringify(recipe.data));
            recipe.editing = true;
            recipe.error = null;
        },

        async submitNewRecipe() {
            //----- prevent user from double clicking save button
            if (this.newRecipe.submitting) return;
            this.newRecipe.submitting = true;

            try {
                const payload = {
                    name: this.newRecipe.name,
                    description: this.newRecipe.description,
                    ingredients: this.newRecipe.ingredients.filter(name => name.trim() !== ''),
                };

                const response = await apiRequest('/api/recipes', 'POST', payload);

                this.recipes.unshift(this.createRecipeComponentFromData(response.recipe));

                this.success = response.message;
                this.clearMessagesAfterDelay();

                //----- resetting form
                this.newRecipe = {name: '', description: '', ingredients: [''], submitting: false};
                this.newRecipeVisible = false;
                this.error = null;
            } catch (error) {
                this.error = error.message || 'Error adding recipe!';
                this.clearMessagesAfterDelay();

                this.newRecipe.submitting = false;
            }
        },

        init() {
            this.fetchRecipes();
        },

        createRecipeComponentFromData(recipeData) {
            return {
                data: recipeData,
                editingData: {},
                editing: false,
                error: null,
            };
        },

        async fetchRecipes() {
            try {
                const response = await apiRequest('/api/recipes');
                this.recipes = response.map(this.createRecipeComponentFromData);
                this.error = null;
            } catch (error) {
                this.error = error.message || 'Error fetching recipes!';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async saveExistingRecipe(recipe) {
            try {
                const payload = {
                    name: recipe.editingData.name,
                    description: recipe.editingData.description,
                    ingredients: recipe.editingData.ingredients.map(i => i.name),
                };

                const response = await apiRequest(`/api/recipes/${recipe.data.id}`, 'PUT', payload);

                recipe.data = response.recipe;
                recipe.editing = false;
                recipe.error = null;

                this.success = response.message;
                this.clearMessagesAfterDelay();
            } catch (error) {
                recipe.error = error.message || 'Error saving recipe!';
                this.clearMessagesAfterDelay();
            }
        },

        async deleteRecipe(recipe) {
            if (!confirm(`Are you sure you want to delete "${recipe.data.name}"?`)) return;

            try {
                const response = await apiRequest(`/api/recipes/${recipe.data.id}`, 'DELETE');

                //----- make sure to remove recipe from ui after deleting recipe
                this.recipes = this.recipes.filter(r => r.data.id !== recipe.data.id);

                this.success = response.message;
                this.clearMessagesAfterDelay();
            } catch (error) {
                this.error = error.message || 'Error deleting recipe!';
                this.clearMessagesAfterDelay();
            }
        },

        //----- show message for 3 seconds and remove it
        //----- one issue with this approach is that it will delete all alerts, which isn't ideal
        clearMessagesAfterDelay() {
            setTimeout(() => {
                this.success = null;
                this.error = null;
                this.recipes.forEach(recipe => recipe.error = null);
            }, 3000);
        },
    };
};
