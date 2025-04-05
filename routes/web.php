<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TenantRegistrationController;
use App\Http\Controllers\RequirementController;
use Illuminate\Support\Facades\Route;

// Central domain routes
Route::middleware(['web'])
    ->withoutMiddleware(['tenant'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        // Admin login on central domain
        Route::get('/login', [LoginController::class, 'showLoginForm'])
            ->name('login');
        Route::post('/login', [LoginController::class, 'login'])
            ->name('login.post');
        Route::post('/logout', [LoginController::class, 'logout'])
            ->name('logout');

        // Tenant registration routes
        Route::get('/register', [Controller::class, 'register'])->name('register');
        Route::post('/register', [Controller::class, 'registerSave'])->name('register.save');

        // Student Requirements Route
        Route::get('/requirements', [RequirementController::class, 'showStudentRequirements'])
            ->name('student.requirements')
            ->middleware(['auth', 'role:student']);
    });

    Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('superadmin.logout');
});
