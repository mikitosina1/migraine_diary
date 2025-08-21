## MigraineDiary

<hr>

### Description:

This is a module for tracking migraines, symptoms, and treatment. <br>
![PHP](https://img.shields.io/badge/PHP-^8.1-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) <br>
You can find the rest of the dependencies in composer.json and package.json.

### Installation:

#### install to directory '$root/Modules/' <br>

#### run `composer install` <br>

#### run `npm install` <br>

#### run migration: `php artisan migrate --path=Modules/MigraineDiary/Database/migrations` <br>

#### run seeder:

`php artisan db:seed --class=MigraineDiaryDatabaseSeeder`
<br> or <br>
`php artisan db:seed --class=Modules\\MigraineDiary\\Database\\Seeders\\MigraineDiaryDatabaseSeeder`

