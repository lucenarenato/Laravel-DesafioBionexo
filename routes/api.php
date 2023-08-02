<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SeleniumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('info', function () {
    $appInfo = [
        'server' => getenv('APP_NAME'),
        'version' => getenv('APP_VERSION')
    ];
    return response()->json($appInfo);
});

Route::group(['middleware' => 'api'], function ($router) {
    /**
     * Authentication Module
     */
    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

});
// 1. Acessar a página https://testpages.herokuapp.com/styled/tag/table.html e capturar todas as informações exibidas na tabela e armazenar em um banco de dados mysql
Route::get('access-page', [SeleniumController::class, 'accessPageSave']);
// 2. Preencher o formulário através do link https://testpages.herokuapp.com/styled/basic-html-form-test.html e retornar se preenchimento foi ok ou não. ( pode inventar as informações a serem preenchidas)
Route::post('read-form', [SeleniumController::class, 'sendDataToForm']);
