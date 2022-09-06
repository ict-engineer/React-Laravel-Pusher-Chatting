<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Session;
use DateTime;
use Pusher\Pusher;
use App\Events\ChatNewEvent;

use App\Models\Channel;
use App\Models\ChatLog;
use App\Models\ChatType;
use App\Models\Message;
use App\Models\Contract;
use App\Models\TimeTrack;
use App\Models\Review;
use App\Models\Freelancer;
use App\Models\Client;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Invoice;
use App\Models\Payment;

class TransactionController extends Controller
{
  public function index()
  {
    try{
      if(auth()->user()->user_role == 'client')
      {
        $transactions = DB::table('channels')
          ->select('channels.channel_id', 'jobs.job_title', 'jobs.job_desc', 'channels.channel_status', 'channels.fre_id', 'channels.clt_id', 'channels.job_id', 'freelancers.fre_full_name as full_name', 'freelancers.fre_first_name as first_name', 'freelancers.fre_last_name as last_name', 'freelancers.fre_avatar as avatar', 'contracts.contract_status', 'contracts.contract_id')
          ->join('contracts', 'channels.channel_id', '=', 'contracts.channel_id')
          ->join('jobs', 'jobs.job_id', '=', 'channels.job_id')
          ->join('freelancers', 'freelancers.user_id', '=', 'channels.fre_id')
          ->where('channels.clt_id', auth()->user()->user_id)
          ->where('contracts.contract_status', '<>' , Contract::CONTRACT_ST_CANCELED)
          ->orderBy('channels.last_time', 'desc')->get();
        
        foreach($transactions as $transaction)
        {
          $chatLogs = ChatLog::where('channel_id', $transaction->channel_id)->oldest()->get();
          $unread = ChatLog::where('channel_id', $transaction->channel_id)->where('is_read', 0)->where('user_id', auth()->user()->user_id)->count();
          $history = [];

          foreach($chatLogs as $chatLog)
          {
            if($chatLog->chat_type == "message" || $chatLog->chat_type == 'm_chat')
            {
              $info = Message::where('msg_id', $chatLog->chat_log_event_id)->first();
            }
            else if(Str::contains($chatLog->chat_type, 'contract_'))
            {
              $info = Contract::where('contract_id', $chatLog->chat_log_event_id)->first();
              $info["user_id"] = $chatLog->user_id;
            }
            else if($chatLog->chat_type == 'timetrack')
            {
              $info = TimeTrack::where('trk_id', $chatLog->chat_log_event_id)->first();
            }
            else if($chatLog->chat_type == "review")
            {
              $info = Review::where('review_id', $chatLog->chat_log_event_id)->first();
              $info["user_id"] = $chatLog->user_id;
            }

            $info['chat_type'] = $chatLog->chat_type;
            array_push($history, $info);
          }
          $transaction->history = $history;
          $transaction->unread = $unread;
          $transaction->has_review = true;

          $count = Review::where('contract_id', $transaction->contract_id)->where('author_id', auth()->user()->user_id)->count();
          if($count == 0)
            $transaction->has_review = false;

          $first_inv_status = $this->first_invoice_status($transaction->contract_id);
          $has_pending_invoice = $this->has_pending_invoice($transaction->contract_id);
  
          if($first_inv_status != '') {
              $transaction->paid_first_invoice = $first_inv_status == 'PAID' ? true : false;
          }

          $transaction->has_pending_invoice = $has_pending_invoice;
        }

        return response()->json(array('status'=>true, 'data'=>$transactions));
      }
      else if(auth()->user()->user_role == 'freelancer')
      {
        $transactions = DB::table('channels')
          ->select('channels.channel_id', 'jobs.job_title', 'jobs.job_desc', 'channels.channel_status', 'channels.fre_id', 'channels.clt_id', 'channels.job_id', 'clients.clt_avatar as avatar', 'contracts.contract_status', 'contracts.contract_id')
          ->join('contracts', 'channels.channel_id', '=', 'contracts.channel_id')
          ->join('jobs', 'jobs.job_id', '=', 'channels.job_id')
          ->join('clients', 'clients.user_id', '=', 'channels.clt_id')
          ->where('channels.fre_id', auth()->user()->user_id)
          ->where('contracts.contract_status', '<>' , Contract::CONTRACT_ST_CANCELED)
          ->orderBy('channels.last_time', 'desc')->get();
        
        foreach($transactions as $transaction)
        {
          $chatLogs = ChatLog::where('channel_id', $transaction->channel_id)->oldest()->get();
          $unread = ChatLog::where('channel_id', $transaction->channel_id)->where('is_read', 0)->where('user_id', auth()->user()->user_id)->count();
          $history = [];

          foreach($chatLogs as $chatLog)
          {
            if($chatLog->chat_type == "message" || $chatLog->chat_type == 'm_chat')
            {
              $info = Message::where('msg_id', $chatLog->chat_log_event_id)->first();
            }
            else if(Str::contains($chatLog->chat_type, 'contract_'))
            {
              $info = Contract::where('contract_id', $chatLog->chat_log_event_id)->first();
              $info["user_id"] = $chatLog->user_id;
            }
            else if($chatLog->chat_type == 'timetrack')
            {
              $info = TimeTrack::where('trk_id', $chatLog->chat_log_event_id)->first();
            }
            else if($chatLog->chat_type == "review")
            {
              $info = Review::where('review_id', $chatLog->chat_log_event_id)->first();
              $info["user_id"] = $chatLog->user_id;
            }

            $info['chat_type'] = $chatLog->chat_type;
            array_push($history, $info);
          }
          $transaction->history = $history;
          $transaction->unread = $unread;
          $transaction->full_name = '';
          $transaction->first_name = '';
          $transaction->last_name = '';

          $transaction->has_review = true;

          $count = Review::where('contract_id', $transaction->contract_id)->where('author_id', auth()->user()->user_id)->count();
          if($count == 0)
            $transaction->has_review = false;

          $first_inv_status = $this->first_invoice_status($transaction->contract_id);
          $has_pending_invoice = $this->has_pending_invoice($transaction->contract_id);
  
          if($first_inv_status != '') {
              $transaction->paid_first_invoice = $first_inv_status == 'PAID' ? true : false;
          }

          $transaction->has_pending_invoice = $has_pending_invoice;
        }

        return response()->json(array('status'=>true, 'data'=>$transactions));
      }
    }
    catch(\Exception $e) {
      return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
    }
  }

