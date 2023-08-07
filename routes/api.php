<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SeleniumController;
use App\Http\Controllers\PdfCsvConversionController;

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

// 3. Baixar o arquivo através do link https://testpages.herokuapp.com/styled/download/download.html pelo botão “Direct Link Download” e salvar no seu disco local e renomear o arquivo para “Teste TKS”
Route::get('download', [SeleniumController::class, 'download']);

//4. Realizar o upload do arquivo baixado no item 3 através do link https://testpages.herokuapp.com/styled/file-upload-test.html.
Route::post('upload', [SeleniumController::class, 'uploadFile']);

// LEITURA PDF.
Route::get('convert-pdf-to-xls', [PdfCsvConversionController::class, 'convertPdfToXLS']);
// Route::get('convert-pdf-to-csv', [PdfCsvConversionController::class, 'convertPdfToCsv']);
