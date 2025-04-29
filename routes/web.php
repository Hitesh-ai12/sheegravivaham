<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return require public_path('home.php');
});
