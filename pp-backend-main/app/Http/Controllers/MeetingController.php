<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use DB;
use DateTime;

use App\Models\User;
use App\Models\Client;
use App\Models\Job;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Contract;
use App\Models\Freelancer;
use App\Models\ChatLog;

use Pusher\Pusher;

class MeetingController extends Controller
{
    //
    public function index()
    {
      try {
        if(Auth::user()->user_role == 'client')
        {
          $jobs = Job::where('user_id', Auth::user()->user_id)->get(['job_id as id', 'job_title as title', 'job_status as status', 'created_at as created']);

          foreach($jobs as $job)
          {
            $lastMessage = DB::table('messages')
            ->join('channels', 'channels.channel_id', '=', 'messages.channel_id')
            ->where('channels.job_id', $job->id)
            ->orderBy('messages.created_at', 'DESC')->first();
            if($lastMessage)
              $job['last_message'] = $lastMessage->msg_body;
            else
              $job['last_message'] = '';
          }
          return response()->json(array('status'=>true, 'data'=>$jobs));
        }
        else if(Auth::user()->user_role == 'freelancer')
        {
          $channels = Channel::where('fre_id', Auth::user()->user_id)->get();
          $jobs = [];

          foreach($channels as $channel)
          {
            $job = Job::where('job_id', $channel->job_id)->first(['job_id as id', 'job_title as title', 'job_status as status', 'created_at as created']);
            $lastMessage = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->latest()->first();
            if($lastMessage)
            $job['last_message'] = $lastMessage->msg_body;
            else
            $job['last_message'] = '';
            array_push($jobs, $job);
          }
          return response()->json(array('status'=>true, 'data'=>$jobs));
        }
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }

    public function getMeetingInfoById($job_id)
    {
      try{
        if(auth()->user()->user_role == 'client')
        {
          $meeting = Job::where('job_id', $job_id)->first(['job_title as title', 'job_desc as description']);
          $channels = Channel::where('job_id', $job_id)->orderBy('fre_id')->get();
          $contacts = [];
          $symbol = 'A';

          foreach($channels as $channel)
          {
            $contract_count = Contract::where('channel_id', $channel->channel_id)->where('contract_status', '<>', 'canceled')->count();

            if($contract_count)
              $channel['has_contract'] = true;
            else
              $channel['has_contract'] = false;

            if($channel->fre_id == '')
            {
              $channel['full_name'] = "Group Chat";
              $channel['first_name'] = "Group";
              $channel['last_name'] = "Chat";
              $channel['avatar'] = "";
              $last_message = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->latest()->first();

              if($last_message == null)
              {
                $channel['last_message'] = '';
                $channel['lastMessageTime'] = '';  
              }
              else{
                $channel['last_message'] = $last_message->msg_body;
                $channel['lastMessageTime'] = $last_message->updated_at;
              }

              $unread = ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $channel->channel_id)->where('chat_type', 'm_chat')->where('is_read', 0)->count();
              $channel['unread'] = $unread;

              $messages = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->oldest()->get();
              $channel['messages'] = $messages;
            }
            else
            {
              $contacts[$channel->fre_id] = $symbol;
              $symbol ++;

              $freelancer = Freelancer::where('user_id', $channel->fre_id)->first();
              $channel['full_name'] = $freelancer->fre_full_name;
              $channel['first_name'] = $freelancer->fre_first_name;
              $channel['last_name'] = $freelancer->fre_last_name;
              $channel['avatar'] = $freelancer->fre_avatar;
              $last_message = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->latest()->first();

              if($last_message == null)
              {
                $channel['last_message'] = '';
                $channel['lastMessageTime'] = '';  
              }
              else{
                $channel['last_message'] = $last_message->msg_body;
                $channel['lastMessageTime'] = $last_message->updated_at;
              }

              $unread = ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $channel->channel_id)->where('chat_type', 'm_chat')->where('is_read', 0)->count();
              $channel['unread'] = $unread;
              
              $messages = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->oldest()->get();
              $channel['messages'] = $messages;
            }
          }

          $meeting['channels'] = $channels;
          $meeting['contacts'] = $contacts;

          return response()->json(array('status'=>true, 'data'=>$meeting));
        }
        else if(auth()->user()->user_role == 'freelancer')
        {
          $meeting = Job::where('job_id', $job_id)->first(['job_title as title', 'job_desc as description']);
          $channels = Channel::where('job_id', $job_id)->orderBy('fre_id')->get();
          $new_channels = [];
          $contacts = [];
          $symbol = 'A';

          foreach($channels as $channel)
          {
            if($channel->fre_id == '')
            {
              $channel['full_name'] = "Group Chat";
              $channel['first_name'] = "Group";
              $channel['last_name'] = "Chat";
              $channel['avatar'] = "";
              $last_message = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->latest()->first();

              $client = Client::where('user_id', $channel->clt_id)->first();
              $channel['clt_avatar'] = $client->clt_avatar;

              if($last_message == null)
              {
                $channel['last_message'] = '';
                $channel['lastMessageTime'] = '';  
              }
              else{
                $channel['last_message'] = $last_message->msg_body;
                $channel['lastMessageTime'] = $last_message->updated_at;
              }

              $unread = ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $channel->channel_id)->where('chat_type', 'm_chat')->where('is_read', 0)->count();
              $channel['unread'] = $unread;

              $messages = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->oldest()->get();
              $channel['messages'] = $messages;
              array_push($new_channels, $channel);
            }
            else 
            {
              $contacts[$channel->fre_id] = $symbol;
              $symbol ++;
              if($channel->fre_id == auth()->user()->user_id)
              {
                $channel['full_name'] = "Private Chat";
                $channel['first_name'] = "Private";
                $channel['last_name'] = "Chat";

                $client = Client::where('user_id', $channel->clt_id)->first();
                $channel['clt_avatar'] = $client->clt_avatar;

                $channel['avatar'] = "";
                $last_message = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->latest()->first();
  
                if($last_message == null)
                {
                  $channel['last_message'] = '';
                  $channel['lastMessageTime'] = '';  
                }
                else{
                  $channel['last_message'] = $last_message->msg_body;
                  $channel['lastMessageTime'] = $last_message->updated_at;
                }
  
                $unread = ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $channel->channel_id)->where('chat_type', 'm_chat')->where('is_read', 0)->count();
                $channel['unread'] = $unread;
                
                $messages = Message::where('channel_id', $channel->channel_id)->where('is_tran', false)->oldest()->get();
                $channel['messages'] = $messages;
                array_push($new_channels, $channel);
              }
            }
          }
  
