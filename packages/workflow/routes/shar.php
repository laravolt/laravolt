<?php

use Illuminate\Support\Facades\Route;
use Laravolt\Workflow\Http\Controllers\SharWorkflowController;
use Laravolt\Workflow\Http\Controllers\SharInstanceController;

/*
|--------------------------------------------------------------------------
| SHAR Workflow Routes
|--------------------------------------------------------------------------
|
| Here are the routes for managing SHAR BPMN workflows and instances.
|
*/

Route::prefix('api/shar')->middleware(['api'])->group(function () {
    
    // Workflow management routes
    Route::prefix('workflows')->group(function () {
        Route::get('/', [SharWorkflowController::class, 'index'])->name('shar.workflows.index');
        Route::post('/', [SharWorkflowController::class, 'store'])->name('shar.workflows.store');
        Route::get('/{name}', [SharWorkflowController::class, 'show'])->name('shar.workflows.show');
        Route::delete('/{name}', [SharWorkflowController::class, 'destroy'])->name('shar.workflows.destroy');
        
        // Launch workflow instance
        Route::post('/{name}/launch', [SharWorkflowController::class, 'launch'])->name('shar.workflows.launch');
        
        // Workflow statistics
        Route::get('/{name}/statistics', [SharWorkflowController::class, 'statistics'])->name('shar.workflows.statistics');
    });

    // Workflow instance management routes
    Route::prefix('instances')->group(function () {
        Route::get('/', [SharInstanceController::class, 'index'])->name('shar.instances.index');
        Route::get('/{instanceId}', [SharInstanceController::class, 'show'])->name('shar.instances.show');
        Route::post('/{instanceId}/complete', [SharInstanceController::class, 'complete'])->name('shar.instances.complete');
        Route::post('/{instanceId}/sync', [SharInstanceController::class, 'sync'])->name('shar.instances.sync');
        Route::patch('/{instanceId}/variables', [SharInstanceController::class, 'updateVariables'])->name('shar.instances.variables');
    });

    // Global statistics
    Route::get('statistics', [SharWorkflowController::class, 'statistics'])->name('shar.statistics');
});

// Web routes for SHAR workflow management UI
Route::prefix('workflow/shar')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return view('workflow::shar.index');
    })->name('workflow.shar.index');
    
    Route::get('/workflows', function () {
        return view('workflow::shar.workflows.index');
    })->name('workflow.shar.workflows.index');
    
    Route::get('/workflows/create', function () {
        return view('workflow::shar.workflows.create');
    })->name('workflow.shar.workflows.create');
    
    Route::get('/workflows/{name}', function ($name) {
        return view('workflow::shar.workflows.show', compact('name'));
    })->name('workflow.shar.workflows.show');
    
    Route::get('/instances', function () {
        return view('workflow::shar.instances.index');
    })->name('workflow.shar.instances.index');
    
    Route::get('/instances/{instanceId}', function ($instanceId) {
        return view('workflow::shar.instances.show', compact('instanceId'));
    })->name('workflow.shar.instances.show');
});