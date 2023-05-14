<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialResource;
use App\Models\Social;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $social = SocialResource::collection(Social::get());

        return response()->json([ 'social' => $social ], Response::HTTP_OK);
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
            'name' => ['string', 'required'],
            'icon' => ['string', 'required'],
            'to' => ['string', 'required'],
            'is_active' => ['boolean', 'required'],
        ]);

        // if(request()->name && request()->charset) {
        //     return response()->json([
        //         'flash' => [
        //             'message' => 'Data has been added.',
        //             'type' => 'error'
        //         ],
        //     ], Response::HTTP_CONFLICT);
        // }

        $social = Social::create([
            'name' => request()->name,
            'icon' => request()->icon,
            'to' => request()->to,
            'is_active' => request()->is_active,
        ]);

        return response()->json([
            'data' => $social,
            'flash' => [
                'message' => 'Social media has been added.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Social $social)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Social $social)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Social $social)
    {
        request()->validate([
            'name' => ['string'],
            'icon' => ['string'],
            'to' => ['string'],
            'is_active' => ['boolean'],
        ]);

        $updateMeta =  $social->update([
            'name' => request()->name,
            'icon' => request()->icon,
            'to' => request()->to,
            'is_active' => request()->is_active,
        ]);

        return response()->json([
            'data' => $updateMeta,
            'flash' => [
                'message' => 'The data has been updated.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        request()->validate([
            'socialIds' => ['required', 'array']
        ]);

        foreach (Social::find(request('socialIds')) as $social) {
            $data = $social->delete();

        }

        return response()->json([
            'data' => $data,
            'flash' => [
                'message' => 'Plik został usunięty.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }
}
