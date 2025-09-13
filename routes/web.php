<?php

use App\Models\Adoption;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\AdoptionRequestController;


Route::get('admin', [AuthController::class, 'showLogin'])->name('login')->middleware('IsAuthenticated');
Route::post('admin/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('admin/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('admin/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('admin/register', [AuthController::class, 'register'])->name('user.register');

Route::get('dashboard/index', [AuthController::class, 'dashboard'])->name('dashboard.show')->middleware(['auth']);

Route::resource('dashboard/users', UserController::class);

// ! Vet Routes
Route::get('dashboard/vets', [VetController::class, 'index'])->name('vets.index');
Route::get('dashboard/vets/edit/{id}', [VetController::class, 'edit'])->name('vets.edit');

Route::get('dashboard/shelter', [ShelterController::class, 'index'])->name('shelter.index');
Route::get('dashboard/shelter/edit/{id}', [ShelterController::class, 'edit'])->name('shelter.edit');

Route::resource('dashboard/appts', AppointmentController::class);
Route::get('/vet-slots/{vet}', [AppointmentController::class, 'vetSlots'])->name('vet.slots');

Route::resource('dashboard/shelter/adoption', AdoptionController::class);
Route::resource('dashboard/shelter/adoption-requests', AdoptionRequestController::class);

Route::post('adoption-requests/{id}/approve', [AdoptionRequestController::class, 'approve'])->name('adoption-requests.approve');
Route::post('adoption-requests/{id}/reject', [AdoptionRequestController::class, 'reject'])->name('adoption-requests.reject');

Route::get('adoption-requests/history', [AdoptionRequestController::class, 'history'])->name('adoption-requests.history');

//! Zain Health Record
Route::resource('dashboard/health-records', HealthRecordController::class);
Route::delete('/dashboard/health-records/pet/{id}', [HealthRecordController::class, 'petDestroy'])->name('health-records.pet.destroy');

//! Zain Products
// Route::get('dashboard/products', [ProductController::class, 'index'])->name('product.index');
// Route::post('dashboard/addToCart/{id}', [ProductController::class, 'add'])->name('product.add');
// Route::resource('products', ProductController::class);

Route::resource('dashboard/products', ProductController::class);


//! Zain Profile
Route::resource('profile', ProfileController::class);
Route::put('/profile/{id}/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.uploadAvatar');
Route::delete('/profile/{id}/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.removeAvatar');
