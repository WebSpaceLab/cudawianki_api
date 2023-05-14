<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Models\OfferCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::where('user_id', auth()->user()->id)
            ->orderBy(request('orderBy'), request('orderDir'))
            ->month(request('month'))
            ->status(request('status'))
            ->search(request('term'))
            ->paginate(request('per_page'));

        $pagination = [
            'total' => $offers->total(),
            'per_page' => $offers->perPage(),
            'current_page' => $offers->currentPage()
        ];

        $offerResource = OfferResource::collection( $offers );

        $status = Offer::selectRaw('distinct status')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'value' => $item->status,
                    'label' => $item->status ? 'Active' : 'Not active'
                ];
            })
            ->unique('value')->values();

        $months = DB::table('offers')
            ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
            ->orderByDesc('value')
            ->get();

        return response()->json([
            'offers' => $offerResource,
            'pagination' => $pagination,
            'months' => $months,
            'status' => $status,
            'queryParams' => request()->all(['month', 'term', 'status', 'orderBy', 'orderDir'])
        ], Response::HTTP_OK);
    }

    public function get()
    {
        $offers = Offer::where('status', 1)->get();

        $offersResource = OfferResource::collection( $offers);

        return response()->json([
            'offers' => $offersResource
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
            'title' => ['required', 'unique:offers'],
            'description' => ['string', 'nullable'],
            'transition' => ['string', 'required'],
            'status' => ['required'],
            'media_id'=>  ['required'],
        ]);

        dd(request('description'));

        $offer = Offer::create([
            'user_id' => auth()->id(),
            'title' => request('title'),
            'description' => request('description'),
            'transition' => request('transition'),
            'status' => request('status'),
            'slug' => Str::of(request('title'))->slug('-'),
            'media_id' => request('media_id'),
        ]);

        return response()->json([
            'offer' => $offer,
            'flash' => [
                'message' => 'A new category has been added.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function addPhotosToOffer(Offer $offer)
    {
        request()->validate([
            'photoIds'=>  ['required'],
        ]);

        $offer->galleries()->attach(request('photoIds'));

        return response()->json([
            'offer' => $offer,
            'flash' => [
                'message' => 'The photos has been added to the offer.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        request()->validate([
            'title' => ['required'],
            'description' => ['string', 'nullable'],
            'status' => ['required'],
            'media_id' => [],
            'transition' => []
        ]);

        $offer->update([
            'title' => request('title'),
            'description' => request('description'),
            'transition' => request('transition'),
            'status' => request('status'),
            'slug' => Str::of(request('title'))->slug('-'),
            'media_id' => request('media_id')
        ]);

        return response()->json([
            'offer' => $offer,
            'flash' => [
                'message' => 'Offer has been updated.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        request()->validate([
            'categoriesId' => [ 'array'],
            'mediaId' => ['array']
        ]);

        foreach (OfferCategory::find(request('categoriesId')) as $category) {
            $category->delete();
        }

        $offer->delete();

        return response()->json([
            'flash' => [
                'message' => 'The offers has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function destroyPhotoFromOffer(Offer $offer)
    {
        request()->validate([
            'mediaIds' => ['required', 'array']
        ]);

        $offer->galleries()->detach(request('mediaIds'));

        return response()->json([
            'flash' => [
                'message' => 'Photo from offer has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

}
