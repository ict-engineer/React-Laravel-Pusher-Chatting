<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Freelancer;
use App\Models\Client;
use App\Models\Timezone;
use App\Models\User;
use App\Models\Channel;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Review;
use App\Models\TimeTrack;
use App\Models\Portfolio;
use App\Models\RateupClientFeedbacks;

class ClientController extends Controller
{

    // public $invoice_status = false;

    /**
     * Show the application dashboard for Freelancer
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('client');
    }
    
    /**
     * POST: getDetail info.
     */
    public function getDetail(){
        $userid = Auth::user()->user_id;
        
        $client = Client::where('user_id', $userid)->first();

        $channel = Channel::where('clt_id', $client->clt_id)->get();

        $channelIds = [];
        foreach ($channel as $key => $value) {
            $channelIds[] = $value->channel_id;
        }

        $contracts = Contract::whereIn('channel_id', $channelIds)
                            ->where('contract_status', 'ended')
                            ->get();

        $contractIds = [];
        foreach ($contracts as $key => $value) {
            $contractIds[] = $value->contract_id;
        }
        
        $reviews = Review::whereIn('contract_id', $contractIds)
                        ->where('author_id', '!=', $client->clt_id)
                        ->get();

        $time_total = TimeTrack::select('contract_id', DB::raw("SUM(trk_total_hrs) as total"))
                        ->whereIn('contract_id', $contractIds)
                        ->groupBy('contract_id')->get();

        foreach ($contracts as $key =>  $contract) {
            $contracts[$key]['total_hours'] = 0;
            foreach ($time_total as $time) {
                if( $contract['contract_id'] == $time['contract_id']) {
                    $contracts[$key]['total_hours'] = $time['total'];
                    break;
                }
            }
        }

        if(!$client){
            if($userid != null){
                // generate client for this user.
                $client = new Client();
                $client->clt_id = (string) Str::uuid();
                $client->user_id = $userid;
                $client->clt_invoice_email = "";
                $client->clt_full_name = "";
                $client->clt_phone = "";
                $client->clt_skype_id = "";
                $client->clt_avatar = "";
                $client->clt_payment_verified = 0;
                $client->save();
            }
        }

       

        // $payment_controller = new PaymentController();
        // $arr = array(
        //     'client_id' => $client->clt_id
        // );
        // $payment_controller->async_call(env('APP_URL') . '/paypal_reminder', $arr);

        return response()->json(array('success' => true, 'client' => $client,'reviews' => $reviews, 'contracts' => $contracts ));
    }



    /**
     * POST : updateClient
     */
    public function updateClient(Request $request){
        $data = $request->all();

        $user_id = Auth::user()->user_id;

        $clt_id = $data['clt_id'];
        $client = Client::where('clt_id', $clt_id)->first();
        if(!($clt_id != null && $clt_id != "" && $client['user_id'] == $user_id)){
            return response()->json(array('success' => false, 'error' => "Invalid Client Id!"));
        }

        // update the client info.
        $client->clt_full_name = isset($data['clt_full_name']) ? $data['clt_full_name'] : $client->clt_full_name;
        $client->clt_phone = isset($data['clt_phone']) ? $data['clt_phone'] : $client->clt_phone;
        $client->clt_skype_id = isset($data['clt_skype_id']) ? $data['clt_skype_id'] : $client->clt_skype_id;
        // update payment email.
        $client->clt_invoice_email = isset($data['clt_invoice_email']) ? $data['clt_invoice_email'] : $client->clt_invoice_email;

        $client->clt_avatar = "/images/default_avatar.png";

        $client->save();

        
        // $email = $client->clt_invoice_email;
        // $firstName = explode(' ', $client->clt_full_name)[0];
        // $lastName = explode(' ', $client->clt_full_name)[1];
        
        // $client->clt_payment_verified = true;

        // $client->save();
        
        return response()->json(array('success'=> true));

    }

    /**
     * store image on Freelancer Photo.
     */
    public function updatePhoto(Request $request){
        $userid = Auth::user()->user_id;

        $client = Client::where('user_id', $userid)->first();

        if(!isset($client->clt_id)){
            return response()->json(['success' => false, 'message' => 'upload Failed!']);
        }

        $upload_path = public_path('uploads/avatar');
        $file_name = $request->file->getClientOriginalName();
        $generated_new_name = 'clt_avatar_'.time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move($upload_path, $generated_new_name);

        // save the file avatar url.
        $avatar = '/uploads/avatar/' . $generated_new_name;
        $client->clt_avatar = $avatar;
        $client->save();

        return response()->json(['success' => true, 
                                'message' => 'You have successfully uploaded "' . $file_name . '"',
                                'clt_avatar' => $avatar
                                ]);
      
    }


