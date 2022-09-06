<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Job;
use App\Models\HashTag;
use App\Models\JobHashtag;
use App\Models\Client;
use App\Models\Channel;

class JobController extends Controller
{
    //
    public function store(Request $req)
    {
      try {
        // get data from frontend
        $user = auth()->user();
        $client =               Client::where('user_id', $user->user_id)->first();
        $description =          $req->input('job_desc', '');
        $title =                $req->input('job_title', '');
        $hashtags =             $req->input('job_tags', []);
        $freelancers =          $req->input('users', []);
        // check exception
        if(!$title || !$description || !$user->user_id || !count($hashtags)) {
            throw new \Exception('Invalid Data');
        }
        
        // insert a Job 
        $job = new Job();
        $job->job_id = (string) Str::uuid();
        $job->user_id = $user->user_id;
        $job->job_title = $title;
        $job->job_desc = $description;
        $job->save();

        // check hashtags
        if(count($hashtags)){
          foreach($hashtags as $hashtag) {
              // check if it is already exist on hashtags table
              if($hashtag != '' && $hashtag != null)
              {
                $hash = HashTag::where('hashtag_name', $hashtag)->first();
                if($hash)
                {
                    $hashtag_id = $hash->hashtag_id;
                    // save job_hashtags
                    $job_hash = new JobHashtag();
                    $job_hash->job_hashtag_id = (string) Str::uuid();
                    $job_hash->job_id = $job->job_id;
                    $job_hash->hashtag_id = $hashtag_id;
                    $job_hash->save();
                }
              }
            }
        }

        /*Create Channels*/

        //create group channel
        $channel = new Channel();
        $channel->channel_id = (string) Str::uuid();
        $channel->fre_id = '';
        $channel->clt_id = auth()->user()->user_id;
        $channel->job_id = $job->job_id;
        $channel->save();

        foreach($freelancers as $freelancer)
        {
          $channel = new Channel();
          $channel->channel_id = (string) Str::uuid();
          $channel->fre_id = $freelancer;
          $channel->clt_id = auth()->user()->user_id;
          $channel->job_id = $job->job_id;
          $channel->save();
        }

        return response()->json(array('status'=>true, 'message'=>'Success'));
      }
      catch(\Exception $e) {
          return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
      }
    }
}
