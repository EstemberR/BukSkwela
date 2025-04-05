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
        Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
            Route::get('/dashboard', function () {
                return view('tenant.admin.dashboard');
            })->name('tenant.admin.dashboard');
            
            // Add other admin routes here
        });

        // Test Google Drive Connection
        Route::get('/test-drive', function(\Google\Service\Drive $driveService) {
            try {
                // Try to list files in the root of Google Drive
                $files = $driveService->files->listFiles([
                    'pageSize' => 10,
                    'fields' => 'files(id, name, mimeType)'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully connected to Google Drive',
                    'files' => $files->getFiles()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Google Drive: ' . $e->getMessage()
                ], 500);
            }
        });
    });

// Tenant Routes
Route::middleware(['web', 'tenant'])
    ->prefix('admin')
    ->name('tenant.admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('tenant.admin.dashboard');
        })->name('dashboard');

        // Requirements
        Route::prefix('requirements')->name('requirements.')->group(function () {
            Route::get('/', [RequirementsController::class, 'index'])->name('index');
            Route::get('/folder/{folderId?}', [App\Http\Controllers\Requirements\RequirementsController::class, 'listFolderContents'])->name('folder.contents');
            Route::post('/folder/create', [RequirementsController::class, 'createFolder'])->name('folder.create');
            Route::post('/folder/rename', [RequirementsController::class, 'renameFolder'])->name('folder.rename');
            Route::delete('/folder/{folderId}', [RequirementsController::class, 'deleteFolder'])->name('folder.delete');
            
            // File management routes
            Route::post('/file/upload', [RequirementsController::class, 'uploadFile'])->name('file.upload');
            Route::delete('/file/{fileId}', [RequirementsController::class, 'deleteFile'])->name('files.delete');
            Route::get('/file/{fileId}', [RequirementsController::class, 'downloadFile'])->name('file.download');
        });
    });
