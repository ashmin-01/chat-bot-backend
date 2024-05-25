<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Prompt;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $chats = $query->with(['user', 'prompts', 'responses'])->get();

        return response()->json($chats);
    }
}
