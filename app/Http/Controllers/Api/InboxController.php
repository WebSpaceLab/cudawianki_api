<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InboxResource;
use App\Models\Inbox;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class InboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $mails = Inbox::all()
                ->orderBy(request('orderBy'), request('orderDir'))
                ->month(request('month'))
                ->read(request('read'))
                ->search(request('term'))
                ->paginate(request('per_page'))
            ;

            $pagination = [
                'total' => $mails->total(),
                'per_page' => $mails->perPage(),
                'current_page' => $mails->currentPage()
            ];

            $inboxResource = InboxResource::collection( $mails);

            $read = Inbox::selectRaw('distinct is_read')
                ->get()
                ->map(function ($item, $key) {
                    return [
                        'value' => $item->is_read,
                        'label' => $item->is_read ? 'Read' : 'Not read'
                    ];
                })
                ->unique('value')->values();

            $months = DB::table('inboxes')
                ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
                ->orderByDesc('value')
                ->get();

            return response()->json([
                'mails' => $inboxResource,
                'pagination' => $pagination,
                'months' => $months,
                'read' => $read,
                'queryParams' => request()->all(['month', 'term', 'read', 'orderBy', 'orderDir'])
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'subject' => ['string', 'required'],
            'sender' => ['string', 'required'],
            'email' => ['string', 'required'],
            'phone' => ['string', 'required'],
            'content' => ['string', 'required'],
        ]);

        Inbox::create([
            'user_id' => 1,
            'subject' => request('subject'),
            'sender' => request('sender'),
            'email' => request('email'),
            'phone' => request('phone'),
            'content' => request('content'),
            'is_read' => 0,
        ]);

        return response()->json([
            'flash' => [
                'message' => 'Wiadomość została wysłana.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inbox $inbox)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inbox $inbox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function read(Inbox $mail)
    {
        $mail->update([
            'is_read' => 1
        ]);

        return response()->json(['mail' => $mail], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        request()->validate([
            'mailIds' => ['required', 'array']
        ]);


        foreach (Inbox::find(request('mailIds')) as $mail) {
            $mail->delete();
        }

        return response()->json([
            'succeed' =>  $mail,
            'flash' => [
                'message' => 'The category has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }
}