  public function makeContract(Request $request)
  {
    try {
      // get data
      $channel_id = $request->input('channel_id');
      $contract_title = $request->input('title');
      $contract_desc = $request->input('desc');
      $contract_max_hrs = $request->input('hours');
      $contract_hourly_rate = $request->input('hourly');
      $contract_allow_manual_track = $request->input('isAllow');

      // data for chatlog
      $contract_status = Contract::CONTRACT_ST_PENDING;

      // create a new Contract.
      $contract_obj = new Contract();
      $contract_obj->contract_id = (string) Str::uuid();
      $contract_obj->contract_title = $contract_title;
      $contract_obj->contract_desc = $contract_desc;
      $contract_obj->contract_max_hrs = $contract_max_hrs;
      $contract_obj->contract_hourly_rate = $contract_hourly_rate;
      $contract_obj->contract_allow_manual_track = $contract_allow_manual_track;
      $contract_obj->contract_status = $contract_status;
      $contract_obj->channel_id = $channel_id;
      $contract_obj->save();

      $channel = Channel::where('channel_id', $channel_id)->first();
      $channel->last_time = new DateTime();
      $channel->save();
      // add chart_log
      $chatlog_obj = new ChatLog();
      $chatlog_obj->chat_log_id = (string) Str::uuid();
      $chatlog_obj->channel_id = $channel_id;
      $chatlog_obj->chat_type = "contract_sent";
      $chatlog_obj->chat_log_event_id = $contract_obj->contract_id;

      if($channel->clt_id != auth()->user()->user_id)
        $chatlog_obj->user_id = $channel->clt_id;
      else
        $chatlog_obj->user_id = $channel->fre_id;

      $chatlog_obj->save();
      
      // add event for offer sent
      return json_encode(array('success' => true, 'message' => 'A new offer was created successfully!'));

    } catch(\Exception $e) {
      return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
    }
  }

