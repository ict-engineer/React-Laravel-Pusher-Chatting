<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Models\Contract;
use App\Models\TimeTrack;
use App\Models\Freelancer;
use App\Models\Invoice;
use App\Models\Channel;
use App\Models\Client;
use App\Models\InvoiceDetail;
use App\Models\Payout;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public $paypal_access_token = '';
    //Get Token
    public function get_paypal_token() {

        try {
            $ch = curl_init();
    
            curl_setopt($ch, CURLOPT_URL, env('PAYPAL_URL')."/v1/oauth2/token");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSLVERSION , 6);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') .":". env('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    
            $result = curl_exec($ch);
            curl_close($ch);
            $this->paypal_access_token = json_decode($result)->access_token;

            return true;

        }catch(\Exception $ex) {
            return false;
        }
    }

    //Create Invoice
    public function createInvoice($invoicer, $recipients, $items) {
        try {
            if( !$this->paypal_access_token )
                $this->get_paypal_token();
    
            $invoice_number = count(Invoice::select()->get()) + 1;
    
            if( $invoice_number > 1 ) {
                try {
                    //code...
                    $ch = curl_init(env('PAYPAL_URL').'/v2/invoicing/generate-next-invoice-number'); 
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $result = curl_exec($ch);
    
                    $invoice_number = json_decode($result)->invoice_number;
                } catch (\Throwable $th) {
                    dd('error', $th);
                }
            } else  {
                $invoice_number = 'INVOICE-0001';
            }

            $ch = curl_init(env('PAYPAL_URL').'/v2/invoicing/invoices'); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            $fields = array(
                "detail" => array(
                    "invoice_number" => $invoice_number,
                    "currency_code" => env('PAYPAL_CURRENCY') ?? 'USD',
                    "payment_term" => array(
                        "due_date" => date('Y-m-d', strtotime(date('Y-m-d'). ' + '. (env('DELAY_DAYS') ?? 3) .' days'))
                    )
                ),
                "invoicer" => array(
                    "name" => array (
                        "firstName" => $invoicer['firstName'],
                        "lastName" => $invoicer['lastName']
                    ),
                    "email_address" => $invoicer['email']
                ),
                "primary_recipients" => [
                    array(
                        "billing_info" => array(
                            "name" => array (
                                "firstName" => $recipients['firstName'],
                                "lastName" => $recipients['lastName']
                            ),
                            "email_address" => $recipients['email'] 
                        )
                    )
                ],
                "items" => $items,
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_POST, 1);
    
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
            $result = curl_exec($ch);
    
            if (curl_errno($ch)) {
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            return  json_decode($result)->href;

        }catch(\Exception $ex) {
            dd('create_invoice', $ex);
        }
    }
    
    //Send Invoice
    public function sendInvoice($recipients_obj, $items, $special_obj, $time_records) {

        try {
            if( !$this->paypal_access_token )
                $this->get_paypal_token();
    
            $invoicer['firstName'] = env('PAYPAL_ADMIN_FIRSTNAME');
            $invoicer['lastName'] = env('PAYPAL_ADMIN_LASTNAME');
            $invoicer['email'] = env('PAYPAL_ADMIN_ADDRESS');    
            $recipients['firstName'] = explode(' ', $recipients_obj['fullName'])[0];
            $recipients['lastName'] = explode(' ', $recipients_obj['fullName'])[1] ?? '';
            $recipients['email'] = $recipients_obj['email'] ?? '';

            $invoice_sendUrl = $this->createInvoice($invoicer, $recipients, $items);
            $ch = curl_init($invoice_sendUrl.'/send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $fields = array(
                "send_to_invoicer" => true
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);
    
            if (curl_errno($ch)) {
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            $result_detail = $this->getInvoiceDetail($invoice_sendUrl);

            try{
                $invoice_obj = new Invoice();
                $invoice_obj->inv_id = $result_detail->id;
                $invoice_obj->inv_status = $result_detail->status;
                $invoice_obj->contract_id = $special_obj->contract_id ?? '';
                $invoice_obj->por_id = $special_obj->por_id ?? '';
                $invoice_obj->inv_fee =  (double)$result_detail->amount->breakdown->tax_total->value;
                $invoice_obj->inv_total = (double)$result_detail->amount->value;
                $invoice_obj->inv_sub_total = (double)$result_detail->amount->breakdown->item_total->value;
                $invoice_obj->inv_type = $special_obj->contract_id ? 2 : 1;
                $invoice_obj->save();

                $invoice_detail_items = $result_detail->items;

                foreach ($invoice_detail_items as $key => $item) {
                    $invoice_detail = new InvoiceDetail();
                    $invoice_detail->inv_dtl_id = $item->id;
                    $invoice_detail->inv_id = $result_detail->id;
                    $invoice_detail->inv_dtl_total_hrs = (double)$item->quantity;
                    $invoice_detail->inv_dtl_hourly_rate = (double)$item->unit_amount->value;
                    $invoice_detail->inv_dtl_date = date('Y-m-d', strtotime($time_records[$key]->trk_date ?? date('Y-m-d')));
                    $invoice_detail->inv_dtl_from = date('H:i:s', strtotime($time_records[$key]->trk_from ?? '00:00:00'));
                    $invoice_detail->inv_dtl_to = date('H:i:s', strtotime($time_records[$key]->trk_to ?? '00:00:00'));
                    $invoice_detail->inv_dtl_is_manual = '';
                    $invoice_detail->save();
                }

            }catch(\Exception $en){
                return false;
                dd('error', $en);
            }

            return true;
        }catch(\Exception $ex) {
            return false;
        }
    }

    //invoice statue
    public function getInvoiceDetail($invoice_sendUrl) {

        try{

            if( !$this->paypal_access_token )
                $this->get_paypal_token();
    
            $ch = curl_init($invoice_sendUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = json_decode(curl_exec($ch));
    
            if (curl_errno($ch)) {
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            return $result;
        }catch(\Exception $ex){
            dd($ex);
        }
    }

    public function sendWeeklyInvoice() {
        try{
            $contract_obj = Contract::where('contract_status', 'accepted')->get();
            foreach ($contract_obj as $contract) {
                $this->_getContractInfo($contract);
            };

        }catch(\Exception $ex) {
            dd('error', $ex);
        }
    }

    public function _getContractInfo($contract_obj) {
        $invoice_obj = Invoice::select(DB::raw("SUM(inv_sub_total) as total"))
                            ->where('contract_id', $contract_obj->contract_id)
                            ->whereIn('inv_status', ['SENT', 'PAID', 'PAYMENT_PENDING', 'PARTIALLY_PAID'])
                            ->groupBy('contract_id')->first();

        $channel_obj = Channel::where('channel_id', $contract_obj->channel_id)->first();
        $client_obj = Client::where('user_id', $channel_obj->clt_id)->first();
        $time_total = TimeTrack::select(DB::raw("SUM(trk_total_hrs) as total"))
                            ->where('contract_id', $contract_obj->contract_id)
                            ->groupBy('contract_id')->first();

        //paypal client 
        $recipients = array(
            'fullName' => $client_obj->clt_full_name ?? "CLIENT",
            'email' => $client_obj->clt_invoice_email
        );

        $hourly_rate = $contract_obj->contract_hourly_rate;
        $tracked_sum = ($time_total->total ?? 0) * $hourly_rate;
        $amount = $tracked_sum - ($invoice_obj->total ?? 0);
       
        if($amount > 0) {
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

            $result = $this->sendInvoice($recipients, $items, $contract_obj, $time_records);
        }
    }

    public function get_paypal_notification() {
        try {
            $post_string = file_get_contents('php://input');
            $post = json_decode($post_string);

            $event_type = $post->event_type;
            
            switch ($event_type) {
                case 'INVOICING.INVOICE.PAID':
                case 'INVOICING.INVOICE.CANCELLED':
                case 'INVOICING.INVOICE.REFUNDED':
                    $inv_id = $post->resource->invoice->id;
                    $status = $post->resource->invoice->status;
                    $invoice = Invoice::where('inv_id', $inv_id)->first();
                    $invoice->inv_status = $status;
                    $invoice->save();
                    break;
                case 'PAYMENT.PAYOUTS-ITEM.SUCCEEDED':
                case 'PAYMENT.PAYOUTS-ITEM.BLOCKED':
                case 'PAYMENT.PAYOUTS-ITEM.CANCELED':
                case 'PAYMENT.PAYOUTS-ITEM.DENIED':
                case 'PAYMENT.PAYOUTS-ITEM.FAILED':
                case 'PAYMENT.PAYOUTS-ITEM.HELD':
                case 'PAYMENT.PAYOUTS-ITEM.REFUNDED':
                case 'PAYMENT.PAYOUTS-ITEM.RETURNED':
                case 'PAYMENT.PAYOUTS-ITEM.SUCCEEDED':
                case 'PAYMENT.PAYOUTS-ITEM.UNCLAIMED':
                    
                    $transaction_status = $post->resource->transaction_status;
                    $payout_item_id = $post->resource->payout_item_id;
                    $payouts_obj = Payout::where('payout_id', $payout_item_id);
                    $payouts_obj->status = $transaction_status;
                    $payouts_obj->save();
                    break;
            }

        } catch (\Exception $ex) 
        {
            dd('paypal_recive_action', $ex);
        }
    }

    public function paypal_reminder(Request $request) {
        $client_id = $request->input('client_id');

        try {
            if( !$this->paypal_access_token )
                $this->get_paypal_token();

            //paypal reminder 
            $channelIds = Channel::select('channel_id')
                    ->where('user_id', $client_id) 
                    ->get();

            $contractIds = Contract::select('contract_id')
                    ->whereIn('channel_id', $channelIds)
                    ->where('contract_status', 'accepted')
                    ->get();
            $invoice = Invoice::select('inv_id', 'created_at')
                    ->whereIn('contract_id', $contractIds)
                    ->where('inv_status', 'SENT')
                    ->get();

            $end_date = date_create(date('Y-m-d'));
            foreach ($invoice as $key => $value) {
                $start_date = date_create(date_format($value->created_at, 'Y-m-d'));
                $diff = date_diff($start_date, $end_date);
                if ($diff->format("%a") >= 3) {
                    $ch = curl_init(env('PAYPAL_URL').'/v2/invoicing/invoices/'.$value->inv_id.'/remind'); 
                    curl_setopt($ch, CURLOPT_POST, 1);
        
                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
                    $result = curl_exec($ch);
        
                    if (curl_errno($ch)) {
                        die('Error:' . curl_error($ch));
                    }
                    curl_close($ch);
                }
            }

        }catch(\Exception $ex) {
            dd('paypal_reminder_error', $ex);
        }
    }

    public function async_call($url, $params = []) {
        $post_string = http_build_query($params);
        $parts = parse_url($url);
        $errno = 0;
        $errstr = "";
        //Use SSL & port 443 for secure servers
        //Use otherwise for localhost and non-secure servers
        //For secure server
        // $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
        //For localhost and un-secure server
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : $_SERVER['SERVER_PORT'], $errno, $errstr, 30);
        
        if(!$fp)
        {
            echo "Some thing Problem";
        }
        
        $out = "POST ". $parts['path'] ." HTTP/1.1\r\n";
        $out.= "Host: ". $parts['host'] . (isset($parts['port']) ? ":". $parts['port'] : '') ."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ". strlen($post_string) ."\r\n";
        $out.= "Connection: Close\r\n\r\n";

        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        
        fclose($fp);
    }

    public function paypal_payouts() {
        try{
            if( !$this->paypal_access_token)
                $this->get_paypal_token();
            
            $payout_inv = Payout::select('inv_id')
                                ->whereIn('status', ['PENDING', 'SUCCESS', 'ONHOLD', 'UNCLAIMED'])
                                ->get();

            //paypal payouts 
            $invoice = DB::table('invoices')
                    ->join('contracts', 'invoices.contract_id', '=', 'contracts.contract_id')
                    ->join('channels', 'contracts.channel_id' , '=', 'channels.channel_id')
                    ->join('freelancers', 'freelancers.fre_id', '=', 'channels.fre_id')
                    ->whereNotIn('invoices.inv_id', $payout_inv)
                    ->where('invoices.inv_status', 'PAID')
                    ->whereDate('invoices.updated_at', '<', date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 2 days')))
                    ->get();

            $ch = curl_init(env('PAYPAL_URL').'/v1/payments/payouts');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $fields = '{
                "sender_batch_header": {
                    "email_subject": "You have a payout!",
                    "email_message": "You have received a payout! Thanks for using our service!"
                },
                "items": [';
            
            foreach ($invoice as $key => $inv_value) {
                $amount = round(($inv_value->inv_total * (1 - env('PAYPAL_FREELANCER_FEE') / 100)) * (1 - env('PAYPAL_FEE')/100), 2);

                if($amount < 20) continue;

                $fields .= '{
                        "recipient_type": "EMAIL",
                        "amount": {
                            "value": "' . $amount . '",
                            "currency": "' . (env('PAYPAL_CURRENCY') ?? 'USD') . '"
                        },
                        "note": "Thanks for your patronage!",
                        "receiver": "' . $inv_value->fre_payment_email . '",
                        "sender_item_id": "' . $inv_value->inv_id . '"
                    },';
            }

            $fields = substr($fields, 0, strlen($fields) - 1 ) . ']}';

            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            $return_obj = json_decode($result);
            $payout_id = $return_obj->batch_header->payout_batch_id;

            $ch = curl_init(env('PAYPAL_URL').'/v1/payments/payouts/' . $payout_id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $payment_result = curl_exec($ch);
            if (curl_errno($ch)) {
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            $payment_return = json_decode($payment_result)->items;

            foreach ($payment_return as $key => $value) {
                $payout_item_id = $value->payout_item_id;
                $transaction_status = $value->transaction_status;
                $sender_item_id = $value->payout_item->sender_item_id;
                $amount = $value->payout_item->amount->value;
                $currency = $value->payout_item->amount->currency;

                $payouts_obj = new Payout();
                $payouts_obj->payout_id = $payout_item_id;
                $payouts_obj->inv_id = $sender_item_id;
                $payouts_obj->amount = $amount;
                $payouts_obj->status = $transaction_status;
                $payouts_obj->currency = $currency;
                $payouts_obj->save();

            }

        }catch(\Exception $ex) {
            dd('payouts', $ex);
        }
    }


    public function cancel_invoice($invoice_id) {
        try{
            if( !$this->paypal_access_token)
                $this->get_paypal_token();

            $ch = curl_init(env('PAYPAL_URL').'/v2/invoicing/invoices/' . $invoice_id . '/cancel'); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer '. $this->paypal_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);
    
            if (curl_errno($ch)) {
                return false;
                die('Error:' . curl_error($ch));
            }
            curl_close($ch);

            $url = env('PAYPAL_URL').'/v2/invoicing/invoices/' . $invoice_id;

            $getResult = $this->getInvoiceDetail($url);

            $invoice = Invoice::where('inv_id', $invoice_id)->first();
            $invoice->inv_status = $getResult->status;;
            $invoice->save();

            return true;
        }catch(\Exception $ex) {
            return false;
        }
    }

    
}

