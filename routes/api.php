<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FdDeploymentController;
use App\Http\Controllers\FdCallbackController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('fast-deploy/uninstall', [FdDeploymentController::class, 'customUninstaller']);
Route::get('fast-deploy/install/{uuid}/{architecture}/{label}/{vultr?}', [FdDeploymentController::class, 'customInstaller']);
Route::get('fast-deploy/{architecture}/tolitesync', [FdDeploymentController::class, 'switchToLiteSync']);
Route::post('fast-deploy/callbacks/created', [FdCallbackController::class, 'created']);
Route::post('fast-deploy/callbacks/finish-install', [FdCallbackController::class, 'finished_installing']);
Route::post('fast-deploy/callbacks/downloading-snapshot', [FdCallbackController::class, 'downloading_snapshot']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('nodes/wallet/', [ApiController::class, 'getWallet']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('nodes', [ApiController::class, 'nodesGet']);
    Route::post('nodes', [ApiController::class, 'nodesCreate']);
    Route::get('nodes/{id}', [ApiController::class, 'nodeGet']);
    Route::post('nodes/delete', [ApiController::class, 'nodesDestroy']);
});
