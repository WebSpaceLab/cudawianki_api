<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = ContactResource::collection(Contact::all());

        return response()->json([ 'contact' => $contact ], Response::HTTP_OK);
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
            'title' => ['string', 'max:255'],
            'description' => ['string', 'nullable'],
            'address' => ['string',  'nullable'],
            'openingHours' => ['string', 'nullable'],
            'phone' => ['string',  'nullable'],
            'map' => ['string', 'nullable']
        ]);

        $contact = Contact::create([
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'openingHours' => $request->openingHours,
            'phone' => $request->phone,
            'map' => $request->map,
        ]);

        return response()->json([
            'data' => $contact,
            'flash' => [
                'message' => 'Data has been added.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        request()->validate([
            'title' => ['string', 'max:255'],
            'description' => ['string', 'nullable'],
            'address' => ['string',  'nullable'],
            'openingHours' => ['string', 'nullable'],
            'phone' => ['string',  'nullable'],
            'map' => ['string', 'nullable']
        ]);

        $contactUpdate = $contact->update([
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'openingHours' => $request->openingHours,
            'phone' => $request->phone,
            'map' => $request->map,
        ]);

        return response()->json([
            'data' => $contactUpdate,
            'flash' => [
                'message' => 'Data has been added.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
