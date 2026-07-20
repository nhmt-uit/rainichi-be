## Development
Clone project from git url:
 
`https://gitlab.bstarsolutions.com/rainichi/backend.git`

Required package:

```composer, nodejs, php(>7.1.3), mariadb```

After install all required package, follow step in below: 

```cp .env.example .env```

Config database in env file.

`DB_DATABASE=rainichi`

`DB_USERNAME=username`

`DB_PASSWORD=password`

run:

`composer install`

`php artisan migrate`

`php artisan passport:install`

## Depolyment
To link all file from storage local to public, run (if not using media server):

`php artisan storage:link`
 
 To create some value default
   - Create config default: php artisan db:seed --class=ConfigurationTableSeeder
   - Create category default: php artisan db:seed --class=CategoryTableSeeder
   - Convert all category slug to slug standard: php artisan db:seed --class=CategoryConvertSeed
   - Convert all article slug to slug standard: php artisan db:seed --class=ArticleConvertSeed
   - Create country default: php artisan db:seed --class=CountryTableSeeder
   - Create course currency default: php artisan db:seed --class=CoursePriceCurrencyTableSeeder
   - Update course price to personal: php artisan db:seed --class=CoursePriceTableSeeder
   - Create level default (N1-N5, Basic): php artisan db:seed --class=LevelTableSeeder
   - Create default user avatars (requires at least one user to already exist, used as `created_by`): php artisan db:seed --class=DefaultAvatarTableSeeder
    

if you are using apache as a web service. run:

`php artisan serve`

it'll run in port 8000
