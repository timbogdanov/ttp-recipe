```sh
# Clone the repository
git clone https://github.com/timbogdanov/ttp-recipe.git
cd ttp-recipe

# Install dependencies
composer update

# Run the application
./vendor/bin/sail up

# While the application is running, open another terminal and initialize the database
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```
