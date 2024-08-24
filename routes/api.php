<?php

namespace App\Http\Controllers\Api;


use App\Events\BasicEvent;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Events\ResponseCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\ResponseController;


$chatbotUrl = env('CHATBOT_API_URL');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/streaming', [PromptController::class, 'streamResponse']);

Route::post('/broadcasting/auth', function (Request $request) {
    $authKey = config('broadcasting.connections.pusher.key');
    $secret = config('broadcasting.connections.pusher.secret');
    $socketId = $request->input('socket_id');
    $chatId = $request->input('chat_id');
    $channelName = 'private-Chat.' . $chatId;

    $stringToSign = $socketId . ':' . $channelName;
    $authSignature = hash_hmac('sha256', $stringToSign, $secret);
    $auth = $authKey . ':' . $authSignature;

    return response()->json(['auth' => $auth]);
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
        Route::post('/upload-voice-message', [ChatController::class, 'uploadVoiceMessage']);


    });



    // Prompt Routes
    Route::prefix('prompts')->group(function () { //done
        Route::get('/{chat_id}', [PromptController::class, 'index']);
        Route::post('/Ask_question', [PromptController::class, 'store']);
        Route::get('/{prompt}', [PromptController::class, 'show']);
        Route::put('/{prompt_id}', [PromptController::class, 'update']);
    });

    // Response Routes
    Route::prefix('responses')->group(function () {
        Route::get('/', [ResponseController::class, 'index']);
        Route::post('/', [ResponseController::class, 'store']);
        Route::get('/{response}', [ResponseController::class, 'show']);
        Route::put('/regenerate/{response_id}', [ResponseController::class, 'regenrate']);
        Route::put('/{response}', [ResponseController::class, 'toggleLikeDislike']);
    });

    // Feedback Routes
    Route::prefix('feedbacks')->group(function () {
        Route::get('/', [FeedbackController::class, 'index']);
        Route::post('/', [FeedbackController::class, 'store']);
        Route::post('/regenrated', [FeedbackController::class, 'review']);
    });

});
