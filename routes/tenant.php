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
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])
            ->name('tenant.login');
        Route::post('/login', [LoginController::class, 'login'])
            ->name('tenant.login.post');
    });

Route::middleware(['web', 'tenant', 'auth:admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
            ->name('tenant.admin.dashboard');
        Route::prefix('admin')->group(function () {
            // Add this temporary route for debugging
            Route::get('/debug/student/{student}', function($student) {
                try {
                    $studentModel = \App\Models\Student\Student::where('id', $student)
                        ->where('tenant_id', tenant('id'))
                        ->first();
                    
                    return response()->json([
                        'exists' => !is_null($studentModel),
                        'student_id' => $student,
                        'tenant_id' => tenant('id'),
                        'data' => $studentModel
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'student_id' => $student,
                        'tenant_id' => tenant('id')
                    ], 500);
                }
            })->name('debug.student');

            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
                ->name('tenant.admin.dashboard');
            Route::get('/staff/register', [StaffRegistrationController::class, 'showRegistrationForm'])
                ->name('staff.register');
            Route::post('/staff/register', [StaffRegistrationController::class, 'register'])
                ->name('staff.register.save');
            Route::post('/instructor', [StaffRegistrationController::class, 'register'])
                ->name('tenant.instructor.store');
            Route::get('/students', [StudentController::class, 'index'])
                ->name('tenant.students.index');
            Route::post('/students', [StudentController::class, 'store'])
                ->name('tenant.students.store');
            Route::put('/students/{student}', [StudentController::class, 'update'])
                ->name('tenant.students.update');
            Route::delete('/students/{student}', [StudentController::class, 'destroy'])
                ->name('tenant.students.destroy')
                ->where('student', '[0-9]+');
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
    Route::prefix('admin')->name('tenant.admin.')->group(function () {
        // Requirements management routes
        Route::prefix('requirements')->name('requirements.')->group(function () {
            Route::get('/', 'RequirementsController@index')->name('index');
            Route::post('/folder/create', 'RequirementsController@createFolder')->name('folder.create');
            Route::post('/folder/{folderId}/rename', 'RequirementsController@renameFolder')->name('folder.rename');
            Route::delete('/folder/{folderId}', 'RequirementsController@deleteFolder')->name('folder.delete');
            Route::get('/folder/{folderId?}', 'RequirementsController@listFolderContents')->name('folder.contents');
            Route::post('/folder/{folderId}/upload', 'RequirementsController@uploadFile')->name('folder.upload');
            Route::get('/file/{fileId}/download', 'RequirementsController@downloadFile')->name('file.download');
            Route::delete('/file/{fileId}', 'RequirementsController@deleteFile')->name('file.delete');
        });
    });
});
