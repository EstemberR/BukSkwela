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
use App\Http\Controllers\Requirements\RequirementsController;
use Illuminate\Support\Facades\Route;
use Google\Client;

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

        // Google OAuth routes
        Route::get('/auth/google', function () {
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect_uri'));
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            $client->setScopes([
                'https://www.googleapis.com/auth/drive',
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive.metadata'
            ]);
            
            return redirect($client->createAuthUrl());
        })->name('google.auth');

        Route::get('/auth/google/callback', function () {
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect_uri'));
            
            $token = $client->fetchAccessTokenWithAuthCode(request('code'));
            
            if (isset($token['refresh_token'])) {
                return 'Your refresh token is: ' . $token['refresh_token'] . 
                       '<br>Add this to your .env file as GOOGLE_DRIVE_REFRESH_TOKEN';
            }
            
            return 'No refresh token was received. Please try again and make sure you revoke the application access in your Google Account settings first.';
        })->name('google.callback');
    });

    Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('superadmin.dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('superadmin.logout');
    });

    // Tenant Routes
    Route::middleware(['web', 'tenant'])
        ->group(function () {
            // Requirements Routes
            Route::prefix('admin/requirements')->name('tenant.admin.requirements.')->group(function () {
                Route::get('/', [App\Http\Controllers\Requirements\RequirementsController::class, 'index'])->name('index');
                Route::get('/folder/{folderId?}', [App\Http\Controllers\Requirements\RequirementsController::class, 'listFolderContents'])->name('folder.contents');
                Route::post('/folder/create', [App\Http\Controllers\Requirements\RequirementsController::class, 'createFolder'])->name('folder.create');
                Route::post('/file/upload', [App\Http\Controllers\Requirements\RequirementsController::class, 'uploadFile'])->name('file.upload');
                Route::delete('/file/{fileId}', [App\Http\Controllers\Requirements\RequirementsController::class, 'deleteFile'])->name('files.delete');
            });

            // Reports Routes
            Route::prefix('reports')->name('tenant.reports.')->group(function () {
                Route::get('/students', [App\Http\Controllers\Reports\ReportsController::class, 'students'])->name('students');
                Route::get('/staff', [App\Http\Controllers\Reports\ReportsController::class, 'staff'])->name('staff');
                Route::get('/courses', [App\Http\Controllers\Reports\ReportsController::class, 'courses'])->name('courses');
                Route::get('/requirements', [App\Http\Controllers\Reports\ReportsController::class, 'requirements'])->name('requirements');
            });
        });
