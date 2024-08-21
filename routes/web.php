<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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


Route::get('/tables', [ResponseController::class, 'BadResponses']);



Route::get('/dashboard', function () {
    return view('home.index'); // or 'dashboard.dashboard' if placed in a subfolder
});

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
});

Route::get('/typography', function () {
    return view('home.typography');
});


Route::get('/user', function () {
    return view('home.user');
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
