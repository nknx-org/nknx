<?php
use Illuminate\Support\Facades\Route;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\EasyTransferConnectionController;

use App\Http\Controllers\FdCallbackController;


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

Route::post('token', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(),400);
    }

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'incorrect credentials'],422);
    }

    return response()->json($user->createToken($request->device_name)->plainTextToken);

});



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::delete('token', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json('Success');
    });


    Route::get('easy-transfer', [EasyTransferConnectionController::class, 'index']);
    Route::post('easy-transfer', [EasyTransferConnectionController::class, 'store']);

});

