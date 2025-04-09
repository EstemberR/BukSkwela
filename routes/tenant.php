<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider
| with the tenancy and web middleware groups. Good luck!
|
*/



use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Staff\StaffAuthController;
use App\Http\Controllers\Staff\StaffRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Requirement\RequirementController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])
            ->name('tenant.login');
        Route::post('/login', [LoginController::class, 'login'])
            ->name('tenant.login.post');
        Route::get('/status', [App\Http\Controllers\Auth\TenantStatusController::class, 'checkStatus'])
            ->name('tenant.status');
    });

// Protected tenant routes
Route::middleware(['web', 'tenant', 'auth:admin'])
    ->group(function () {
        // Main dashboard route
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');

        Route::prefix('admin')->group(function () {
            // Remove the admin dashboard route since we're using the main one
            Route::get('/staff/register', [StaffRegistrationController::class, 'showRegistrationForm'])
                ->name('staff.register');
            Route::post('/staff/register', [StaffRegistrationController::class, 'register'])
                ->name('staff.register.save');
            Route::post('/instructor', [StaffRegistrationController::class, 'register'])
                ->name('tenant.instructor.store');
            
            // Direct delete routes without model binding - must be before other student routes
            Route::post('/students/delete-direct/{id}', [StudentController::class, 'deleteDirectly'])
                ->name('tenant.students.delete.direct.post');
            Route::get('/students/delete-direct/{id}', [StudentController::class, 'deleteDirectly'])
                ->name('tenant.students.delete.direct');
                
            // Simple POST endpoint that uses the request body instead of URL parameters
            Route::post('/students/delete-simple', [StudentController::class, 'deleteSimple'])
                ->name('tenant.students.delete.simple');
                
            // Direct update route without model binding
            Route::post('/students/update-direct/{id}', [StudentController::class, 'updateDirectly'])
                ->name('tenant.students.update.direct');
                
            // Direct store route without model binding  
            Route::post('/students/add-direct', [StudentController::class, 'storeDirectly'])
                ->name('tenant.students.store.direct');
                
            // Test route for debugging student lookup
            Route::get('/students/test-lookup/{id?}', [StudentController::class, 'testStudentLookup'])
                ->name('tenant.students.test.lookup');
            
            Route::get('/students', [StudentController::class, 'index'])
                ->name('tenant.students.index');
            Route::post('/students', [StudentController::class, 'store'])
                ->name('tenant.students.store');
            Route::put('/students/{student}', [StudentController::class, 'update'])
                ->name('tenant.students.update');
            Route::delete('/students/{student}', [StudentController::class, 'destroy'])
                ->name('tenant.students.destroy');
            Route::post('/students/{student}/delete', [StudentController::class, 'destroy'])
                ->name('tenant.students.delete');
            // Add a GET route for delete as a last resort workaround
            Route::get('/students/{student}/delete', [StudentController::class, 'destroy'])
                ->name('tenant.students.delete.get');
            Route::get('/staff', [StaffController::class, 'index'])
                ->name('tenant.staff.index');
            Route::post('/staff', [StaffController::class, 'store'])
                ->name('tenant.staff.store');
            Route::put('/staff/{staff}', [StaffController::class, 'update'])
                ->name('tenant.staff.update');
            Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])
                ->name('tenant.staff.destroy');
            Route::get('/courses', [CourseController::class, 'index'])
                ->name('tenant.courses.index');
            Route::post('/courses', [CourseController::class, 'store'])
                ->name('tenant.courses.store');
            Route::put('/courses/{course}', [CourseController::class, 'update'])
                ->name('tenant.courses.update');
            Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
                ->name('tenant.courses.destroy');
            // Alternative formats for course deletion to support various client implementations
            Route::post('/courses/{course}/delete', [CourseController::class, 'destroy'])
                ->name('tenant.courses.delete');
            Route::get('/courses/{course}/delete', [CourseController::class, 'destroy'])
                ->name('tenant.courses.delete.get');
            Route::post('/courses/delete-direct/{id}', [CourseController::class, 'destroyDirect'])
                ->name('tenant.courses.delete.direct');
            // Direct update route without model binding
            Route::post('/courses/update-direct/{id}', [CourseController::class, 'updateDirect'])
                ->name('tenant.courses.update.direct');
            Route::get('/requirements', [RequirementController::class, 'index'])
                ->name('tenant.requirements.index');
            Route::post('/requirements', [RequirementController::class, 'store'])
                ->name('tenant.requirements.store');
            Route::delete('/requirements/{requirement}', [RequirementController::class, 'destroy'])
                ->name('tenant.requirements.destroy');
        });
    });

// Staff routes
Route::prefix('staff')->group(function () {
    // Staff auth routes (no auth required)
    Route::get('/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('staff.login.post');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
    Route::get('/tenant-logout', [StaffAuthController::class, 'tenantLogout'])->name('staff.tenant.logout');

    // Protected staff routes
    Route::middleware(['auth:staff'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])
            ->name('staff.dashboard');
    });
});

// Course Management Routes
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::post('/', [CourseController::class, 'store'])->name('store');
    Route::put('/{course}', [CourseController::class, 'update'])->name('update');
    Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
});

Route::middleware(['web', 'tenant'])->group(function () {
    // Admin routes
    Route::prefix('admin')->name('tenant.admin.')->middleware(['auth:admin'])->group(function () {
        // Requirements management routes
        Route::prefix('requirements')->name('requirements.')->group(function () {
            try {
                Route::get('/', [\App\Http\Controllers\Requirements\RequirementsController::class, 'index'])->name('index')->middleware('auth:admin');
                Route::post('/folder/create', [\App\Http\Controllers\Requirements\RequirementsController::class, 'createFolder'])->name('folder.create')->middleware('auth:admin');
                Route::post('/folder/{folderId}/rename', [\App\Http\Controllers\Requirements\RequirementsController::class, 'renameFolder'])->name('folder.rename')->middleware('auth:admin');
                Route::delete('/folder/{folderId}', [\App\Http\Controllers\Requirements\RequirementsController::class, 'deleteFolder'])->name('folder.delete')->middleware('auth:admin');
                Route::get('/folder/{folderId?}', [\App\Http\Controllers\Requirements\RequirementsController::class, 'listFolderContents'])->name('folder.contents')->middleware('auth:admin');
                Route::post('/folder/{folderId}/upload', [\App\Http\Controllers\Requirements\RequirementsController::class, 'uploadFile'])->name('folder.upload')->middleware('auth:admin');
                Route::get('/file/{fileId}/download', [\App\Http\Controllers\Requirements\RequirementsController::class, 'downloadFile'])->name('file.download')->middleware('auth:admin');
                Route::delete('/file/{fileId}', [\App\Http\Controllers\Requirements\RequirementsController::class, 'deleteFile'])->name('file.delete')->middleware('auth:admin');
            } catch (\Exception $e) {
                \Log::error('Requirements routes error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    });
});

// Profile Routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
