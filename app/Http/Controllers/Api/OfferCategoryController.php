<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferCategoryResource;
use App\Models\Offer;
use App\Models\OfferCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class OfferCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {

    //     $categoriesResource = OfferCategoryResource::collection(OfferCategory::where('status', 1)->get());


    //     return response()->json([
    //         'categories' => $categoriesResource,
    //     ], 200);
    // }

    public function index()
    {
        try {
            $categories = OfferCategory::where('user_id', auth()->user()->id)
                ->orderBy(request('orderBy'), request('orderDir'))
                ->month(request('month'))
                ->status(request('status'))
                ->search(request('term'))
                ->paginate(request('per_page'))
            ;

            $pagination = [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage()
            ];

            $categoriesResource = OfferCategoryResource::collection( $categories);

            $status = OfferCategory::selectRaw('distinct status')
                ->get()
                ->map(function ($item, $key) {
                    return [
                        'value' => $item->status,
                        'label' =>  $item->status ? 'Active' : 'Not active'
                    ];
                })
                ->unique('value')->values();

            $months = DB::table('offer_categories')
                ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
                ->orderByDesc('value')
                ->get();

            return response()->json([
                'categories' => $categoriesResource,
                'pagination' => $pagination,
                'months' => $months,
                'status' => $status,
                'queryParams' => request()->all(['month', 'term', 'status'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function get()
    {
        $categories = OfferCategory::where('status', 1)->get();

        $categoryResource = OfferCategoryResource::collection( $categories);

        return response()->json([
            'categories' => $categoryResource
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $offers = Offer::where('status', 1)
            // ->selectRaw('distinct title')
            ->get()
            ->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'value' => $item->id,
                    'label' => ucfirst($item->title)

                ];
            })
            ->unique('value')->values();

        return response()->json([
            'offers' => $offers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => ['required', 'unique:offer_categories'],
            'description' => ['string', 'nullable'],
            'transition' => ['string', 'required'],
            'status' => ['required'],
            'media_id'=>  ['required'],
            'offer_id'=>  ['required'],
        ]);

        $category = OfferCategory::create([
            'user_id' => auth()->id(),
            'name' => request('name'),
            'description' => request('description'),
            'transition' => request('transition'),
            'status' => request('status'),
            'slug' => Str::of(request('name'))->slug('-'),
            'media_id' => request('media_id'),
            'offer_id' => request('offer_id'),
        ]);

        return response()->json([
            'category' => $category,
            'flash' => [
                'message' => 'A new category has been added.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

        /**
     * Store a newly created resource in storage.
     */
    public function addPhotosToCategory(OfferCategory $offer_category)
    {
        request()->validate([
            'photoIds'=>  ['required'],
        ]);

        $categories = $offer_category->galleries()->attach(request('photoIds'));

        return response()->json([
            'categories' => $categories,
            'flash' => [
                'message' => 'The photos has been added to the category.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(OfferCategory $offerCategory)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfferCategory $offerCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OfferCategory $offerCategory)
    {
        request()->validate([
            'name' => ['required'],
            'description' => ['string', 'nullable'],
            'status' => ['required'],
            'media_id' => [],
            'transition' => []
        ]);

        $category = $offerCategory->update([
            'name' => request('name'),
            'description' => request('description'),
            'transition' => request('transition'),
            'status' => request('status'),
            'slug' => Str::of(request('name'))->slug('-'),
            'media_id' => request('media_id')
        ]);

        return response()->json([
            'category' => $category,
            'flash' => [
                'message' => 'Category has been updated.',
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
            'categoriesId' => ['required', 'array']
        ]);


        foreach (OfferCategory::find(request('categoriesId')) as $category) {
            $category->delete();
        }

        return response()->json([
            'flash' => [
                'message' => 'The category has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function destroyPhotoFromCategory(OfferCategory $offerCategory)
    {
        request()->validate([
            'mediaIds' => ['required', 'array']
        ]);

        $deleted = $offerCategory->galleries()->detach(request('mediaIds'));

        return response()->json([
            'flash' => [
                'message' => 'Photo from category has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }
}
