<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-email', function () {
    \Mail::raw('Test email dari Laravel', function ($message) {
        $message->to('ichsanmuhammed01@gmail.com')
                ->subject('Test Email');
    });

    return 'Email sent!';
});