  public function changeContract(Request $request)
  {
    try {
      $contract = Contract::where('contract_id', $request->id)->first();
      $channel = Channel::where('channel_id', $contract->channel_id)->first();

      //send invoice when accept 
      if($request->status == "ended")
      {
        $result = $this->paid_check($channel->clt_id, $channel->channel_id);
        if(!$result)
          return response()->json(array('status'=>false, 'message'=>'There are unpaid invoices'), 422);
      }

      if($request->status == "accepted")
      {
        $payment_obj = new PaymentController();
        $client_obj = Client::where('user_id', $channel->clt_id)->first();
        $client_user = User::where('user_id', $channel->clt_id)->first();

          //paypal client 
        $recipients = array(
          'fullName' => $client_obj->clt_full_name ?? "CLIENT",
          'email' => $client_user->payment_email
        );

        $hourly_rate = $contract->contract_hourly_rate;
        $records = [];
        $records[] = TimeTrack::where('contract_id', $contract->contract_id)->first();

        $items[] = array(
            "name" => $contract->contract_title,
            "description" => "prepaid this Contract before begin.",
            "quantity" => round(($contract->contract_max_hrs / 2), 2),
            "unit_amount" => array(
                "currency_code" => env('PAYPAL_CURRENCY') ?? 'USD',
                "value" => $hourly_rate
            ),
            "tax" => array(
                "name" => "Paypal + Service fee",
                "percent" => (env('PAYPAL_FEE') ?? 3) + (env('PAYPAL_CLIENT_FEE') ?? 3)
            )
        );

        $result = $payment_obj->sendInvoice($recipients, $items, $contract, $records);

        if(!$result) {
          return response()->json(array('status'=>false, 'message'=>'Invoice sent failed'), 422);
        }
      }

      $contract->contract_status = $request->status;
      $contract->save();
     
      // add chart_log
      $chatlog_obj = new ChatLog();
      $chatlog_obj->chat_log_id = (string) Str::uuid();
      $chatlog_obj->channel_id = $contract->channel_id;
      $chatlog_obj->chat_type = "contract_".$request->status;
      $chatlog_obj->chat_log_event_id = $contract->contract_id;

      if($channel->clt_id != auth()->user()->user_id)
        $chatlog_obj->user_id = $channel->clt_id;
      else
        $chatlog_obj->user_id = $channel->fre_id;

      $chatlog_obj->save();

      // add event for offer sent
      $options = array(
        'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        'encrypted' => false
      );

      $pusher = new Pusher(
          config('broadcasting.connections.pusher.key'),
          config('broadcasting.connections.pusher.secret'),
          config('broadcasting.connections.pusher.app_id'), $options
      );

      
      $channel->last_time = new DateTime();
      $channel->save();
      $contract['user_id'] =  $chatlog_obj->user_id;
      $contract['chat_type'] = $chatlog_obj->chat_type;

      if(auth()->user()->user_role == "freelancer")
      {
        $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'contract', 'data' => $contract, 'status' => $request->status]);
      }
      else{
        $pusher->trigger('chat-new-channel-'.$channel->fre_id, 'MessageSent', ['type'=>'contract', 'data' => $contract, 'status' => $request->status]);
      }

