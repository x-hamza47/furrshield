<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('admin', [AuthController::class, 'showLogin'])->name('login')->middleware('IsAuthenticated');
Route::post('admin/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('admin/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('admin/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('admin/register', [AuthController::class, 'register'])->name('user.register');

Route::get('dashboard/index', [AuthController::class, 'dashboard'])->name('dashboard.show')->middleware(['auth']);

Route::resource('dashboard/users', UserController::class);
