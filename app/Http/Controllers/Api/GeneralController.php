<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\UsersCollection;
use App\Models\General;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $general = GeneralResource::collection(General::where('id', 1)->get());

        return response()->json([ 'general' => $general ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => ['string', 'max:255'],
            'description' => ['string']
        ]);

        $general = General::create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'data' => $general,
            'flash' => [
                'message' => 'Data has been added.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    public function getRandomUsers()
    {
        try {
            $suggested = User::inRandomOrder()->limit(5)->get();
            $following = User::inRandomOrder()->limit(10)->get();

            return response()->json([
                'suggested' => new UsersCollection($suggested),
                'following' => new UsersCollection($following)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(General $general)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(General $general)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(General $general)
    {
        request()->validate([
            'title' => ['string', 'required', 'max:255'],
            'description' => ['string'],
        ]);

        $generalUpdate = $general->update([
            'title' => request('title'),
            'description' => request('description'),
        ]);

        return response()->json([
            'data' => $generalUpdate,
            'flash' => [
                'message' => 'The data has been updated.',
                'type' => 'success'
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(General $general)
    {
        //
    }
}
