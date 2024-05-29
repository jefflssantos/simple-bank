<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Models\User;

Route::get('/', function () {

    User::factory()->create();

    return view('welcome');
});