    /**
     * UPdate password
     */
    public function updatePassword(Request $request){
        $userid = Auth::user()->user_id;

        $origin_pass = $request->input('origin_password');
        $new_pass = $request->input('new_password');

        // validate origin password
        $credentials['user_email'] = Auth::user()->user_email;
        $credentials['password'] = $origin_pass;

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            // set the new password
            $user = User::where('user_id', $userid)->first();
            if($user){
                $user->user_password = Hash::make($new_pass);
                $user->save();
                return response()->json(['success'=> true]);
            }else{
                return response()->json(['success' => false, 'message'=>'Invalid User!']);
            }
        }else{
            return response()->json(['success' => false, 'message'=>'Invalid Current Password!']);
        }


        
    }


    /**
     * createChannel
     */
    public function createChannel(Request $request){
        $userid = Auth::user()->user_id;
        
        $fre_id = $request->input('fre_id');
        $clt_id = $request->input('clt_id');
        $portfolio_id = $request->input('portfolio_id');

        // check if the client has the channel from : fre_id, clt_id, portfolio_id
        // $channel = Channel::where('fre_id', $fre_id)->where('clt_id', $clt_id)->where('portfolio_id', $portfolio_id)->first();
        // if(!$channel){
            // create a channel.
            $channel = new Channel();
            $channel->channel_id = (string) Str::uuid();
            $channel->fre_id = $fre_id;
            $channel->clt_id = $clt_id;
            $channel->portfolio_id = $portfolio_id;
            $channel->contract_id = null;
            $channel->channel_status = true;

            $channel->save();
        // }

        $portfolio_obj = Portfolio::where('por_id', $portfolio_id)->first();
        $portfolio_obj->por_viewed = ++$portfolio_obj->por_viewed;
        $portfolio_obj->save();


        return response()->json(['success' => true, 'message'=>'Created a Channel!', 'channel_id' => $channel->channel_id]);
    }

    public function rate_feedback(Request $request)
    {
        try {

            $id = $request->input('id');
            $rateup = $request->input('rateup');
            $hire_able = $request->input('hire_able');

            $request_obj = RateupClientFeedbacks::where('id', $id)->first();
            $request_obj->rateup = $rateup;
            $request_obj->hire_able = $hire_able;
            $request_obj->save();

            $this->feedback_check($request_obj->fre_id);

            return response()->json(array('success' => true, 'data' => ''));

        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'data' => ''));
        }
    }

    public function feedback_check($fre_id)
    {
        try {
            $feedback_obj = RateupClientFeedbacks::where('fre_id', $fre_id)->get();

            $feedback_uncheck = RateupClientFeedbacks::where('fre_id', $fre_id)
                                ->where('rateup', '<>', 3)
                                ->get();
            $feedback_reject = RateupClientFeedbacks::where('fre_id', $fre_id)
                                ->where('rateup', 2)
                                ->orWhere('rateup', 0)
                                ->get();

            if (count($feedback_uncheck) == count($feedback_obj) && count($feedback_reject) > 0) {
                $freelancer = Freelancer::where('fre_id', $fre_id)->first();
                $freelancer->fre_rate_req_status = 2;
                $freelancer->save();
            } else {
                $freelancer = Freelancer::where('fre_id', $fre_id)->first();
                $freelancer->fre_rate = $feedback_obj[0]->able_rate;
                $freelancer->fre_rate_req_status = 0;
                $freelancer->save();
            }
            
        } catch (\Exception $e) {
            dd($e);
        }
    }

    //get feedbackInfo by id
    public function get_feedback_info(Request $request) {
        try {
            $id = $request->input('id');

            $feedback_info = RateupClientFeedbacks::where('id', $id)
                                ->where('rateup', 3)
                                ->where('hire_able', 3)
                                ->first();

            if(sizeof($feedback_info)) {
                return response()->json(array('success' => true, 'data' => $feedback_info));
            }else {
                return response()->json(array('success' => false, 'data' => ''));
            }

        }catch(\Exception $e) {
            return response()->json(array('success' => false, 'data' => ''));
        }
    }

    // public function ticket_unread_msg_send()
    // {
    //     try {
    //         $unread_msg = '';

    //         $ticket_unread_msg = TicketDetail::where('is_read', 0)->get();

    //         foreach ($ticket_unread_msg as $key => $value) {
    //             $unread_msg .= $value->ticket_dtl_msg . '\n ';
    //         }

    //         $detail = [
    //             'msg' => $unread_msg,
    //         ];

    //     } catch (\Exception $e) {}
    // }
    
}
