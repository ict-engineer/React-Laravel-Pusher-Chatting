<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * get tickets by user
     * for admin: show all
     * for client/freelancer: show what the user created
     */
    public function index()
    {
        // get loggedin user
        $userid = auth()->user()->user_id;
        $user_role = auth()->user()->user_role;
        try {
            // get tickets
            $tickets = Ticket::select('*');
            if ($user_role !== 'admin') {
                $tickets = $tickets->where('user_id', $userid);
            }

            $tickets = $tickets->orderby('updated_at', 'DESC')->orderby('created_at', 'DESC')->get()->toArray();

            return response()->json(array('status' => false, 'data' => $tickets));
        } catch (\Exception $e) {
          return response()->json(array('status' => false, 'message' => $e->getMessage()), 422);
        }
    }

    /**
     * get tickets_datails by ticket_id
     */
    public function getTicketDetails($id)
    {
        try {
            $userid = auth()->user()->user_id;

            $user= auth()->user();
            $user_role = $user->user_role;

            $ticket_id = $id;

            // TicketDetail::where('ticket_id', $ticket_id)
            //     ->where('is_read', 0)
            //     ->where('user_id', '<>', $userid)
            //     ->update(['is_read' => 1]);

            $ticket_obj = Ticket::where('ticket_id', $ticket_id)->first();
            $user_obj = user::where('user_id', $ticket_obj->user_id)->first();
            $ticket_msg = [];

            if ($user_obj->user_role == 'freelancer') {
                $free_info = freelancer::where('user_id', $user_obj->user_id)->first();
                $name = $free_info->fre_full_name;
            } else if ($user_obj->user_role == 'client') {
                $clt_info = client::where('user_id', $user_obj->user_id)->first();
                $name = $clt_info->clt_full_name;
            } else {
                $name = 'admin';
            }

            // if ($user_obj->user_role == 'freelancer') {
            //     $ticket_msg = TicketDetail::leftjoin('freelancers', 'freelancers.user_id', 'ticket_details.user_id')
            //         ->where('ticket_details.ticket_id', $ticket_id)
            //         ->select('ticket_details.*', 'freelancers.fre_full_name as name', 'freelancers.fre_avatar as avatar')
            //         ->orderBy('ticket_details.created_at', 'asc')
            //         ->get();
            // } else if ($user_obj->user_role == 'client') {
            //     $ticket_msg = TicketDetail::leftjoin('clients', 'clients.user_id', 'ticket_details.user_id')
            //         ->where('ticket_details.ticket_id', $ticket_id)
            //         ->select('ticket_details.*', 'clients.clt_full_name as name', 'clients.clt_avatar as avatar')
            //         ->orderBy('ticket_details.created_at', 'asc')
            //         ->get();
            // } else {
            //     $ticket_msg = TicketDetail::leftjoin('users', 'users.user_id', 'ticket_details.user_id')
            //         ->where('ticket_details.ticket_id', $ticket_id)
            //         ->orderBy('ticket_details.created_at', 'asc')
            //         ->get();
            // }

            $ticket_details = TicketDetail::where('ticket_id', $ticket_id)->orderBy('created_at', 'asc')->get();
            $details = [];
            foreach($ticket_details as $detail)
            {
              $detail_user = User::where('user_id', $detail->user_id)->first();
              if($detail_user->user_role == "freelancer")
              {
                $freelancer = Freelancer::where('user_id', $detail->user_id)->first();
                $detail['sender'] = $freelancer->fre_full_name;
                $detail['avatar'] = $freelancer->fre_avatar;
              }
              else if($detail_user->user_role == "client")
              {

              }
              else{
                $detail['sender'] = 'Admin';
                $detail['avatar'] = "";
              }
              array_push($details, $detail);
            }

            $ticket_obj['name'] = $name;
            $ticket_obj['user_role'] = $user_role;

            $result = [];
            $result['main_info'] = $ticket_obj;
            $result['history'] = $details;

            return response()->json(array('status' => true, 'data' => $result));
        } catch (\Exception $e) {
          return response()->json(array('status' => false, 'message' => $e->getMessage()), 422);
        }
    }

    /**
     * create new ticket
     * info needing: the title, description for ticket.
     */

    public function store(Request $request)
    {
        try {
            $userid = auth()->user()->user_id;
            $ticket_title = $request->input('ticket_title');
            $ticket_status = $request->input('ticket_status');
            $ticket_description = $request->input('ticket_description');

            $ticket_obj = new Ticket();
            $ticket_obj->ticket_id = (string) Str::uuid();
            $ticket_obj->user_id = $userid;
            $ticket_obj->ticket_title = $ticket_title;
            $ticket_obj->ticket_status = $ticket_status;
            $ticket_obj->ticket_description = $ticket_description;
            $ticket_obj->save();

            return response()->json(array('status' => true, 'message' => 'Successfully Opened Ticket', 'data'=>$ticket_obj));

        } catch (\Exception $e) {
            return response()->json(array('status' => false, 'message' => 'Fail Opened Ticket'), 422);
        }
    }

    /**
     * create ticket_message by ticket_id.
     * info needing: ticket_id, new message data, ticket status(closed or open).
     */
    public function update(Request $request)
    {
        try {
            $userid = auth()->user()->user_id;
            $ticket_id = $request->input('ticket_id');
            $ticket_msg = $request->input('message');
            $ticket_status = $request->input('ticket_status');

            $ticket_details_obj = new TicketDetail();
            $ticket_details_obj->ticket_dtl_id = (string) Str::uuid();
            $ticket_details_obj->ticket_id = $ticket_id;
            $ticket_details_obj->user_id = $userid;
            $ticket_details_obj->ticket_dtl_msg = $ticket_msg;
            $ticket_details_obj->save();

            if(auth()->user()->user_role == "freelancer")
            {
              $freelancer = Freelancer::where('user_id', $userid)->first();
              $ticket_details_obj['sender'] = $freelancer->fre_full_name;
              $ticket_details_obj['avatar'] = $freelancer->fre_avatar;
            }
            else if(auth()->user()->user_role == "client")
            {

            }
            else{
              $ticket_details_obj['sender'] = 'Admin';
              $ticket_details_obj['avatar'] = "";
            }

            Ticket::where('ticket_id', $ticket_id)->update(['ticket_status' => $ticket_status]);

            return response()->json(array('status' => true, 'data' => $ticket_details_obj, 'm'=>"asdfasdf"));

        } catch (\Exception $e) {
            return response()->json(array('status' => false, 'message' => $e->getMessage()), 422);
        }
    }

    public function update_tickets(Request $request)
    {
        try {

        } catch (\Exception $e) {}
    }
   
}
