<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\TicketDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Tickets\StoreTicket;
use App\Http\Requests\Tickets\UpdateTicket;
use App\Ticket;
use App\TicketChannel;
use App\TicketFile;
use App\TicketGroup;
use App\TicketReply;
use App\TicketReplyTemplate;
use App\TicketTag;
use App\TicketTagList;
use App\TicketType;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManageTicketsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tickets';
        $this->pageIcon = 'ti-ticket';
        $this->middleware(function ($request, $next) {
            if (!in_array('tickets', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(TicketDataTable $dataTable)
    {
        $startDate = Carbon::now()->subWeek(1)->timezone($this->global->timezone);
        $endDate   = Carbon::now()->timezone($this->global->timezone);

        $this->startDate = $startDate->format('m/d/Y');
        $this->endDate   = $endDate->format('m/d/Y');
        $this->channels  = TicketChannel::all();
        $this->groups    = TicketGroup::all();
        $this->types     = TicketType::all();

        $this->chartData = $this->getGraphData($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        return $dataTable->render('admin.tickets.index', $this->data);
    }

    public function getGraphData($fromDate, $toDate, $agentId = null, $status = null, $priority = null, $channelId = null, $typeId = null)
    {
        $graphData = [];

        $total = [];
        $totalTickets = Ticket::where(DB::raw('DATE(`created_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`created_at`)'), '<=', $toDate)
            ->groupBy('created_at')
            ->orderBy('created_at', 'ASC');

        if ($agentId != 0) {
            $totalTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $totalTickets->where('status', '=', $status);
        }

        if ($priority) {
            $totalTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $totalTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $totalTickets->where('type_id', '=', $typeId);
        }

        $totalTickets = $totalTickets->get([
            DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as date'),
            DB::raw('count(id) as total')
        ]);

        foreach ($totalTickets as $ticket) {
            if (!isset($total[$ticket->date])) {
                $total[$ticket->date] = 0;
            }
            $total[$ticket->date] += $ticket->total;
        }

        $resolved = [];
        $resolvedTickets = Ticket::where(DB::raw('DATE(`updated_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $toDate)
            ->where(function ($query) {
                $query->where('status', 'closed');
                $query->orWhere('status', 'resolved');
            })
            ->groupBy('updated_at')
            ->orderBy('updated_at', 'ASC');

        if ($agentId != 0) {
            $resolvedTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $resolvedTickets->where('status', '=', $status);
        }

        if ($priority) {
            $resolvedTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $resolvedTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $resolvedTickets->where('type_id', '=', $typeId);
        }

        $resolvedTickets = $resolvedTickets->get([
            DB::raw('DATE_FORMAT(updated_at,"%Y-%m-%d") as date'),
            DB::raw('count(id) as resolved')
        ]);

        foreach ($resolvedTickets as $ticket) {
            if (!isset($resolved[$ticket->date])) {
                $resolved[$ticket->date] = 0;
            }
            $resolved[$ticket->date] += $ticket->resolved;
        }

        $unresolved = [];
        $unresolvedTickets = Ticket::where(DB::raw('DATE(`updated_at`)'), '>=', $fromDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $toDate)
            ->where(function ($query) {
                $query->where('status', '<>', 'closed');
                $query->where('status', '<>', 'resolved');
            })
            ->groupBy('updated_at')
            ->orderBy('updated_at', 'ASC');

        if ($agentId != 0) {
            $unresolvedTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $unresolvedTickets->where('status', '=', $status);
        }

        if ($priority) {
            $unresolvedTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $unresolvedTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $unresolvedTickets->where('type_id', '=', $typeId);
        }

        $unresolvedTickets = $unresolvedTickets->get([
            DB::raw('DATE_FORMAT(updated_at,"%Y-%m-%d") as date'),
            DB::raw('count(id) as unresolved')
        ]);

        foreach ($unresolvedTickets as $ticket) {
            if (!isset($unresolved[$ticket->date])) {
                $unresolved[$ticket->date] = 0;
            }
            $unresolved[$ticket->date] += $ticket->unresolved;
        }

        $dates = array_keys(array_merge(array_merge($total, $resolved), $unresolved));

        foreach ($dates as $date) {
            $graphData[] = [
                'date' =>  $date,
                'total' =>  isset($total[$date]) ? $total[$date] : 0,
                'resolved' =>  isset($resolved[$date]) ? $resolved[$date] : 0,
                'unresolved' =>  isset($unresolved[$date]) ? $unresolved[$date] : 0
            ];
        }

        usort($graphData, function ($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            return $t1 - $t2;
        });

        return $graphData;
    }


    public function create()
    {
        $this->groups = TicketGroup::all();
        $this->types = TicketType::all();
        $this->channels = TicketChannel::all();
        $this->templates = TicketReplyTemplate::all();
        $this->requesters = User::all();
        $this->lastTicket = Ticket::orderBy('id', 'desc')->first();
        return view('admin.tickets.create', $this->data);
    }

    public function store(StoreTicket $request)
    {
        $ticket = new Ticket();
        $ticket->subject = $request->subject;
        $ticket->status = $request->status;
        $ticket->user_id = $request->user_id;
        $ticket->agent_id = $request->agent_id;
        $ticket->type_id = $request->type_id;
        $ticket->priority = $request->priority;
        $ticket->channel_id = $request->channel_id;
        $ticket->save();

        //save first message
        $reply = new TicketReply();
        $reply->message = $request->description;
        $reply->ticket_id = $ticket->id;
        $reply->user_id = $this->user->id; //current logged in user
        $reply->save();

        // save tags
        $tags = $request->tags;

        if ($tags) {
            TicketTag::where('ticket_id', $ticket->id)->delete();
            foreach ($tags as $tag) {
                $tag = TicketTagList::firstOrCreate([
                    'tag_name' => $tag
                ]);


                TicketTag::create([
                    'tag_id' => $tag->id,
                    'ticket_id' => $ticket->id
                ]);
            }
        }

        //log search
        $this->logSearchEntry($ticket->id, 'Ticket: ' . $ticket->subject, 'admin.tickets.edit', 'ticket');

        return Reply::redirect(route('admin.tickets.index'), __('messages.ticketAddSuccess'));

        //        return Reply::redirect(route('admin.tickets.index'), __('messages.ticketAddSuccess'));
    }

    public function edit($id)
    {
        $this->ticket = Ticket::findOrFail($id);
        $this->groups = TicketGroup::all();
        $this->types = TicketType::all();
        $this->channels = TicketChannel::all();
        $this->templates = TicketReplyTemplate::all();
        return view('admin.tickets.edit', $this->data);
    }

    public function update(UpdateTicket $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->status;
        // $ticket->agent_id = $request->agent_id;
        // $ticket->type_id = $request->type_id;
        // $ticket->priority = $request->priority;
        // $ticket->channel_id = $request->channel_id;
        $ticket->save();

        $lastMessage = null;

        if ($request->message != '') {
            //save first message
            $reply = new TicketReply();
            $reply->message = $request->message;
            $reply->ticket_id = $ticket->id;
            $reply->user_id = $this->user->id; //current logged in user
            $reply->save();

            $this->fileSave($request, $reply->id);
            // save tags
            // $tags = $request->tags;
            // if ($tags) {
            //     TicketTag::where('ticket_id', $ticket->id)->delete();

            //     foreach ($tags as $tag) {
            //         $tag = TicketTagList::firstOrCreate([
            //             'tag_name' => $tag
            //         ]);


            //         TicketTag::create([
            //             'tag_id' => $tag->id,
            //             'ticket_id' => $ticket->id
            //         ]);
            //     }
            // }

            $global = $this->global;

            $lastMessage = view('admin.tickets.last-message', compact('reply', 'global'))->render();
        }


        return Reply::successWithData(__('messages.ticketReplySuccess'), ['lastMessage' => $lastMessage]);
    }

    public function updateOtherData(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->agent_id = $request->agent_id;
        $ticket->type_id = $request->type_id;
        $ticket->priority = $request->priority;
        $ticket->channel_id = $request->channel_id;
        $ticket->save();

        $tags = $request->tags;
        if ($tags) {
            TicketTag::where('ticket_id', $ticket->id)->delete();

            foreach ($tags as $tag) {
                $tag = TicketTagList::firstOrCreate([
                    'tag_name' => $tag
                ]);


                TicketTag::create([
                    'tag_id' => $tag->id,
                    'ticket_id' => $ticket->id
                ]);
            }
        }
        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy($id)
    {
        Ticket::destroy($id);
        return Reply::success(__('messages.ticketDeleteSuccess'));
    }

    public function destroyReply($id)
    {
        $ticketFiles = TicketFile::where('ticket_reply_id', $id)->get();
        foreach ($ticketFiles as $file) {
            Files::deleteFile($file->hashname, 'user-uploads/ticket-files/' . $file->ticket_reply_id);
            $file->delete();
        }
        TicketReply::destroy($id);
        return Reply::success(__('messages.ticketDeleteSuccess'));
    }

    public function refreshCount($startDate = null, $endDate = null, $agentId = null, $status = null, $priority = null, $channelId = null, $typeId = null)
    {
        $openTickets = Ticket::select(DB::raw('count(`id`) as total'))
            ->where('status', 'open')
            ->where(DB::raw('DATE(`updated_at`)'), '>=', $startDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $endDate);

        if ($agentId != 0) {
            $openTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $openTickets->where('status', '=', $status);
        }

        if ($priority) {
            $openTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $openTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $openTickets->where('type_id', '=', $typeId);
        }

        $openTickets = $openTickets->first();

        $pendingTickets = Ticket::select(DB::raw('count(`id`) as total'))
            ->where('status', 'pending')
            ->where(DB::raw('DATE(`updated_at`)'), '>=', $startDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $endDate);

        if ($agentId != 0) {
            $pendingTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $pendingTickets->where('status', '=', $status);
        }

        if ($priority) {
            $pendingTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $pendingTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $pendingTickets->where('type_id', '=', $typeId);
        }

        $pendingTickets = $pendingTickets->first();

        $resolvedTickets = Ticket::select(DB::raw('count(`id`) as total'))
            ->where('status', 'resolved')
            ->where(DB::raw('DATE(`updated_at`)'), '>=', $startDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $endDate);

        if ($agentId != 0) {
            $resolvedTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $resolvedTickets->where('status', '=', $status);
        }

        if ($priority) {
            $resolvedTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $resolvedTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $resolvedTickets->where('type_id', '=', $typeId);
        }

        $resolvedTickets = $resolvedTickets->first();

        $closedTickets = Ticket::select(DB::raw('count(`id`) as total'))
            ->where('status', 'closed')
            ->where(DB::raw('DATE(`updated_at`)'), '>=', $startDate)
            ->where(DB::raw('DATE(`updated_at`)'), '<=', $endDate);

        if ($agentId != 0) {
            $closedTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $closedTickets->where('status', '=', $status);
        }

        if ($priority) {
            $closedTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $closedTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $closedTickets->where('type_id', '=', $typeId);
        }

        $closedTickets = $closedTickets->first();

        $totalTickets = Ticket::select(DB::raw('count(`id`) as total'))
            ->where(DB::raw('DATE(`created_at`)'), '>=', $startDate)
            ->where(DB::raw('DATE(`created_at`)'), '<=', $endDate);

        if ($agentId != 0) {
            $totalTickets->where('agent_id', '=', $agentId);
        }

        if ($status) {
            $totalTickets->where('status', '=', $status);
        }

        if ($priority) {
            $totalTickets->where('priority', '=', $priority);
        }

        if ($channelId != 0) {
            $totalTickets->where('channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $totalTickets->where('type_id', '=', $typeId);
        }

        $totalTickets = $totalTickets->first();

        $chartData = $this->getGraphData($startDate, $endDate, $agentId, $status, $priority, $channelId, $typeId);

        $chartData = json_encode($chartData);

        return Reply::dataOnly(['chartData' => $chartData, 'totalTickets' => $totalTickets->total, 'closedTickets' => $closedTickets->total, 'openTickets' => $openTickets->total, 'pendingTickets' => $pendingTickets->total, 'resolvedTickets' => $resolvedTickets->total]);
    }

    public function export($startDate = null, $endDate = null, $agentId = null, $status = null, $priority = null, $channelId = null, $typeId = null)
    {

        $tickets = Ticket::join('users', 'users.id', 'tickets.user_id')
            ->select('tickets.id', 'tickets.subject', 'users.name', 'tickets.created_at', 'tickets.status');

        if ($startDate != 0) {
            $tickets->where(DB::raw('DATE(tickets.created_at)'), '>=', $startDate);
        }

        if ($endDate != 0) {
            $tickets->where(DB::raw('DATE(tickets.created_at)'), '<=', $endDate);
        }

        if ($agentId != 0) {
            $tickets->where('tickets.agent_id', '=', $agentId);
        }

        if ($status) {
            $tickets->where('tickets.status', '=', $status);
        }

        if ($priority) {
            $tickets->where('tickets.priority', '=', $priority);
        }

        if ($channelId != 0) {
            $tickets->where('tickets.channel_id', '=', $channelId);
        }

        if ($typeId != 0) {
            $tickets->where('tickets.type_id', '=', $typeId);
        }

        $attributes =  ['created_at'];

        $tickets = $tickets->get()->makeHidden($attributes);

        // Initialize the array which will be passed into the Excel
        // generator.
        $exportArray = [];

        // Define the Excel spreadsheet headers
        $exportArray[] = ['ID', 'Subject', 'Requested Name', 'Status', 'Requested On'];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        foreach ($tickets as $row) {
            $exportArray[] = $row->toArray();
        }

        // Generate and return the spreadsheet
        Excel::create('Ticket', function ($excel) use ($exportArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Ticket');
            $excel->setCreator('Worksuite')->setCompany($this->companyName);
            $excel->setDescription('Ticket file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($exportArray) {
                $sheet->fromArray($exportArray, null, 'A1', false, false);

                $sheet->row(1, function ($row) {

                    // call row manipulation methods
                    $row->setFont(array(
                        'bold'       =>  true
                    ));
                });
            });
        })->download('xlsx');
    }

    public function fileSave($request, $ticketReplyID)
    {
        if ($request->hasFile('file')) {
            foreach ($request->file as $fileData) {
                $file = new TicketFile();
                $file->user_id = $this->user->id;
                $file->ticket_reply_id = $ticketReplyID;
                $filename = Files::uploadLocalOrS3($fileData,'ticket-files/'.$ticketReplyID);
                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;

                $file->size = $fileData->getSize();
                $file->save();
            }
        }
    }
}
