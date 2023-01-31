<?php

use App\Models\Node;
use App\Models\User;

use Inertia\Inertia;
use App\Models\Wallet;
use Aws\Ec2\Ec2Client;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\NodeController;
use LKDev\HetznerCloud\HetznerAPIClient;
use App\Http\Controllers\SshKeyController;
use App\Http\Controllers\VpsKeyController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\NetworkController;



use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FastDeployController;
use App\Http\Controllers\FdDeploymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FdConfigurationController;
use LKDev\HetznerCloud\Models\Images\ImageRequestOpts;
use App\Http\Controllers\NotificationSettingsController;
use App\Notifications\IdGenerated;

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

Route::get('/test', function () {

});


Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::post('btcpay/webhook', [WebhookController::class, 'handleWebhook'])->name('webhook');

Route::group(
    [
        'middleware' => ['auth:sanctum', 'verified']
    ],
    function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('network', [NetworkController::class, 'index'])->name('network.index');

        Route::resource('wallets', WalletController::class)->except([
            'create', 'edit', 'update'
        ]);

        Route::delete('wallets', [WalletController::class, 'destroyAll'])->name('wallets.destroyAll');

        Route::resource('nodes', NodeController::class)->except([
            'create', 'edit'
        ]);

        Route::resource('notifications', NotificationController::class)->only([
            'index', 'update', 'destroy'
        ]);

        Route::delete('nodes', [NodeController::class, 'destroyAll'])->name('nodes.destroyAll');

        Route::get('fast-deploy', [FastDeployController::class, 'index'])->name('fastdeploy.index');

        Route::get('ssh-keys', [SshKeyController::class, 'index'])->name('sshKeys.index');
        Route::post('ssh-keys', [SshKeyController::class, 'store'])->name('sshKeys.store');
        Route::delete('ssh-keys/{sshKey}', [SshKeyController::class, 'destroy'])->name('sshKeys.destroy');

        Route::get('vps-keys', [VpsKeyController::class, 'index'])->name('vpsKeys.index');
        Route::post('vps-keys', [VpsKeyController::class, 'store'])->name('vpsKeys.store');

        Route::delete('vps-keys/{vpsKey}', [VpsKeyController::class, 'destroy'])->name('vpsKeys.destroy');

        Route::get('fast-deploy/helpers/digitalocean/sizes', [VpsKeyController::class, 'getDoSizes'])->name('do-sizes');
        Route::get('fast-deploy/helpers/vultr/sizes', [VpsKeyController::class, 'getVultrSizes'])->name('vultr-sizes');
        Route::get('fast-deploy/helpers/aws/sizes', [VpsKeyController::class, 'getAWSSizes'])->name('aws-sizes');
        Route::get('fast-deploy/helpers/hetzner/sizes', [VpsKeyController::class, 'getHetznerSizes'])->name('hetzner-sizes');

        Route::get('user-profile-notifications', [NotificationSettingsController::class, 'index'])->name('user-profile-notifications.index');
        Route::post('user-profile-notifications', [NotificationSettingsController::class, 'update'])->name('user-profile-notifications.update');

        Route::post('fd-configurations', [FdConfigurationController::class, 'store'])->name('fdConfigurations.store');
        Route::delete('fd-configurations/{configuration}', [FdConfigurationController::class, 'destroy'])->name('fdConfigurations.destroy');

        Route::post('fast-deploy/configurations/{id}/deployment', [FdDeploymentController::class, 'store'])->name('fdDeployments.deploy');

        Route::post('purchase', [PaymentController::class, 'ProcessPayment'])->name('purchase');
    }
);
