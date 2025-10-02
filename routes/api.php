<?php

use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->prefix('organizations')->group(function () {
    Route::get('search', [OrganizationController::class, 'searchByName']);
    Route::get('building/{building}', [OrganizationController::class, 'indexByBuilding']);
    Route::get('activity/{activity}', [OrganizationController::class, 'indexByActivity']);
    Route::get('geo/rectangle', [OrganizationController::class, 'indexByRectangle']);
    Route::get('geo/radius', [OrganizationController::class, 'indexByRadius']);
    Route::get('{id}', [OrganizationController::class, 'searchById']);
    Route::get('activity-with-children/{activity}', [OrganizationController::class, 'searchByActivity']);
});
