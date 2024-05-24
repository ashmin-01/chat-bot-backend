<?php

  namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function Register(Request $request){
        $data=$request->all();
        $validator= Validator::make($data,[
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'mobile_number'=>'string|required'
        ]);
        if($validator->fails()){
            return response()->json('Something went wrong!try again',400);
        }
       $user = Auth::user();

        $user = User::create([
            'name' => $data['name'],
            'password' => bcrypt($data['password']),
            'mobile_number'=>$data['mobile_number']
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $user_token = [
            'user' => $user,
            'token' => $token
        ];
        return response()->json($user_token,201);
    }

    public function login(Request $request){
        $fields = $request->validate([
            'mobile_number' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = user::where('mobile_number' , $fields['mobile_number'])->first();

        //check password
        if(!$user || !Hash::check($fields['password'] , $user->password)){
            return response([
                'message' => 'Wrong password'
            ] , 401);

        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response , 201);
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged Out'
        ],200);
    }
}




