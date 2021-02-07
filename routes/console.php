<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('aparat:clear', function () {
    clear_storage('videos');
    $this->info('Clear Uploaded Videos Files');
    clear_storage('category');
    $this->info('Clear Uploaded Categories Files');
    clear_storage('channels');
    $this->info('Clear Uploaded Channels Files');
})->purpose('Clear all temporary files and folders');
