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


    // Endpoint for sending a voice message : TEST
    public function uploadVoiceMessage(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:mp3,wav,aac|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('voice_messages');

        $output = [];
        $transcription = null;
        $command = "python " . base_path('whisper_transcribe.py') . " " . storage_path('app/' . $filePath);

        Log::info('Executing command: ' . $command);
        exec($command, $output, $returnVar);
        Log::info('Command output: ' . implode("\n", $output));
        Log::info('Command return status: ' . $returnVar);

        if (!empty($output)) {
            $transcription = implode("\n", $output);
        }

        return response()->json(['file_path' => $filePath, 'transcription' => $transcription]);
    }

    // Endpoint for sending a voice message, stores the transcription and sends it as a prompt.
    public function uploadVoiceMessage_with_transcript(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file' => 'required|mimes:mp3,wav,aac|max:10240',
            'chat_id' => 'required|exists:chats,id', // Ensure chat_id is provided and valid
        ]);

        // Store the uploaded file
        $file = $request->file('file');
        $filePath = $file->store('voice_messages');

        // Call the Whisper Python script to transcribe the audio
        $output = [];
        $transcription = null;
        $command = "python3 " . base_path('whisper_transcribe.py') . " " . storage_path('app/' . $filePath);
        exec($command, $output);

        if (!empty($output)) {
            $transcription = implode("\n", $output);

            // Define the transcriptions directory and file name
            $transcriptionDirectory = base_path('transcriptions');
            $transcriptionFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.txt';

            // Ensure the transcriptions directory exists
            if (!file_exists($transcriptionDirectory)) {
                mkdir($transcriptionDirectory, 0755, true);
            }

            // Save the transcription to the transcriptions directory
            $transcriptionFilePath = $transcriptionDirectory . '/' . $transcriptionFileName;
            file_put_contents($transcriptionFilePath, $transcription);

            // Save the transcription as a prompt in the database
            $prompt = new Prompt();
            $prompt->chat_id = $request->chat_id;
            $prompt->prompt_content = $transcription;
            $prompt->save();
        }

        return response()->json([
            'file_path' => $filePath,
            'transcription' => $transcription,
            'transcription_file' => $transcriptionFilePath ?? null,
            'prompt_id' => $prompt->id ?? null,
        ]);
    }



}
