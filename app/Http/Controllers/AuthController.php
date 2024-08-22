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
    public function register(Request $request){
        $data=$request->all();
        $validator= Validator::make($data,[
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'mobile_number'=>'string|required'
        ]);
        if($validator->fails()){
            return response()->json('Something went wrong! try again',400);
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
        return response()->json(
            ['data' => $user_token,
            'message' => 'User Registered Successfully!'],
            201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'mobile_number' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('mobile_number', $fields['mobile_number'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Wrong password'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'User Logged in Successfully!'
        ];

        return response()->json($response, 201);
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged Out'
        ],200);
    }






    //================= Dashboard ========================



    public function showLoginForm()
    {
        // Message displayed on the login form, can be adjusted as needed
        $msg = "Welcome!";

        // Create a form object with fields
        $form = new \stdClass();

        // Update the field name to 'mobile_number' instead of 'username'
        $form->mobile_number = '<input type="text" name="mobile_number" class="form-control" required>';
        $form->password = '<input type="password" name="password" class="form-control" required>';

        // Return the view with the form and message variables
        return view('accounts.login', compact('msg', 'form'));
    }


    public function loginAdmin(Request $request)
    {
        $fields = $request->validate([
            'mobile_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('mobile_number', $fields['mobile_number'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return redirect()->back()->withErrors(['message' => 'Wrong mobile number or password']);
        }

        if ($user->id != 1) {
            return redirect()->back()->withErrors(['message' => 'You are not allowed to access this dashboard']);
        }

        // Log in the user using session-based authentication
        Auth::login($user);

        return redirect()->route('dashboard');
    }



    public function showRegisterForm()
    {
        $msg = null;  // Default value for the message
        $success = false;  // Default value for the success variable

        // Initialize the form fields with their HTML and error messages
        $form = new \stdClass();
        $form->username = (object)[
            'input' => '<input type="text" name="username" class="form-control" value="' . old('username') . '" required>',
            'errors' => session('errors') ? session('errors')->get('username') : null
        ];
        $form->email = (object)[
            'input' => '<input type="email" name="email" class="form-control" value="' . old('email') . '" required>',
            'errors' => session('errors') ? session('errors')->get('email') : null
        ];
        $form->password1 = (object)[
            'input' => '<input type="password" name="password" class="form-control" required>',
            'errors' => session('errors') ? session('errors')->get('password') : null
        ];
        $form->password2 = (object)[
            'input' => '<input type="password" name="password_confirmation" class="form-control" required>',
            'errors' => session('errors') ? session('errors')->get('password_confirmation') : null
        ];

        return view('accounts.register', compact('msg', 'form', 'success'));
    }

public function handleRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $msg = "Registration successful!";
        $success = true;

        return view('accounts.register', compact('msg', 'success'));
    }


    public function logout_admin(Request $request)
    {
        // Invalidate the current session and log out the user
        Auth::logout();


        // Redirect the user to the home page or login page
        return redirect()->route('login');
    }



}
