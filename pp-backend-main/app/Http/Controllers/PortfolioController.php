<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\HashTag;
use App\Models\PortfolioHashTag;
use App\Models\Freelancer;
use App\Models\Contract;
use App\Models\Review;
use App\Models\TimeTrack;
use App\Models\Channel;
use App\Models\Invoice;
use App\Classes\UserControlClass;

use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Foreach_;

class PortfolioController extends Controller
{
    //
    public function index() {
        return response()->json(Portfolio::with(['freelancer'])->paginate(10)->toArray());
    }

    /**
     * Get portfolios with filter & pagination & orderby
     */
    public function search(Request $req) {
        try {
            // get parameters
            $keys = $req->input('key');
            $current_page = $req->input('page');
            $tab = $req->input('tab');
            $order = $req->input('order');
            $orderby = '';
            // Query start here
            $review_sql = Review::join('contracts', 'contracts.contract_id', 'reviews.contract_id')
                                ->join('channels', 'channels.channel_id', 'contracts.channel_id')->select(DB::raw('AVG(reviews.review_rating)'))->toSql();

            $hashtags = PortfolioHashTag::leftjoin('hashtags', 'portfolio_hashtags.hashtag_id', 'hashtags.hashtag_id')
                                            ->leftjoin('portfolios', 'portfolio_hashtags.id', 'portfolios.id')
                                            ->select('portfolios.*', DB::raw('coalesce((' . $review_sql . ' WHERE channels.por_id=portfolios.id AND portfolios.fre_id!=reviews.author_id), 0) as review_rating'));

            // key compare with LIKE
            if(count($keys)) {
                foreach($keys as $key) {
                    $needle = strtolower($key['key']);
                    $hashtags = $hashtags->orwhere('hashtags.name', 'like', '%'.trim($needle).'%');

                    if($key['highlight'] == 'primary') {
                        // make order by query
                        $orderby .= 'CASE WHEN name LIKE \'%'.trim($needle).'%\' THEN 0 ELSE 1 END, ';
                    }
                }
            }

            // order by item            
            switch($order) {
                case '':
                    $orderby .= 'portfolios.platform_verified DESC, review_rating DESC, portfolios.helped DESC, portfolios.viewed DESC';
                    break;
                case 'viewed':
                    $orderby .= 'portfolios.viewed DESC';
                    break;
                case 'helpful':
                    $orderby .= 'portfolios.helped DESC';
                    break;
                case 'recent':
                    $orderby .= 'portfolios.created_at DESC';
                    break;
            }
            
           $result = $hashtags->orderByRaw($orderby);

            // connect with the main portfolio and freelancer table
            $result = $hashtags->with(['portfolio'=> function($query) {
                $query->with('freelancer');
            }]);
            // dd('result', $result->toSql());

            // get result
           $result = $result->get()->toArray();

            /***
             * pagination
             * group by port_id
             * save +$20 rate and -$20 rate to the separate array
             */
            $total = [];
            $total_count = 0;
            $page_index = 1;
            $port_ids = [];
            $str_length = 650;
        
            if(count($result)) {
                foreach($result as $port) {
                    $port['portfolio']['review_rating'] = $port['review_rating'];

                    if (strlen($port['portfolio']['desc']) > $str_length) {
                        $port['portfolio']['desc'] = substr($port['portfolio']['desc'], 0, $str_length) . ' ...';
                    }

                    if(!in_array($port['id'], $port_ids)) {
                        $port_ids[] = $port['id'];
                        if($tab == 0) {
                            // check freelancer rate
                            if( isset($port['portfolio']['freelancer']) && $port['portfolio']['freelancer']['fre_rate'] >= 20 ) {
                                $total_count++;
                                $total[$page_index][] = $port['portfolio'];
                                if(count($total[$page_index]) == 10) {
                                    $page_index++;
                                }
                            }
                        }
                        else if($tab == 1) {
                            if( isset($port['portfolio']['freelancer']) && $port['portfolio']['freelancer']['fre_rate'] < 20 ){
                                $total_count++;
                                $total[$page_index][] = $port['portfolio'];
                                if(count($total[$page_index]) == 10) {
                                    $page_index++;
                                }
                            }
                        }
                        else if($tab == 2) { // pending verification
                            if( isset($port['portfolio']['platform_verified']) && $port['portfolio']['platform_verified'] == 1 ){
                                $total_count++;
                                $total[$page_index][] = $port['portfolio'];
                                if(count($total[$page_index]) == 10) {
                                    $page_index++;
                                }
                            }
                        }
                    }
                }
            }
            
            return response()->json(array('status'=>true, 'data'=>isset($total[$current_page]) ? $total[$current_page]:[], 'total'=>$total_count));

        } catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /***
     * Get by Freelancer ID and type
     */
    public function getByFreID(Request $req) {
        try {
            // get parameters
            $current_page = $req->input('page');
            $tab = trim($req->input('tab'));
            $fre_id = trim($req->input('fre_id'));

            if(!$tab || !$fre_id) {
                throw new \Exception('Invalid Data');
            }
            
            $result = Portfolio::with(['freelancer'])->where('status', $tab)->where('fre_id', $fre_id)->orderby('created_at')->paginate(10)->toArray();

            return response()->json(array('status'=>true, 'data'=> $result['data'], 'total'=>$result['total']));

        } catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
     * Get single portfolio by ID
     */
    public function getPortfolioByID($id) {

        
        try {
            if(!$id) {
                throw new \Exception('Invalid Data');
            }
            
            $portfolio = Portfolio::where("id", $id)
            ->with(["freelancer" => function($query) {
                $query->with(["english_level", "freelancer_timezone" => function($query) {
                    $query->with(["timezone"]);
                }]);
            }, 
            "portfolio_images", 
            "portfolio_hashtags" => function($query) {
                $query->with(["hashtag"]);
            }])
            ->first();
            
            if(!$portfolio) {
                throw new \Exception('Invalid Portfolio ID');
            }
            
            $relevant_portfolios = 
            Portfolio::where("fre_id", $portfolio->fre_id)
            ->where('id', '!=', $id)
            ->with(["freelancer"])
            ->take(5)->get();
            
            $str_length = 400;

            foreach ($relevant_portfolios as $port) {
                if (strlen($port['desc']) > $str_length) {
                    $port['desc'] = substr($port['desc'], 0, $str_length) . ' ...';
                }
            }

            $channels = Channel::where('por_id', $id)->get();
            
            $channelIds = [];
            foreach ($channels as $key => $channel) {
                $channelIds[] = $channel->channel_id;
            }

            $contracts = Contract::whereIn('channel_id', $channelIds)
                                ->where('contract_status', 'ended')->get();

            $contractIds = [];
            foreach ($contracts as $key => $contract) {
                $contractIds[] = $contract->contract_id;
            }

            $reviews = Review::whereIn('contract_id', $contractIds)
                                ->where('author_id','<>', $portfolio->fre_id)->get();

            $time_total = TimeTrack::select('contract_id',DB::raw("SUM(trk_total_hrs) as total"))->whereIn('contract_id', $contractIds)
                                ->groupBy('contract_id')->get();

            $invoice_obj = Invoice::where('id', $id)->first();


            foreach ($contracts as $key =>  $contract) {
                $contracts[$key]['total_hours'] = 0;
                foreach ($time_total as $time) {
                    if( $contract['contract_id'] == $time['contract_id']) {
                        $contracts[$key]['total_hours'] = $time['total'];
                        break;
                    }
                }
            }

            $portfolio_details = $portfolio;
            $portfolio_details["other_portfolios"] = $relevant_portfolios;
            $portfolio_details["images"] = [];

            $portfolio_details["contracts"] =  $contracts;
            $portfolio_details["reviews"] =  $reviews;
            $portfolio_details["payment_status"] = isset($invoice_obj->inv_status) ? $invoice_obj->inv_status : null;

            return response()->json(array('status'=>true, 'data'=>$portfolio_details));
        } catch (\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
    * Get Portfolios in verification requested pending
    */
    public function getVerificationPendingPortfolios() {
        try {
            return response()->json(Portfolio::with(['freelancer'])->where('platform_verified', 1)->paginate(10)->toArray());
        } catch (\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
     * Save a portfolio
     */
    public function store(Request $req) {
        try {
            // get data from frontend
            $user = auth()->user();
            $fre_id =               $user->user_id;
            $description =          $req->input('por_desc', '');
            $title =                $req->input('por_title', '');
            $hashtags =             $req->input('por_tags', []);

            // check exception
            if(!$title || !$description || !$fre_id || !count($hashtags)) {
                throw new \Exception('Invalid Data');
            }
            
            // insert a portfolio 
            $portfolio = new Portfolio();
            $portfolio->por_id = (string) Str::uuid();
            $portfolio->fre_id = $fre_id;
            $portfolio->por_title = $title;
            $portfolio->por_desc = $description;
            $portfolio->save();

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
                        // save portfoio_hashtags
                        $port_hash = new PortfolioHashTag();
                        $port_hash->por_hashtag_id = (string) Str::uuid();
                        $port_hash->por_id = $portfolio->por_id;
                        $port_hash->hashtag_id = $hashtag_id;
                        $port_hash->save();
                    }
                  }
                }
            }

            return response()->json(array('status'=>true, 'message'=>'Success', 'data'=> UserControlClass::getUserData(auth()->user()->user_id)));
        }
        catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
     * Update a portfolio
     */
    public function update(Request $req) {
        try {
            $data = $req->all();
            $id = $data['por_id'];
            
            $portfolio = Portfolio::where('por_id', $id)->first();
            
            if(!($id != null && $id != "")){
                return response()->json(array('success' => false, 'error' => "Invalid Freelancer Id!"), 422);
            }

            // update portfolio info
            $portfolio->por_title = isset($data['por_title']) ? $data['por_title'] : $portfolio->por_title;
            $portfolio->por_desc = isset($data['por_desc']) ? $data['por_desc'] : $portfolio->por_desc;
            $portfolio->save();

            // update portfolio hashtags
            // check hashtags
            $hashtags = $data['por_tags'];
            if(count($hashtags)){
                # delete all hashtags with selected portfolio id from the table first 
                PortfolioHashTag::where('por_id', $id)->delete();

                foreach($hashtags as $hashtag) {
                    ## check if the hashtag already is existed in the system or not
                    if($hashtag != '' && $hashtag != null)
                    {
                      $hashtag_validation = HashTag::where('hashtag_name', '=', $hashtag)->first();
                  
                      if($hashtag_validation){ ## in the case of the hashtag is exsited in our system
                          
                          ## Get the hashtag id 
                          $hashtag_id = $hashtag_validation->hashtag_id;

                          ## save portfoio_hashtags
                          $port_hash = new PortfolioHashTag();
                          $port_hash->por_hashtag_id = (string) Str::uuid();
                          $port_hash->por_id = $id;
                          $port_hash->hashtag_id = $hashtag_id;
                          $port_hash->save();
                      }
                    }
                    ## Need to add the image update feature here later...
                }
            }
            return response()->json(array('status'=>true, 'message'=>'Success', 'data'=> UserControlClass::getUserData(auth()->user()->user_id)));
        }
        catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }
  
    /**
     * Status update: active or archive or draft 
     */
    public function archivePortfolio(Request $req) {
        try {
            // get parameters
            $id = $req->input('id');
            $status = trim($req->input('status'));            
            // $platform_verified = trim($req->input('platform_verified'));

            if(!$id || !$status) {
                throw new \Exception('Invalid Data');
            }
            
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->fre_status = $status;
            // $portfolio->fre_platform_verified = $platform_verified;
            $portfolio->save();

            return response()->json(array('status'=>true, 'message'=> 'Success'));

        } catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
     * Portfolio verfication process 
     * #
     * @platform_verified = 0 : no request
     * @platform_verified = 1 : request pending
     * @platform_verified = 2 : verified by admin
     * @platform_verified = 3 : verfication failed by admin
     * 
     */
    public function verficationPortfolio(Request $req) {
        try {
            // get parameters
            $id = $req->input('id');      
            $platform_verified = trim($req->input('platform_verified'));

            if(!$id) {
                throw new \Exception('Invalid Data');
            }
            
            $user_role = Auth::user()->user_role;
            
            if($user_role != 'admin') {
                $userid = Auth::user()->user_id;
                $freelancer = Freelancer::where('user_id', $userid)->first();

                if(empty($freelancer->fre_fre_payment_email)) {
                    return response()->json(array('status'=>false, 'message'=> 'Please verify Paypal address.'), 422);
                }
    
                $portfolio_obj = Portfolio::where('id', $id)->first();

                $items[] = array(
                    "name" => $portfolio_obj->title,
                    "description" => 'payment for portfolio verify',
                    "quantity" => 1,
                    "unit_amount" => array(
                        "currency_code" => env('PAYPAL_CURRENCY') ?? 'USD',
                        "value" => 2
                    ),
                    "tax" => array(
                        "name" => "Paypal + Service fee",
                        "percent" => (env('PAYPAL_FEE') ?? 3)
                    )
                );

                $recipients = array(
                    'fullName' => $freelancer->fre_fre_full_name,
                    'email' => $freelancer->fre_fre_payment_email
                );

                $payment_obj = new PaymentController();
                $result = $payment_obj->sendInvoice($recipients, $items, $portfolio_obj, []);

                if(!$result) {
                    return response()->json(array('status'=>false, 'message'=>'failed'), 422);
                }
            }

            $portfolio = Portfolio::findOrFail($id);
            $portfolio->fre_platform_verified = $platform_verified;
            $portfolio->save();

            return response()->json(array('status'=>true, 'message'=> 'Success'));

        } catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    /**
     * Duplicate a specific portfolio
     */
    public function duplicatePortfolio(Request $req) {
        try {
            // get parameters
            $id = $req->input('id');

            if(!$id) {
                throw new \Exception('Invalid Data');
            }
            
            $source_portfolio = Portfolio::findOrFail($id);
            
            // insert a portfolio 
            $portfolio = new Portfolio();
            $new_id = (string) Str::uuid();
            $portfolio->fre_done_inside_platform = $source_portfolio->done_inside_platform;
            $portfolio->fre_id = $new_id;
            $portfolio->fre_id = $source_portfolio->fre_id;
            $portfolio->por_title = $source_portfolio->title.'_copy';
            $portfolio->por_desc = $source_portfolio->description;
            $portfolio->fre_status = 'draft';
            
            $portfolio->save();

            // get hashtags_portfolios
            $portfolio_hashtags = PortfolioHashTag::where('id', $id)->get()->toArray();
            
            // check hashtags
            if(count($portfolio_hashtags)){
                foreach($portfolio_hashtags as $hashtag) {
                    // save portfoio_hashtags
                    $port_hash = new PortfolioHashTag();
                    $port_hash_id = (string) Str::uuid();
                    $port_hash->hashtag_id = $port_hash_id;
                    $port_hash->id = $new_id;
                    $port_hash->hashtag_id = $hashtag['hashtag_id'];
                    $port_hash->save();
                }
            }

            // get hashtags_portfolios
            $portfolio_images = PortfolioImage::where('id', $id)->get()->toArray();
            // check images
            if(count($portfolio_images)) {
                foreach($portfolio_images as $img) {
                    // save portfoio_images
                    $port_img = new PortfolioImage();
                    $image_id = (string) Str::uuid();
                    $port_img->image_id = $image_id;
                    $port_img->id = $new_id;
                    $port_img->image_url = $img['image_url'];
                    $port_img->save();
                }
            }

            return response()->json(array('status'=>true, 'message'=> 'Successfully duplicated as a draft.'));

        } catch(\Exception $e) {
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()), 422);
        }
    }

    public function delete($id)
    {
      Portfolio::where('por_id', $id)->delete();
      return response()->json(array('status'=>true, 'message'=>'Success', 'data'=> UserControlClass::getUserData(auth()->user()->user_id)));
    }
}