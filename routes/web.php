<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ResponseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/login', [AuthController::class, 'loginAdmin'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');

Route::post('/register', [AuthController::class, 'handleRegister'])->name('register.submit');


    //Route::get('/tables', [ResponseController::class, 'BadResponses']);
    Route::get('/tables', [ResponseController::class, 'viewTable'])->name('view.table');

        // Route for Bad Responses
    Route::get('/bad-responses', [ResponseController::class, 'BadResponses'])->name('bad.responses');

    // Route for Regenerated Responses
    Route::get('/regenerated-responses', [ResponseController::class, 'RegeneratedResponses'])->name('regenerated.responses');

    Route::post('/upload-document', [DashboardController::class, 'upload_document'])->name('document.upload');




    Route::post('/search-documents', [DashboardController::class, 'searchDocuments'])->name('search.documents');

    Route::post('/dashboard/configure', [DashboardController::class, 'configure'])->name('dashboard.configure');

    Route::post('/dashboard/update-template', [DashboardController::class, 'update_template'])->name('dashboard.update_template');

    Route::post('/delete-file', [DashboardController::class, 'deleteFile']);



    Route::get('/dashboard', function () {
        return view('home.index'); // or 'dashboard.dashboard' if placed in a subfolder
    })->middleware('auth')->name('dashboard');

    Route::get('/icons', function () {
        return view('home.icons');
    });

    /*
    Route::get('/tables', function () {
        return view('home.tables');
    });
    */

    Route::get('/map', function () {
        return view('home.map');
    })->middleware('auth')->name('dashboard');

    Route::get('/typography', [DashboardController::class, 'showFiles'])->name('dashboard.files')->middleware('auth')->name('dashboard');


    Route::get('/document', function () {
        return view('home.document');
    })->middleware('auth')->name('dashboard');


    Route::get('/user', function () {
        return view('home.user');
    })->middleware('auth')->name('dashboard');


    Route::post('/logout', [AuthController::class, 'logout_admin'])->name('logout');