      return json_encode(array('success' => true, 'message' => 'successful!', 'data' => $contract));
    } catch(\Exception $e) {
      return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
    }
  }
  
  public function saveMessage(Request $request)
    {
      $options = array(
        'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        'encrypted' => false
      );

      $pusher = new Pusher(
          config('broadcasting.connections.pusher.key'),
          config('broadcasting.connections.pusher.secret'),
          config('broadcasting.connections.pusher.app_id'), $options
      );

      try {
        if($request->chat_type == "message")
        {
          $message = new Message();
          $message->msg_id = (string) Str::uuid();
          $message->msg_body = $request->msg_body;
          $message->user_id = $request->user_id;
          $message->channel_id = $request->channel_id;
          $message->save();
          
          $channel = Channel::where('channel_id', $request->channel_id)->first();
          $channel->last_time = new DateTime();
          $channel->save();

          if($channel->clt_id != auth()->user()->user_id)
          {
            $this->_createLog($message->msg_id, $channel->clt_id, $channel->channel_id, "message");
            $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'message', 'data' => $message]);
          }

          if($channel->fre_id != auth()->user()->user_id)
          {
            $this->_createLog($message->msg_id, $channel->fre_id, $channel->channel_id, "message");
            $pusher->trigger('chat-new-channel-'.$channel->fre_id, 'MessageSent', ['type'=>'message', 'data' => $message]);
          }
          return response()->json(array('status'=>true, 'data'=>$message));
        }
        else if($request->chat_type == "timetrack")
        {
          $from_time = $request->trk_from;
          $to_time = $request->trk_to;
          $contract = Contract::where('channel_id', $request->channel_id)->latest()->first();
          // validate the time available for this contract.
          // ...
          $timetracks = TimeTrack::where('trk_date','=', $request->trk_date)
                                  ->where('contract_id', $contract->contract_id)
                                  ->where(function ($query) use($from_time, $to_time) {
                                      $query->whereTime('trk_from','<=', $from_time)
                                          ->whereTime('trk_to','>=' , $from_time)
                                          ->orWhere(function($query1) use($to_time) {
                                             $query1->whereTime('trk_from','<=', $to_time)
                                                  ->whereTime('trk_to','>=' , $to_time);
                                          });
                                  })
                                  ->get();
          if(count($timetracks) > 0){
            return response()->json(array('status'=>false, 'message'=>"Already exists in same period"), 422);
          }
  
          // create a new Contract.
          $trk_obj = new TimeTrack();
          $trk_obj->trk_id = (string) Str::uuid();
          $trk_obj->contract_id = $contract->contract_id;
          $trk_obj->trk_is_manual = 1;
          $trk_obj->trk_date = $request->trk_date;
          $trk_obj->trk_from = $from_time;
          $trk_obj->trk_to = $to_time;
          $trk_obj->trk_total_hrs = $request->trk_total_hrs;
          $trk_obj->trk_status = 0;
          $trk_obj->trk_description = $request->trk_description ? $request->trk_description : "";
          $trk_obj->save();
          
          $channel = Channel::where('channel_id', $request->channel_id)->first();

          $trk_obj->channel_id = $channel->channel_id;

          if($channel->clt_id != auth()->user()->user_id)
          {
            $this->_createLog($trk_obj->trk_id, $channel->clt_id, $channel->channel_id, "timetrack");
            $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'timetrack', 'data' => $trk_obj]);
          }

          if($channel->fre_id != auth()->user()->user_id)
          {
            $this->_createLog($trk_obj->trk_id, $channel->fre_id, $channel->channel_id, "timetrack");
            $pusher->trigger('chat-new-channel-'.$channel->fre_id, 'MessageSent', ['type'=>'timetrack', 'data' => $trk_obj]);
          }
          return response()->json(array('status'=>true, 'data'=>$trk_obj));
        }
        else if($request->chat_type == "review"){
          // send review
          // check if the contract is 'ended' now. 
          $contract_obj = Contract::where('contract_id', $request->contract_id)->first();
          $channel = Channel::where('channel_id', $contract_obj->channel_id)->first();

          if($contract_obj['contract_status'] != Contract::CONTRACT_ST_ENDED){
              $message = 'This contract was not ended.';
              return json_encode(array('success' => false, 'message' => $message), 422);        
          }

          // check if the user sent the review already
          $chatlog_review = Review::where('contract_id', $contract_obj->contract_id)
                  ->where('author_id', auth()->user()->user_id)
                  ->first();

          if($chatlog_review){
              $message = "You already sent the Contract Review.";
              return response()->json(array('success' => false, 'message' => $message), 422);        
          }

          // add review 
          $review_obj = new Review();
          $review_obj->review_id = (string) Str::uuid();
          $review_obj->contract_id = $contract_obj->contract_id;
          $review_obj->review_rating = isset($request->review_rating) ? $request->review_rating : 0;
          $review_obj->review_feedback = isset($request->review_feedback) ? $request->review_feedback : "";
          $review_obj->author_id = auth()->user()->user_id;
          $review_obj->save();

          $review_obj->channel_id = $channel->channel_id;

          if($channel->clt_id != auth()->user()->user_id)
          {
            $this->_createLog($review_obj->review_id, $channel->clt_id, $channel->channel_id, "review");
            $review_obj->user_id = $channel->clt_id;
            $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'review', 'data' => $review_obj]);
          }

          if($channel->fre_id != auth()->user()->user_id)
          {
            $this->_createLog($review_obj->review_id, $channel->fre_id, $channel->channel_id, "review");
            $review_obj->user_id = $channel->fre_id;
            $pusher->trigger('chat-new-channel-'.$channel->fre_id, 'MessageSent', ['type'=>'review', 'data' => $review_obj]);
          }
          return response()->json(array('status'=>true, 'data'=>$review_obj));
        }
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }

    public function _createLog($id, $user_id, $channel_id, $chat_type)
    {
      $chatlog = new ChatLog();
      $chatlog->chat_log_id = (string) Str::uuid();
      $chatlog->chat_log_event_id = $id;
      $chatlog->user_id = $user_id;
      $chatlog->channel_id = $channel_id;
      $chatlog->chat_type = $chat_type;
      $chatlog->save();
    }

    public function setUnreadAsReadById(Request $request)
    {
      try{
          ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $request->id)->where('is_read', 0)->update(['is_read' => 1]);
          return response()->json(array('status'=>true));
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }
    //before end-contract paid_check
    public function paid_check($clt_id, $channel_id) {
      try{
          //freelancer and client sendInvoice check
          $payment_obj = new PaymentController();

          $client_obj = Client::where('user_id', $clt_id)->first();
          $contract_obj = Contract::where('channel_id', $channel_id)->first();
          $invoice_obj = Invoice::select(DB::raw("SUM(inv_sub_total) as total"))
                              ->where('contract_id', $contract_obj->contract_id)
                              ->whereIn('inv_status', ['SENT', 'PAID', 'PAYMENT_PENDING', 'PARTIALLY_PAID'])
                              ->where('inv_type', 2)
                              ->groupBy('contract_id')->first();

          $time_total = TimeTrack::select(DB::raw("SUM(trk_total_hrs) as total"))
                              ->where('contract_id', $contract_obj->contract_id)
                              ->groupBy('contract_id')->first();
         
          //paypal client 
          $recipients = array(
              'fullName' => $client_obj->clt_full_name,
              'email' => $client_obj->clt_invoice_email
          );

          $hourly_rate = $contract_obj->contract_hourly_rate;
          $tracked_sum = ($time_total->total ?? 0) * $contract_obj->contract_hourly_rate;
          $amount = round($tracked_sum - ($invoice_obj->total ?? 0), 2);
          $records = [];
          
          $records = TimeTrack::where('contract_id', $contract_obj->contract_id)->orderBy('created_at', 'desc')->get();
          $tracked_total = 0;
          $time_records = [];
          $sent_hours = 0;

          foreach ($records as $key => $item) {
              $time_records[] = $item;
              $tracked_total += $item['trk_total_hrs'] * $hourly_rate;

              if( $tracked_total > $amount) {
                  $sent_hours = ($tracked_total - $amount) / $hourly_rate;
              }

              if($tracked_total >= $amount) break;
          }

          $time_records = array_reverse($time_records);
          $time = $time_records[0]-> trk_from;
          $timesplit = explode(':', $time);
          $min = mktime($timesplit[0], $timesplit[1] + $sent_hours * 60, $timesplit[2]);
          $time_records[0]->trk_from = date("H:i:s", $min);
          $time_records[0]->trk_total_hrs = $time_records[0]->trk_total_hrs - $sent_hours;

          $items = [];

          foreach ($time_records as $key => $value) {
              $items[] = array(
                  "name" => $contract_obj->contract_title,
                  "description" => 'Billed:'. $value['trk_date'],
                  "quantity" => round($value['trk_total_hrs'], 2),
                  "unit_amount" => array(
                      "currency_code" => env('PAYPAL_CURRENCY') ?? 'USD',
                      "value" => $hourly_rate
                  ),
                  "tax" => array(
                      "name" => "Paypal + Service fee",
                      "percent" => (env('PAYPAL_FEE') ?? 3) + (env('PAYPAL_CLIENT_FEE') ?? 3)
                  )
              );
          }

          if($amount > 0) {
              
              if(auth()->user()->user_role == "freelancer") {
                  return false;
              }
              
              $payment_obj->sendInvoice($recipients, $items, $contract_obj, $time_records);

          } else if($amount < 0) {
              
              if(auth()->user()->user_role == "freelancer") {
                  return false;
              }

              $inv_id = Invoice::where('inv_status', 'SENT' )
                              ->where('contract_id', $contract_obj->contract_id)
                              ->first();

              $result = $payment_obj->cancel_invoice($inv_id->inv_id);

              if($result && $tracked_sum) {

                  $payment_obj->sendInvoice($recipients, $items, $contract_obj, $time_records);

              }
          }

          $invoice_inv_status = Invoice::where('contract_id', $contract_obj->contract_id)
                                      ->where('inv_status', 'SENT')
                                      ->where('inv_type', 2)
                                      ->get();

          if ( !sizeof($invoice_inv_status) ) {
              return true;
          }

          return false;
      }catch(\Exception $ex) {
          return false;
      }
    }

    //first invoice status
    public function first_invoice_status($contract_id) {
        try {
            $invoice_obj = Invoice::where('contract_id', $contract_id)->first();
            return $invoice_obj->inv_status;

        }catch(\Exception $ex) {
            return "";
        }
    }

    //check pending invoice of contract
    public function has_pending_invoice($contract_id) {
        try {
            $invoice_objs = Invoice::select('created_at')
                                ->where('contract_id', $contract_id)
                                ->where('inv_status', 'SENT')
                                ->where('inv_type', 2)
                                ->get();

            $end_date = date_create(date('Y-m-d'));
            foreach ($invoice_objs as $key => $value) {
                $start_date = date_create(date_format($value->created_at, 'Y-m-d'));
                $diff=date_diff($start_date,$end_date);
                if($diff->format("%a") >= 2) {
                    return true;
                }
            }

            return false;
        }catch(\Exception $ex) {
            return false;
        }
    }
}
