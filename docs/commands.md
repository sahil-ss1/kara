# Useful Commands 

    composer install --optimize-autoloader --no-dev -> Autoloader Optimization
    php artisan key:generate
    php artisan storage:link ->create symlink (link storage/app/public folder inside public)
    //clear db and recreate with seeds
    php artisan migrate:fresh --seed

    composer update -> update vendors
    composer dumpautoload

    php artisan config:cache -> cache .env settings
    php artisan route:cache

    php artisan down -> down for maintenance
    php artisan up

    php artisan make:controller PostsController -r -mPost
    php artisan make:policy PostPolicy --model=Post    
    php artisan make:view users.index --extends=layouts.app --section=content
    php artisan make:livewire ShowPosts
    php artisan make:component Alert

    php artisan translatable:export en,el

    php artisan iseed my_table,another_table

    php artisan currency:manage add <currency>

    php artisan make:eloquent-filter NameFilter
    php artisan make:eloquent-filter Models/Product/NameFilter --field=name

