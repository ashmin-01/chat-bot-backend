<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;


// Chat Routes 
Route::prefix('chats')->group(function () {
    Route::get('/', [ChatController::class, 'index']);             
    Route::post('/', [ChatController::class, 'store']);            
    Route::get('/{chat}', [ChatController::class, 'show']);       
    Route::put('/{chat}', [ChatController::class, 'update']);      
    Route::delete('/{chat}', [ChatController::class, 'destroy']);  
    Route::get('/search', [ChatController::class, 'search']);
});


// Prompt Routes
Route::prefix('prompts')->group(function(){
    Route::get('/', [PromptController::class, 'index']);
    Route::post('/', [PromptController::class, 'store']);
    Route::get('/{prompt}', [PromptController::class, 'show']);
    Route::put('/{prompt}', [PromptController::class, 'update']);

});


// Response Routes 
Route::prefix('responses')->group(function(){
    Route::get('/', [PromptController::class, 'index']);
    Route::post('/', [PromptController::class, 'store']);
    Route::get('/{response}', [PromptController::class, 'show']);
    Route::put('/{response}', [PromptController::class, 'update']);

});





