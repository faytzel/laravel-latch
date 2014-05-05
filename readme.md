## Laravel Latch Integration (Alpha version)

## Installation

Require this package in your composer.json and run composer update:

    "faytzel/laravel-latch": "0.*"

After updating composer, add the Service Provider to the providers array in app/config/app.php

    'Faytzel\LaravelLatch\LaravelLatchServiceProvider',

You add config files.

    $ php artisan config:publish faytzel/laravel-latch

If you want to use the facade, add this to your facades in app/config/app.php

    'Latch' => 'Faytzel\LaravelLatch\Facades\LaravelLatch',

## Examples

### Pair with Latch Account

    $token = Input::get('token');

    if ($accountId = Latch::pair($token))
    {
        // Add account id latch to user table
    }
    else
    {
        echo Latch::error();
    }

### Check it if locked Latch Account

    $accountId = 'latch_account_id';

    if ( ! Latch::locked($accountId))
    {
        // Auth user
    }

### Check if unlocked Latch Account

    $accountId = 'latch_account_id';

    if (Latch::unlocked($accountId))
    {
        // Auth user
    }

### Unpair Latch Account

    $accountId = 'latch_account_id';

    if (Latch::unpair($accountId))
    {
        // Delete account id latch in user table
    }
    else
    {
        echo Latch::error();
    }