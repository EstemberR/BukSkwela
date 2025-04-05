<?php

use Illuminate\Support\Facades\Route;

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