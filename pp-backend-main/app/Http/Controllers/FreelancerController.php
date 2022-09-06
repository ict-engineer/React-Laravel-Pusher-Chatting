<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Contract;
use App\Models\EnglishLevel;
use App\Models\Freelancer;
use App\Models\FreelancerTimezone;
use App\Models\Invoice;
use App\Models\Review;
use App\Models\TimeTrack;
use App\Models\Timezone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Classes\UserControlClass;

class FreelancerController extends Controller
{
    public function getTopFreelancers()
    {
      $freelancers = Freelancer::orderBy('fre_id')->take(5)->get(['user_id']);
      $result = [];
      foreach($freelancers as $freelancer)
      {
        array_push($result, UserControlClass::getUserData($freelancer->user_id));
      }

      return response()->json(['data' => $result]);
    }

    /**
     * Show the application dashboard for Freelancer
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('freelancer');
    }

    /**
     * Display a listing of the resource.
     * POST a listing of the freelancers.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewList()
    {
        return response()->json(Freelancer::all()->toArray());
    }

    /**
     * POST: getDetail info.
     */
    public function getDetail()
    {
        $userid = Auth::user()->user_id;

        $freelancer = Freelancer::where('user_id', $userid)->first();

        $freelancer_rate_req_status = Freelancer::where('fre_id', $freelancer->fre_id)
            ->where('fre_rate_req_status', 2)
            ->whereDate('updated_at', '<', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 6 days')))->first();

            if(isset($freelancer_rate_req_status) > 0) {
                $freelancer_rate_req_status->fre_rate_req_status = 0;
                $freelancer_rate_req_status->save();
            }

        $free_timezones = [];
        if ($freelancer) {
            $free_timezones = FreelancerTimezone::select('timezone_id')->where('fre_id', $freelancer->fre_id)->get()->pluck('timezone_id');

            if ($freelancer->fre_english_level_id != "") {
                $englevel = EnglishLevel::where('english_level_id', $freelancer->fre_english_level_id)->first();
            } else {
                $englevel = EnglishLevel::first();
            }

            $channel = Channel::where('fre_id', $freelancer->fre_id)->get();

            $channelIds = [];
            foreach ($channel as $key => $value) {
                $channelIds[] = $value->channel_id;
            }

            $contracts_all_id = Contract::select('contract_id')
                ->whereIn('channel_id', $channelIds)->get();

            $invoice_obj = Invoice::select(DB::raw('SUM(inv_total) as total_profit'))
                ->whereIn('contract_id', $contracts_all_id)
                ->where('inv_status', 'PAID')
                ->get();

            $contracts_ended = Contract::whereIn('channel_id', $channelIds)
                ->where('contract_status', 'ended')
                ->get();

            $contractIds = [];
            foreach ($contracts_ended as $key => $value) {
                $contractIds[] = $value->contract_id;
            }

            $reviews = Review::whereIn('contract_id', $contractIds)
                ->where('author_id', '<>', $freelancer->fre_id)
                ->get();

            $time_total = TimeTrack::select('contract_id', DB::raw("SUM(trk_total_hrs) as total"))
                ->whereIn('contract_id', $contractIds)
                ->groupBy('contract_id')->get();

            foreach ($contracts_ended as $key => $contract) {
                $contracts_ended[$key]['total_hours'] = 0;
                foreach ($time_total as $time) {
                    if ($contract['contract_id'] == $time['contract_id']) {
                        $contracts_ended[$key]['total_hours'] = $time['total'];
                        break;
                    }
                }
            }
        } else {
            if ($userid != null) {
                // check the english Level.
                $englevel = EnglishLevel::first();

                // generate freelancer for this user.
                $freelancer = new Freelancer();
                $freelancer->fre_id = (string) Str::uuid();
                $freelancer->user_id = $userid;
                $freelancer->fre_payment_email = "";
                $freelancer->fre_full_name = "";
                $freelancer->fre_en_name = "";
                $freelancer->fre_phone = "";
                $freelancer->fre_skype_id = "";
                $freelancer->fre_avatar = "";
                $freelancer->fre_short_desc = "";
                $freelancer->fre_english_level_id = "";

                if ($englevel) {
                    $freelancer->fre_english_level_id = $englevel->english_level_id;
                }

                $freelancer->save();
            }
        }

        $freelancer['fre_timezones'] = $free_timezones;
        $freelancer['english_level'] = $englevel;
        $freelancer['total_work_hours'] = 100;
        $freelancer['profile_rate'] = 3.5;
        $freelancer['total_profit'] = $invoice_obj[0]['total_profit'];

        $timezones = Timezone::get();

        return response()->json(array('success' => true, 'freelancer' => $freelancer, 'timezones' => $timezones, 'reviews' => $reviews, 'contracts' => $contracts_ended));
    }

    /**
     * POST : updateFreelancer
     */
    public function updateFreelancer(Request $request)
    {
        $data = $request->all();
        $user_id = Auth::user()->user_id;
        $fre_id = $data['fre_id'];

        $freelancer = Freelancer::where('fre_id', $fre_id)->first();
        if (!(isset($data['user_role']) && $data['user_role'] == 'request') && !($fre_id != null && $fre_id != "" && $freelancer['user_id'] == $user_id)) {
            return response()->json(array('success' => false, 'error' => "Invalid Freelancer Id!"));

            // update the freelancer timezones.
            $free_timezones = $data['fre_timezones'];
            // delete the old ones.
            FreelancerTimezone::where('fre_id', $freelancer->fre_id)->delete();

            if ($free_timezones != null) {
                foreach ($free_timezones as $fre_timezone_id) {
                    // add the this timezone.
                    $free_timezone_obj = new FreelancerTimezone();
                    $free_timezone_obj->timezone_id = $fre_timezone_id;
                    $free_timezone_obj->fre_id = $freelancer->fre_id;
                    $free_timezone_obj->fre_timezone_id = (string) Str::uuid();
                    $free_timezone_obj->save();
                }
            }
        }

        //send email to clients
        if (isset($data['user_role']) && $data['user_role'] == 'request') {
            $email_obj = new EmailController();
            $email_obj->rate_send_email($fre_id, $data['able_rate']);
        }

        // update the freelancer info.
        $freelancer->fre_full_name = isset($data['fre_full_name']) ? $data['fre_full_name'] : $freelancer->fre_full_name;
        $freelancer->fre_en_name = isset($data['fre_en_name']) ? $data['fre_en_name'] : $freelancer->fre_en_name;
        $freelancer->fre_phone = isset($data['fre_phone']) ? $data['fre_phone'] : $freelancer->fre_phone;
        $freelancer->fre_skype_id = isset($data['fre_skype_id']) ? $data['fre_skype_id'] : $freelancer->fre_skype_id;
        $freelancer->fre_short_desc = isset($data['fre_short_desc']) ? $data['fre_short_desc'] : $freelancer->fre_short_desc;
        $freelancer->fre_show_en_name = isset($data['fre_show_en_name']) ? $data['fre_show_en_name'] : $freelancer->fre_show_en_name;
        $freelancer->fre_rate_req_status = isset($data['fre_rate_req_status']) ? $data['fre_rate_req_status'] : $freelancer->fre_rate_req_status;
        $freelancer->fre_rate = isset($data['fre_rate']) ? $data['fre_rate'] : $freelancer->fre_rate;

        
        // update payment email.
        $freelancer->fre_payment_email = isset($data['fre_payment_email']) ? $data['fre_payment_email'] : $freelancer->fre_payment_email;

        $freelancer->save();

        return response()->json(array('success' => true, 'user_role' => isset($data['user_role']) ? $data['user_role'] : ''));

    }

    /**
     * store image on Freelancer Photo.
     */
    public function updatePhoto(Request $request)
    {
        $userid = Auth::user()->user_id;

        $freelancer = Freelancer::where('user_id', $userid)->first();

        if (!isset($freelancer->fre_id)) {
            return response()->json(['success' => false, 'message' => 'upload Failed!']);
        }

        $upload_path = public_path('uploads/avatar');
        $file_name = $request->file->getClientOriginalName();
        $generated_new_name = 'free_avatar_' . time() . '.' . $request->file->getClientOriginalExtension();

        // file move from tmp to the new filename.
        $request->file->move($upload_path, $generated_new_name);

        // image resize part.
        // $imageSource= $request->file;
        // $ext = $request->file->getClientOriginalExtension();
        // $resizedWidth = 671;
        // $resizedHeight = 462;
        // $imageQuality = 90;
        // $img = $this->resize_image($imageSource, $ext, $resizedWidth, $resizedHeight);
        // $location = $upload_path . '/' .$generated_new_name;
        // if($ext == 'jpg' or $ext == 'jpeg'){
        //     imagejpeg($img, $location, $imageQuality);
        // }else if($ext == 'png'){
        //     imagepng($img, $location, $imageQuality);
        // }else if($ext == 'gif'){
        //     imagegif($img, $location, $imageQuality);
        // }

        // save the file avatar url.
        $avatar = '/uploads/avatar/' . $generated_new_name;
        $freelancer->fre_avatar = $avatar;
        $freelancer->save();

        return response()->json(['success' => true,
            'message' => 'You have successfully uploaded "' . $file_name . '"',
            'fre_avatar' => $avatar,
        ]);

    }

    //Generic Method for resize Image
    public function resize_image($file, $ext, $dw, $dh)
    {
        list($width, $height) = getimagesize($file);
        //aspect ratio
        $r = $width / $height;

        if ($dw / $dh > $r) {
            $newwidth = $dh * $r;
            $newheight = $dh;
        } else {
            $newheight = $dw / $r;
            $newwidth = $dw;
        }

        if ($ext == 'jpg' or $ext == 'jpeg') {
            $src = imagecreatefromjpeg($file);
        } else if ($ext == 'png') {
            $src = imagecreatefrompng($file);
        } else if ($ext == 'gif') {
            $src = imagecreatefromgif($file);
        }

        //set new width and new hieght
        $dst = imagecreatetruecolor($newwidth, $newheight);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

    /**
     * UPdate password
     */
    public function updatePassword(Request $request)
    {
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
            if ($user) {
                $user->user_password = Hash::make($new_pass);
                $user->save();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid User!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid Current Password!']);
        }
    }

    public function get_fre_profit($fre_id)
    {
        try {
            $channel = Channel::where('fre_id', $fre_id)->get();

            $channelIds = [];
            foreach ($channel as $key => $value) {
                $channelIds[] = $value->channel_id;
            }

            $contracts_all_id = Contract::select('contract_id')
                ->whereIn('channel_id', $channelIds)->get();

            $invoice_obj = Invoice::select(DB::raw('SUM(inv_total) as total_profit'))
                ->whereIn('contract_id', $contracts_all_id)
                ->where('inv_status', 'PAID')
                ->get();
            return $invoice_obj[0]['total_profit'];

        } catch (\Exception $ex) {}
    }

    public function search_freelancer(Request $req)
    {
        try {
            $current_page = $req->input('page');

            $freelancer_info = Freelancer::where('fre_rate_req_status', 1)
            // ->orderByRaw('fre_rate DESC')
                ->get();
            foreach ($freelancer_info as $key => $value) {
                $value['total_profit'] = $this->get_fre_profit($value->fre_id) ?? 0;
            }

            $total = [];
            $total_count = 0;
            $page_index = 1;

            if (count($freelancer_info)) {
                foreach ($freelancer_info as $fre) {
                    $total_count++;
                    $total[$page_index][] = $fre;
                    if (count($total[$page_index]) == 10) {
                        $page_index++;
                    }
                }
            }

            return response()->json(array('status' => true, 'data' => isset($total[$current_page]) ? $total[$current_page] : [], 'total' => $total_count));
        } catch (\Exception $ex) {
            dd('error', $ex);
        }
    }
}
