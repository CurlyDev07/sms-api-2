<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\StatisticController;



Route::options('{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');



Route::post('/create-customer-info', [SmsController::class, 'create_customer_info']);

Route::post('/create-sms-message', [SmsController::class, 'create_sms_message']);
Route::get('/get-sms-message', [SmsController::class, 'get_sms_message']);
Route::get('/get-single-sms-message/{id}', [SmsController::class, 'get_single_sms_message']);
Route::post('/update-sms-message/{id}', [SmsController::class, 'update_sms_message']);
Route::delete('/delete-sms-message/{id}', [SmsController::class, 'delete_sms_message']);

Route::get('/get-customer-follow-up', [SmsController::class, 'get_customer_follow_up']);


// |--------------------------------------------------------------------------
// | STATISTICS
// |--------------------------------------------------------------------------

Route::get('/followups/statistics', [StatisticController::class, 'followUpStats']);




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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


