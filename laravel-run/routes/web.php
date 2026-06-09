<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormBuilderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FormBuilderController::class, 'index'])->name('form-builder');
