<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\HashTagController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TransactionController;
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

Route::group([
  'middleware' => 'api',
  'prefix' => 'auth'

], function ($router) {
  Route::post('/login', [AuthController::class, 'login']);
  Route::post('/signup', [AuthController::class, 'register']);
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::post('/change_password', [AuthController::class, 'changePassword']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => ''
], function ($router) {
  Route::get('/get_top_freelancers', [FreelancerController::class, 'getTopFreelancers']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'user'

], function ($router) {
  Route::post('/get_confirmcode', [UserController::class, 'getConfirmCode']);
  Route::get('/get_userdata', [UserController::class, 'getUserData']);
  Route::post('/get_profiledata', [UserController::class, 'getProfileDataById']);
  Route::post('/update_userdata', [UserController::class, 'updateUserData']);
  
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'portfolios'
], function ($router) {
  Route::post('', [PortfolioController::class, 'store']);
  Route::put('', [PortfolioController::class, 'update']);
  Route::delete('/{id}', [PortfolioController::class, 'delete']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'hashtags'
], function ($router) {
  Route::get('', [HashTagController::class, 'index']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'tickets'
], function ($router) {
  Route::get('', [TicketController::class, 'index']);
  Route::get('/{id}', [TicketController::class, 'getTicketDetails']);
  Route::post('', [TicketController::class, 'store']);
  Route::put('', [TicketController::class, 'update']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'jobs'
], function ($router) {
  Route::get('', [JobController::class, 'index']);
  Route::post('', [JobController::class, 'store']);
  Route::put('', [JobController::class, 'update']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'meetings'
], function ($router) {
  Route::get('', [MeetingController::class, 'index']);
  Route::get('/{id}', [MeetingController::class, 'getMeetingInfoById']);
  Route::post('', [MeetingController::class, 'saveMessage']);
  Route::put('', [MeetingController::class, 'update']);
  Route::post('/set_unread_as_read_byid', [MeetingController::class, 'setUnreadAsReadById']);
});

Route::group([
  'middleware' => 'api',
  'prefix' => 'transactions'
], function ($router) {
  Route::get('', [TransactionController::class, 'index']);
  Route::put('', [TransactionController::class, 'update']);
  Route::post('', [TransactionController::class, 'saveMessage']);
  Route::post('/set_unread_as_read_byid', [TransactionController::class, 'setUnreadAsReadById']);
  Route::post('/make_contract', [TransactionController::class, 'makeContract']);
  Route::post('/change_contract', [TransactionController::class, 'changeContract']);
});
