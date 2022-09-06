<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Session;

use App\Events\ChatNewEvent;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * [POST]
     * auth_validate
     */
    public function auth_validate(Request $request){
        if(Auth::user()){
            return json_encode(array('success' => true, 'message' => 'success!', 'userinfo' => Auth::user() ));
        }else{
            return json_encode(array('success' => false, 'message' => 'Unauthenticated.'));
        }
    }

    public function app()
    {
        return view('app');
    }
}
