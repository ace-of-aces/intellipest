# Recipes

Guides for common setups.

## Watch Mode - Laravel Starter Kits

For applications based on one of the [official Laravel Starter Kits](https://laravel.com/starter-kits), there's an easy way to set up IntelliPest's watch mode, so you don't have to manually regenerate the helper file.

Modify the `dev` (and optionally `dev:ssr`) scripts in your `composer.json` to run IntelliPest in [watch mode](/configuration#watch-w) alongside your dev server and other processes:

```json
"dev": [
    "Composer\\Config::disableProcessTimeout",
    "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"pnpm run dev\" --names=server,queue,logs,vite --kill-others" // [!code --]
    "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#eb93fd\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"pnpm run dev\" \"./vendor/bin/intellipest --watch\" --names=server,queue,logs,vite,intellipest --kill-others" // [!code ++]
],
```

> [!NOTE]
> This approach can be used in other setups too. Simply modify the script that manages your local development processes to include `./vendor/bin/intellipest --watch`.