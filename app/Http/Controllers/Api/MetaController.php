<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetaResource;
use App\Models\Meta;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meta = MetaResource::collection(Meta::get());

        return response()->json([
            'meta' => $meta,
        ], Response::HTTP_OK);
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
            'name' => ['string'],
            'content' => ['string'],
            'charset' => [],
            'http_equiv' => [],
        ]);

        // if(request()->name && request()->charset) {
        //     return response()->json([
        //         'flash' => [
        //             'message' => 'Data has been added.',
        //             'type' => 'error'
        //         ],
        //     ], Response::HTTP_CONFLICT);
        // }

        $meta = Meta::create([
            'name' => request()->name,
            'content' => request()->content,
            'charset' => request()->charset,
            'http_equiv' => request()->http_equiv,
        ]);

        return response()->json([
            'data' => $meta,
            'flash' => [
                'message' => 'Meta Tags has been added.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meta $meta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meta $meta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meta $meta)
    {
        request()->validate([
            'content' => ['string'],
            'name' => ['string'],
        ]);

        $updateMeta =  $meta->update([
            'content' => request('content'),
            'name' => request('name'),
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
            'metaIds' => ['required', 'array']
        ]);

        foreach (Meta::find(request('metaIds')) as $meta) {
            $data = $meta->delete();

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