          $meeting['channels'] = $new_channels;
          $meeting['contacts'] = $contacts;

          return response()->json(array('status'=>true, 'data'=>$meeting));
        }
      }
      catch(\Exception $e) {
        return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }

    public function saveMessage(Request $request)
    {
      try {
        $message = new Message();
        $message->msg_id = (string) Str::uuid();
        $message->msg_body = $request->msg_body;
        $message->user_id = $request->user_id;
        $message->channel_id = $request->channel_id;
        $message->is_tran = false;
        $message->save();
        
        $channel = Channel::where('channel_id', $request->channel_id)->first();
        $channel->last_time = new DateTime();
        $channel->save();

        $options = array(
          'cluster' => config('broadcasting.connections.pusher.options.cluster'),
          'encrypted' => false
        );

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'), $options
        );

        if($channel->fre_id == '') //Group chat
        {
          $channels = Channel::where('job_id', $channel->job_id)->get();

          foreach($channels as $ch)
          {
            if($ch->fre_id != '' && $ch->fre_id != auth()->user()->user_id)
            {
              $this->_createMessageLog($message->msg_id, $ch->fre_id, $channel->channel_id);
              $pusher->trigger('chat-new-channel-'.$ch->fre_id, 'MessageSent', ['type'=>'m_chat', 'data' => $message]);
            }
          }

          if($channel->clt_id != auth()->user()->user_id)
          {
            $this->_createMessageLog($message->msg_id, $channel->clt_id, $channel->channel_id);
            $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'m_chat', 'data' => $message]);
          }
        }
        else{
          if($channel->clt_id != auth()->user()->user_id)
          {
            $this->_createMessageLog($message->msg_id, $channel->clt_id, $channel->channel_id);
            $pusher->trigger('chat-new-channel-'.$channel->clt_id, 'MessageSent', ['type'=>'m_chat', 'data' => $message]);
          }

          if($channel->fre_id != auth()->user()->user_id)
          {
            $this->_createMessageLog($message->msg_id, $channel->fre_id, $channel->channel_id);
            $pusher->trigger('chat-new-channel-'.$channel->fre_id, 'MessageSent', ['type'=>'m_chat', 'data' => $message]);
          }
        }

        return response()->json(array('status'=>true, 'data'=>$message));
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }

    public function setUnreadAsReadById(Request $request)
    {
      try{
          ChatLog::where('user_id', auth()->user()->user_id)->where('channel_id', $request->id)->where('chat_type', 'm_chat')->where('is_read', 0)->update(['is_read' => 1]);
          return response()->json(array('status'=>true));
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }

    public function _createMessageLog($msg_id, $user_id, $channel_id)
    {
      $chatlog = new ChatLog();
      $chatlog->chat_log_id = (string) Str::uuid();
      $chatlog->chat_log_event_id = $msg_id;
      $chatlog->user_id = $user_id;
      $chatlog->channel_id = $channel_id;
      $chatlog->chat_type = "m_chat";
      $chatlog->save();
    }
}
