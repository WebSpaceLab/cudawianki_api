<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $about = About::where('user_id', auth()->user()->id)
                ->orderBy(request('orderBy'), request('orderDir'))
                ->month(request('month'))
                ->status(request('status'))
                ->search(request('term'))
                ->paginate(request('per_page'))
            ;

            $pagination = [
                'total' => $about->total(),
                'per_page' => $about->perPage(),
                'current_page' => $about->currentPage()
            ];

            $aboutResource = AboutResource::collection( $about);

            $status = About::selectRaw('distinct status')
                ->get()
                ->map(function ($item, $key) {
                    return [
                        'value' => $item->status,
                        'label' => $item->status ? 'Active' : 'Not active'
                    ];
                })
                ->unique('value')->values();

            $months = DB::table('abouts')
                ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
                ->orderByDesc('value')
                ->get();

            return response()->json([
                'about' => $aboutResource,
                'pagination' => $pagination,
                'months' => $months,
                'status' => $status,
                'queryParams' => request()->all(['month', 'term', 'status', 'orderBy', 'orderDir'])
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function get()
    {
        $about = About::where('status', 1)->paginate(3);

        $aboutResource = AboutResource::collection( $about);

        return response()->json([
            'about' => $aboutResource
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
    public function store()
    {
        request()->validate([
            'name' => ['required', 'unique:abouts'],
            'description' => ['string', 'required'],
            'status' => ['required'],
            'media_id'=>  ['required'],
        ]);

        $about = About::create([
            'user_id' => auth()->id(),
            'name' => request('name'),
            'description' => request('description'),
            'status' => request('status'),
            'media_id' => request('media_id'),
        ]);

        return response()->json([
            'about' => $about,
            'flash' => [
                'message' => 'A new about tags has been added.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(About $about)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(About $about)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, About $about)
    {
        request()->validate([
            'name' => ['required', 'string'],
            'description' => ['string', 'required'],
            'status' => ['required'],
            'media_id' => [],
        ]);

        $about->update([
            'name' => request('name'),
            'description' => request('description'),
            'status' => request('status'),
            'media_id' => request('media_id')
        ]);

        return response()->json([
            'succeed' => $about,
            'flash' => [
                'message' => 'Offer has been updated.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        request()->validate([
            'aboutIds' => ['required', 'array']
        ]);


        foreach (About::find(request('aboutIds')) as $about) {
            $about->delete();
        }

        return response()->json([
            'succeed' =>  $about,
            'flash' => [
                'message' => 'The category has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }
}
