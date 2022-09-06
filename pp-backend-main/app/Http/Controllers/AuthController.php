<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;
use App\Classes\UserControlClass;

use Validator;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
      $this->middleware('auth:api', ['except' => ['login', 'register']]);
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request){
    $validator = Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => 'required|string|min:6',
      ]);

      if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
      }
      if (! $token = auth()->attempt($validator->validated())) {
          return response()->json(['error' => 'Unauthorized'], 401);
      }

      return $this->createNewToken($token);
  }

  /**
   * Register a User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request) {
      $data = $request->all(); 
      $user_role = isset($data['user_role']) ? $data['user_role'] : "freelancer";
      
      if($user_role == 'freelancer')
        $password = $request->password;
      else
        $password = '123456789';

      if($user_role == "freelancer")        
      {
        $validator = Validator::make($request->all(), [
          'first_name' => 'required|string|between:2,100',
          'last_name' => 'required|string|between:2,100',
          'email' => 'required|string|email|max:100|unique:users',
          'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
          return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
          $validator->validated(),
          ['user_id' => (string) Str::uuid(), 'password' => bcrypt($password), "user_role" => $user_role]
        ));

        $freelancer = new Freelancer();
        $freelancer->fre_id = (string) Str::uuid();
        $freelancer->user_id = $user['user_id'];
        $freelancer->fre_full_name = trim($data['first_name']).' '.trim($data['last_name']);
        $freelancer->fre_first_name = trim($data['first_name']);
        $freelancer->fre_last_name = trim($data['last_name']);
        $freelancer->fre_timezone = $data['timezone'];
        $freelancer->fre_desc = "";
        $freelancer->save();
      }
      else{
        $exist = User::where('email', $request->email)->where('user_role', 'client')->first();
        
        $validator = Validator::make($request->all(), [
          'email' => 'required|string|email|max:100|unique:users',
        ]);

        if(!$validator->fails()){
          $user = User::create(array_merge(
            $validator->validated(),
            ['user_id' => (string) Str::uuid(), 'password' => bcrypt($password), "user_role" => $user_role]
          ));

          $client = new Client();
          $client->clt_id = (string) Str::uuid();
          $client->user_id = $user['user_id'];
          $client->save();
        }
        else{
        }
      }

      $token = auth()->attempt(array('email' => $request->email, 'password' => $password));

      return response()->json([
          'message' => 'User successfully registered',
          'access_token' => $token,
          'user' => UserControlClass::getUserData(auth()->user()->user_id)
      ], 201);
  }


  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout() {
      auth()->logout();

      return response()->json(['message' => 'User successfully signed out']);
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */

  

  public function changePassword(Request $request) {
    $user = auth()->user();

    if(!\Hash::check($request->oldPassword, $user->password))
      return response()->json(["message" => "Old password not matched."], 422);  

    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json(['message' => 'Password changed successfully']);
  }

  protected function createNewToken($token){
      return response()->json([
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' => auth()->factory()->getTTL() * 60,
          'user' => UserControlClass::getUserData(auth()->user()->user_id)
      ]);
  }
}
