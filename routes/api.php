<?php

use Illuminate\Support\Facades\Route;
use Modules\Transaction\Http\Controllers\TransactionController;

Route::post('/transfer', TransactionController::class)->name('transfer');
