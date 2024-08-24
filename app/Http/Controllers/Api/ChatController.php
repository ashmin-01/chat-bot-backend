<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Prompt;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\PromptController;
use Illuminate\Support\Facades\Http;


class ChatController
{
    /**
     * Display a listing of the resource.
     */
    public function index() // get
    {
        $user = Auth::user();
        $chats = $user->chats()->latest()->get();
        return response()->json($chats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // post
    {
        $user_id = Auth::user()->id;
        $Validator = Validator::make($request->all(), [
            'chat_title' => 'required|string',
            'isPinned' => 'boolean',
        ]);

        if($Validator->fails()){
            return response('Something Went Wrong!',400);
        }


        $chat=Chat::create([
            'user_id'=>$user_id,
            'chat_title'=>$request['chat_title'],
            'isPinned'=>$request['isPinned'],
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Chat Created Successfully',
            'data' => $chat
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user_id = Auth::user()->id;
        if($id != $user_id){
            return response("Unauthorized");
        }
        else{

            $chat=DB::table('chats')
            ->join('prompts','prompts.chat_id','=','chats.id')
            ->join('responses','responses.prompt_id','=','prompts.id')
            ->select("prompts.prompt_content" ,"responses.response_content")
            ->where('chats.id',$id)
            ->first();
            return response()->json($chat);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , $id)
    {

        $Validator = Validator::make($request->all(), [
            'chat_title' => 'required|string',
        ]);

        if($Validator->fails()){
            return response('Something Went Wrong!',400);
        }

        $chat = Chat::find($id);

        $chat->update($request->all());
        $chat->save();

        return response()->json("Title Updated");
    }

    public function pinning($id)
    {
        $chat = Chat::find($id);

        $chat->isPinned = !$chat->isPinned;

        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Chat Pinning Status Updated',
            'data' => $chat,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $chat = Chat::find($id);
        $chat->delete();
        return response()->json(['message' => 'Chat Deleted Successfully']);
    }

    public function search(Request $request)
    {
        $query = Chat::query();

        if ($search = $request->query('q')) {
            $query->where('chat_title', 'like', "%{$search}%")
                  ->orWhereHas('prompts', function ($q) use ($search) {
                      $q->where('prompt_content', 'like', "%{$search}%");
                  })
                  ->orWhereHas('responses', function ($q) use ($search) {
                      $q->where('response_content', 'like', "%{$search}%");
                  });
        }

        $chats = $query->with(['prompts' => function ($q) use ($search) {
                        $q->where('prompt_content', 'like', "%{$search}%");
                    }, 'responses' => function ($q) use ($search) {
                        $q->where('response_content', 'like', "%{$search}%");
                    }])
                    ->get(['id', 'chat_title']);

        $result = $chats->map(function($chat) use ($search) {
            $filteredPrompts = $chat->prompts->filter(function($prompt) use ($search) {
                return stripos($prompt->prompt_content, $search) !== false;
            });

            $filteredResponses = $chat->responses->filter(function($response) use ($search) {
                return stripos($response->response_content, $search) !== false;
            });

            return [
                'id' => $chat->id,
                'chat_title' => $chat->chat_title,
                'prompts' => $filteredPrompts->values(),
                'responses' => $filteredResponses->values()
            ];
        });

        return response()->json($result);
    }

    public function uploadVoiceMessage(Request $request)
    {
        // Log the start of the upload process
        Log::info('UploadVoiceMessage method initiated', $request->all());
    
        // Validate the incoming request
        $request->validate([
            'file' => 'required|mimes:mp3,wav,aac|max:10240',
            'chat_id' => 'required|exists:chats,id',
        ]);
    
        // Store the uploaded file temporarily
        $file = $request->file('file');
        $filePath = $file->store('temp_voice_messages');
        Log::info('File stored successfully', ['filePath' => $filePath]);
    
        $chatbotUrl = env('CHATBOT_API_URL');
        Log::info('Chatbot URL', ['url' => $chatbotUrl]);
    
        try {
            Log::info('Sending file to FastAPI for transcription', ['filePath' => $filePath]);
    
            $response = Http::attach(
                'file',
                file_get_contents(storage_path('app/' . $filePath)),
                $file->getClientOriginalName()
            )->post($chatbotUrl . '/chat/audio-to-text');
    
            if ($response->successful()) {
                $transcription = $response->json()['text'];
                Log::info('Transcription received successfully', ['transcription' => $transcription]);
    
                // Create a new request object to mimic a store request
                $storeRequest = new Request([
                    'chat_id' => $request->chat_id,
                    'prompt_content' => $transcription,
                ]);
    
                // Create an instance of PromptController
                $promptController = new PromptController();
    
                // Call the store method
                $storeResponse = $promptController->store($storeRequest);
    
                Log::info('Store method called from UploadVoiceMessage', ['storeResponse' => $storeResponse]);
    
                return response()->json([
                    'file_path' => $filePath,
                    'transcription' => $transcription,
                    'store_response' => $storeResponse->getData(), // Get data from store response
                ]);
            } else {
                Log::error('Failed to transcribe audio', ['response' => $response->body()]);
    
                return response()->json([
                    'error' => 'Failed to transcribe audio.',
                    'details' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while processing the voice message', ['exception' => $e->getMessage()]);
    
            return response()->json([
                'error' => 'An error occurred while processing the voice message.',
                'details' => $e->getMessage()
            ], 500);
        }
    }



}
