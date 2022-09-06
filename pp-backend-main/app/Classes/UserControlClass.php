<?php 
namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;
use App\Models\Portfolio;
use App\Models\HashTag;
use App\Models\PortfolioHashTag;
use App\Models\Channel;
use App\Models\Contract;
use App\Models\TimeTrack;
use App\Models\ChatLog;
use App\Models\Review;

class UserControlClass {
  static function getUserData($user_id)
  {
    $user = User::where('user_id', $user_id)->first(['user_id', 'email', 'payment_email', 'created_at', 'user_role']);

    if($user['user_role'] == "freelancer")
    {
      $freelancer = Freelancer::where('user_id', $user['user_id'])->first();
      $user['full_name'] = $freelancer->fre_full_name;
      $user['first_name'] = $freelancer->fre_first_name;
      $user['last_name'] = $freelancer->fre_last_name;
      $user['skype_id'] = $freelancer->fre_skype_id;
      $user['timezone'] = $freelancer->fre_timezone;
      $user['english_level'] = $freelancer->fre_english_level;
      $user['description'] = $freelancer->fre_desc;
      $user['avatar'] = $freelancer->fre_avatar;
      $user['phone'] = $freelancer->fre_phone;
      $portfolios = Portfolio::where('fre_id', auth()->user()->user_id)->get(['por_title','por_desc', 'por_id']);;

      foreach($portfolios as $portfolio)
      {
        $tagIds = PortfolioHashTag::where('por_id', $portfolio->por_id)->get();
        $tags = [];

        foreach($tagIds as $tagId)
        {
          $name = HashTag::where('hashtag_id', $tagId->hashtag_id)->first();
          array_push($tags, $name->hashtag_name);
        }

        $portfolio['por_tags'] = $tags;
      }
      $user['portfolios'] = $portfolios;

      $channel = Channel::where('fre_id', auth()->user()->user_id)->get();

      $channelIds = [];
      foreach ($channel as $key => $value) {
          $channelIds[] = $value->channel_id;
      }

      $contracts_ended = Contract::whereIn('channel_id', $channelIds)
          ->where('contract_status', 'ended')
          ->get();

      $contractIds = [];
      foreach ($contracts_ended as $key => $value) {
          $contractIds[] = $value->contract_id;
      }

      $reviews = Review::whereIn('contract_id', $contractIds)
          ->where('author_id', '<>', auth()->user()->user_id)
          ->get();

      foreach($reviews as $review)
      {
        $contract = Contract::where('contract_id', $review->contract_id)->first();
        $review['rate'] = $contract->contract_hourly_rate;
        $review['track_hours'] = TimeTrack::where('contract_id', $review->contract_id)->sum('trk_total_hrs');
      }

      $user['reviews'] = $reviews;

      //unread meeting
      $countM = ChatLog::where('user_id', auth()->user()->user_id)->where('is_read', false)->where('chat_type', 'm_chat')->count();
      $user['unreadMeeting'] =  $countM;

      //unread transaction
      $countT = ChatLog::where('user_id', auth()->user()->user_id)->where('is_read', false)->where('chat_type', '<>' ,'m_chat')->count();
      $user['unreadTransaction'] =  $countT;
    }
    else if($user['user_role'] == "client")
    {
      $client = Client::where('user_id', $user['user_id'])->first();
      $user['avatar'] = $client->clt_avatar;
      $user['full_name'] = "";
      $user['first_name'] = "";
      $user['last_name'] = "";

      $channel = Channel::where('clt_id', auth()->user()->user_id)->get();

      $channelIds = [];
      foreach ($channel as $key => $value) {
          $channelIds[] = $value->channel_id;
      }

      $contracts_ended = Contract::whereIn('channel_id', $channelIds)
          ->where('contract_status', 'ended')
          ->get();

      $contractIds = [];
      foreach ($contracts_ended as $key => $value) {
          $contractIds[] = $value->contract_id;
      }

      $reviews = Review::whereIn('contract_id', $contractIds)
          ->where('author_id', '<>', auth()->user()->user_id)
          ->get();

      foreach($reviews as $review)
      {
        $contract = Contract::where('contract_id', $review->contract_id)->first();
        $review['rate'] = $contract->contract_hourly_rate;
        $review['track_hours'] = TimeTrack::where('contract_id', $review->contract_id)->sum('trk_total_hrs');
      }

      $user['reviews'] = $reviews;

      //unread meeting
      $countM = ChatLog::where('user_id', auth()->user()->user_id)->where('is_read', false)->where('chat_type', 'm_chat')->count();
      $user['unreadMeeting'] =  $countM;

      //unread transaction
      $countT = ChatLog::where('user_id', auth()->user()->user_id)->where('is_read', false)->where('chat_type', '<>' ,'m_chat')->count();
      $user['unreadTransaction'] =  $countT;
    }

    return $user;
  }
}
