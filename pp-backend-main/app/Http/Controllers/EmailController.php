<?php
namespace App\Http\Controllers;

use App\Mail\RateSendEmail;
use App\Mail\RequiredClients;
use App\Models\Channel;
use App\Models\Freelancer;
use App\Models\RateupClientFeedbacks;
use app\Models\TicketDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    public function rate_send_email($id, $able_rate)
    {
        try {
            // $id = $request->input('id');
            // $able_rate = $request->input('able_rate');
            $fre_obj = Freelancer::where('fre_id', $id)->first();

            RateupClientFeedbacks::where('fre_id', $fre_obj->fre_id)->delete();

            $send_info = Channel::where('channels.fre_id', $fre_obj->fre_id)
                        ->leftjoin('clients', 'clients.clt_id', 'channels.clt_id')
                        ->leftjoin('contracts', 'contracts.channel_id', 'channels.channel_id')
                        ->where('contracts.contract_status', 'ended')
                        ->orderBy('contracts.created_at', 'desc')
                        ->limit(5)->get();

            $clt_info = '';

            foreach ($send_info as $key => $value) {

                $email_obj = new RateupClientFeedbacks();
                $email_obj->id = (string) Str::uuid();
                $email_obj->fre_id = $fre_obj->fre_id;
                $email_obj->clt_id = $value->clt_id;
                $email_obj->current_rate = $fre_obj->fre_rate;
                $email_obj->able_rate = $able_rate;
                $email_obj->rateup = 3;
                $email_obj->hire_able = 3;
                $email_obj->fre_name = $fre_obj->fre_full_name;
                $email_obj->clt_name = $value->clt_full_name;
                $email_obj->project_name = $value->contract_title;
                $email_obj->save();

                $last_id = RateupClientFeedbacks::latest()->first();

                $detail = [
                    'name' => $value->clt_full_name,
                    'fre_name' => $fre_obj->fre_full_name,
                    'project_name' => $value->contract_title,
                    'rate' => $fre_obj->fre_rate,
                    'able_rate' => $able_rate,
                    'answer_url' => env('APP_URL') . '/rateup/feedback?id=' . $last_id->id
                ];

                $clt_email = User::where('user_id', $value->user_id)->first();
                $clt_info .= $value->clt_full_name . ' : ' . $clt_email->user_email . ', ';

                Mail::to($clt_email->user_email)->send(new RateSendEmail($detail));
            }

            $detail = [
                'clt_info' => $clt_info,
            ];
            $fre_email = User::where('user_id', $fre_obj->user_id)->first();

            Mail::to($fre_email->user_email)->send(new RequiredClients($detail));

            // return response()->json(array('success' => true, 'data' => ''));

            return true;
        } catch (\Exception $e) {

            return false;
            // return response()->json(array('success' => false, 'data' => ''));
        }
    }

   
}
