<?php

namespace App\Http\Controllers\Api;


use App\Events\BasicEvent;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Events\ResponseCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\FeedbackController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/broadcast-basic-event', function () {
    $basicEvent = new BasicEvent('Hello, world!');
    event($basicEvent);
    //broadcast(new ResponseCreated('Hello, world!'));
    return 'Basic event broadcasted!';
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);
    // Chat Routes
    Route::prefix('chats')->group(function () { //done
        Route::get('/All_Chats', [ChatController::class, 'index']);
        Route::post('/New_Chat', [ChatController::class, 'store']);
        Route::get('/show_chat/{id}', [ChatController::class, 'show']);
        Route::put('/change_title/{id}', [ChatController::class, 'update']);
        Route::put('/pin/{id}', [ChatController::class, 'pinning']);
        Route::delete('/delete/{chat}', [ChatController::class, 'destroy']);
        Route::get('/search', [ChatController::class, 'search']);
        // new : voice message
        Route::post('/upload-voice-message', [ChatController::class, 'uploadVoiceMessage']);
        // Route::post('/upload-voice-message', [ChatController::class, 'uploadVoiceMessage_with_transctipt']);


    });

    // Prompt Routes
    Route::prefix('prompts')->group(function () { //done
        Route::get('/{chat_id}', [PromptController::class, 'index']);
        Route::post('/Ask_question', [PromptController::class, 'store']);
        Route::get('/{prompt}', [PromptController::class, 'show']);
        Route::put('/{prompt_id}', [PromptController::class, 'upxdate']);
    });

    // Response Routes
    Route::prefix('responses')->group(function () {
        Route::get('/', [ResponseController::class, 'index']);
        Route::post('/', [ResponseController::class, 'store']);
        Route::get('/{response}', [ResponseController::class, 'show']);
        Route::put('/{response}', [ResponseController::class, 'update']);
        Route::put('/{response}', [ResponseController::class, 'toggleLikeDislike']);
    });

    // Feedback Routes
    Route::prefix('feedbacks')->group(function () {
        Route::get('/', [FeedbackController::class, 'index']);
        Route::post('/', [FeedbackController::class, 'store']);
    });

});
