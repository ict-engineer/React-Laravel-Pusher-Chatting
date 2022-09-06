<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;
use App\Classes\UserControlClass;

use Validator;

class UserController extends Controller
{
    //

  public function getUserData()
  {
    return response()->json(['data' => UserControlClass::getUserData(auth()->user()->user_id)]);
  }
  
  public function getProfileDataById(Request $request)
  {
    return response()->json(['data' => UserControlClass::getUserData($request->id)]);
  }
  
  public function updateUserData(Request $request)
  {
    try {
      $user = auth()->user();
      $data = $request->all(); 
      
      if(isset($data['payment_email']))
      {
        $user->payment_email = $data['payment_email'];
        if(auth()->user()->user_role == "client")
        {
          $client = Client::where('user_id', $user->user_id)->first();
          $client->clt_invoice_email = $data['payment_email'];
          $client->save();
        }
      }
      
      if($user->user_role == 'freelancer')
      {
        if(isset($data['full_name']))
        {
          $freelancer = Freelancer::where('user_id', $user->user_id)->first();
          if($freelancer)
          {
            $freelancer->fre_full_name = $data['full_name'];
            $freelancer->fre_first_name = $data['first_name'];
            $freelancer->fre_last_name = $data['last_name'];

            if(isset($data['avatar']))
            {
              $image = $request->get('avatar');
            
              $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
              File::delete($freelancer->fre_avatar);
              \Image::make($request->get('avatar'))->save(public_path('avatars/').$name);
              $freelancer->fre_avatar = 'avatars/'.$name;
            }
            
            $freelancer->fre_english_level = $data['english_level'];
            $freelancer->fre_timezone = $data['timezone'];
            $freelancer->fre_desc = $data['description'];
            $freelancer->save();
          }
          else{
            return response()->json(["message" => "Can't find user"], 422);  
          }
        }
      }
      else if($user->user_role == 'client')
      {
        $client = Client::where('user_id', $user->user_id)->first();
        if($client)
        {
          if(isset($data['avatar']))
          {
            $image = $request->get('avatar');
          
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            File::delete($client->clt_avatar);
            \Image::make($request->get('avatar'))->save(public_path('avatars/').$name);
            $client->clt_avatar = 'avatars/'.$name;
          }
          
          $client->save();
        }
        else{
          return response()->json(["message" => "Can't find user"], 422);  
        }
      }

      $user->save();
    } catch(Exception $e){
      return response()->json($e, 422);
    }

    return response()->json(['message' => 'User successfully updated', 'data' => UserControlClass::getUserData(auth()->user()->user_id)]);
  }

  public function getConfirmCode(Request $request)
  {
    try{
      // Mail::to($request->email)->send(['Confirm Code'=>'12345']);
      return response()->json(['message' => 'Successfully sent', 'data' => '12345']);
    } catch(Exception $e){
      return response()->json($e, 422);
    }
  }
}
