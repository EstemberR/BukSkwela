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
use App\Http\Controllers\SuperAdmin\PaymentController;

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
        Route::get('/register/success', [Controller::class, 'registerSuccess'])->name('register.success');

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

    // Super Admin Routes
    Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'superadmin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

        // Tenants Management
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'reject'])->name('reject');
            Route::post('/{id}/disable', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'disable'])->name('disable');
            Route::post('/{id}/deny', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'deny'])->name('deny');
            Route::post('/{id}/enable', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'enable'])->name('enable');
            Route::post('/{id}/downgrade', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'downgradePlan'])->name('downgrade');
            Route::post('/{id}/update-subscription', [App\Http\Controllers\SuperAdmin\TenantsController::class, 'updateSubscription'])->name('update-subscription');
        });

        // Payments Management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/mark-paid', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'markAsPaid'])->name('mark-paid');
            Route::get('/export', [App\Http\Controllers\SuperAdmin\PaymentController::class, 'export'])->name('export');
        });

        // Account Settings
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/settings', [App\Http\Controllers\SuperAdmin\AccountController::class, 'settings'])->name('settings');
            Route::put('/update-profile', [App\Http\Controllers\SuperAdmin\AccountController::class, 'updateProfile'])->name('update-profile');
            Route::put('/change-password', [App\Http\Controllers\SuperAdmin\AccountController::class, 'changePassword'])->name('change-password');
            Route::post('/logout', [App\Http\Controllers\SuperAdmin\AccountController::class, 'logout'])->name('logout');
        });

        // Payment Management Routes
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::put('/payments/{payment}/mark-as-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-as-paid');
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    });

    // Tenant Routes
    Route::middleware(['web', 'tenant'])
        ->group(function () {
            // Main dashboard route
            Route::get('/dashboard', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])
                ->name('tenant.dashboard')
                ->middleware('auth:admin');

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

    // Super Admin - Tenant Data Management
    Route::middleware(['auth', 'superadmin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/tenant-data', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'index'])->name('tenant-data.index');
        Route::get('/tenant-data/run-migration', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'runMigration'])->name('tenant-data.run-migration');
        Route::get('/tenant-data/run-batched-migration', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'runBatchedMigration'])->name('tenant-data.run-batched-migration');
        Route::get('/tenant-data/auto-setup', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'autoSetupDatabases'])->name('tenant-data.auto-setup');
        Route::get('/tenant-data/auto-migrate', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'autoMigrateAllDatabases'])->name('tenant-data.auto-migrate');
        Route::get('/tenant-data/{tenant}/check-database', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'checkDatabase'])->name('tenant-data.check-database');
        Route::get('/tenant-data/{tenant}/manage-database', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'manageTenantDatabase'])->name('tenant-data.manage-database');
        Route::post('/tenant-data/{tenant}/database-action', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'runDatabaseAction'])->name('tenant-data.database-action');
        Route::get('/tenant-data/{tenant}', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'viewTenantData'])->name('tenant-data.view');
        Route::get('/tenant-data/{tenant}/{table}', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'viewTableData'])->name('tenant-data.table');
        Route::get('/tenant-data/{tenant}/{table}/{id}/edit', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'editRecord'])->name('tenant-data.edit');
        Route::put('/tenant-data/{tenant}/{table}/{id}', [App\Http\Controllers\SuperAdmin\TenantDataController::class, 'updateRecord'])->name('tenant-data.update');
        
        // System Check Routes
        Route::get('/system-check/mysql', [App\Http\Controllers\SuperAdmin\SystemCheckController::class, 'checkMySQLConnections'])->name('system-check.mysql');
        Route::get('/system-check/mysql-ajax', [App\Http\Controllers\SuperAdmin\SystemCheckController::class, 'ajaxCheckMySQLStatus'])->name('system-check.mysql-ajax');
    });
