<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Image;

class MediaController extends Controller
{
    public function index()
    {
        try {
            $media = Media::where('author_id', auth()->id())
                ->orderBy(request('orderBy'), request('orderDir'))
                ->type(request('fileType'))
                ->month(request('month'))
                ->search(request('term'))
                ->paginate(request('per_page')
            );

            $pagination = [
                'total' => $media->total(),
                'per_page' => $media->perPage(),
                'current_page' => $media->currentPage()
            ];

            $mediaResource = MediaResource::collection($media );


            $fileTypes = Media::selectRaw('distinct mime_type')
                ->get()
                ->map(function ($item, $key) {
                    return [
                        'value' => $item->file_type,
                        'label' => ucfirst($item->file_type)
                    ];
                })
                ->unique('value')->values();

            $months = DB::table('media')
                ->selectRaw('distinct DATE_FORMAT(created_at, "01-%m-%Y") as value, DATE_FORMAT(created_at, "%M %Y") as label')
                ->orderByDesc('value')
                ->get();

                // dd($media);

            return response()->json([
                'success' => 'OK',
                'media' => $mediaResource,
                'pagination' => $pagination,
                'fileTypes' => $fileTypes,
                'months' => $months,
                'queryParams' => request()->all(['fileType', 'month', 'term'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        request()->validate([
            'file' => ['file', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $file = $request->file('file');

        $directory = 'media/' . auth()->user()->id;

        $media = Media::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->hashName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'author_id' => auth()->id(),
            'filePath' =>  url('storage/' . $directory . '/' . $file->hashName())
        ]);


        $file->storeAs($directory, $media->file_name, 'public');

        return response()->json([
            'file' => [
                'id' => $media->id,
                'preview_url' => $media->preview_url,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'author' => [
                    'id' => $media->author->id,
                    'name' => $media->author->name,
                ],
                'created_at' => $media->created_at->format('d/m/Y'),
            ],
            'flash' => [
                'message' => 'The file has been added.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function storeWithCropper(Request $request) {
        request()->validate([
            'image' => ['image', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);

        $image = Image::make(request()->file('image'));
        $file = request()->file('image');
        $extension = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        if($extension) {
            $name = preg_replace('/\.' . preg_quote($extension, '/') . '$/', '', $name);

            $name = $name .'-'.time();
        }

        // modify name
        $directory = '/storage/media/' . auth()->user()->id. '/';
        $filePath = $directory . $name . $file->hashName();
        $image->crop(
            request()->width,
            request()->height,
            request()->left,
            request()->top
        );

        // $name = time() . '.' . $extension;
        $image->save(public_path() . $filePath );

        $media = Media::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->hashName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'author_id' => auth()->id(),
            'filePath' =>  url( $filePath)
        ]);

        return response()->json([
            'file' => [
                'id' => $media->id,
                'preview_url' => $media->preview_url,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'author' => [
                    'id' => $media->author->id,
                    'name' => $media->author->name,
                ],
                'created_at' => $media->created_at->format('d/m/Y'),
            ],
            'flash' => [
                'message' => 'The file has been added.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function show(Request $request) {
        $media = MediaResource::collection(
            Media::where('author_id', auth()->id())->whereIn('mime_type', Media::getMimes('image'))->latest()->paginate($request->get('limit', 12))
        );


        $current_page = $media->currentPage();

        return response()->json([
            'media' => $media,
            'meta' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage()
            ],
            // 'links' => [
            //     'prev' => $media->prev()
            // ]
        ], Response::HTTP_OK);
    }

    public function update(Media $media)
    {
        // dd(request('name'));
        request()->validate([
            'name' => ['required', 'unique:media'],
            'description' => [],
        ]);

        $media->update([
            'name' => request('name'),
            'description' => request('description'),
        ]);

        return response()->json([
            'media' => $media,
            'flash' => [
                'message' => 'The file has been updated.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }

    public function updateFile(Media $media)
    {
        request()->validate([
            'image' => ['image', 'max:512000']
        ], [
            'max' => 'File cannot be larger than 512MB.'
        ]);


        $deleted = Storage::disk('public')->delete($media->pathDeleted);

        if($deleted) {
            $image = Image::make(request()->file('image'));
            $file = request()->file('image');
            $extension = $file->getClientOriginalExtension();
            $name = $file->getClientOriginalName();
            if($extension) {
                $name = preg_replace('/\.' . preg_quote($extension, '/') . '$/', '', $name);

                $name = $name .'-'.time();
            }

            // modify name
            $directory = '/storage/media/' . auth()->user()->id. '/';
            $filePath = $directory . $name . $file->hashName();
            $image->crop(
                request()->width,
                request()->height,
                request()->left,
                request()->top
            );

            // $name = time() . '.' . $extension;
            $image->save(public_path() . $filePath );

            $media->update([
                'name' => $name,
                'file_name' => $file->hashName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'author_id' => auth()->id(),
                'filePath' =>  url($filePath)
            ]);


            // $file->storeAs($directory, $media->file_name, 'public');
            return response()->json([
                'file' => [
                    'id' => $media->id,
                    'preview_url' => $media->preview_url,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'author' => [
                        'id' => $media->author->id,
                        'name' => $media->author->name,
                    ],
                    'created_at' => $media->created_at->format('d/m/Y'),
                ],
                'flash' => [
                    'message' => 'The file has been updated.',
                    'type' => 'success'
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'flash' => [
                    'message' => 'Something went wrong',
                    'type' => 'error'
                ]
            ], 417);
        }
    }

    public function destroy()
    {
        request()->validate([
            'mediaIds' => ['required', 'array']
        ]);


        foreach (Media::find(request('mediaIds')) as $media) {
            $media->delete();
            $deleted = Storage::disk('public')->delete($media->pathDeleted);
        }

        return response()->json([
            'data' =>  $deleted,

            'flash' => [
                'message' => 'The file has been deleted.',
                'type' => 'success'
            ]
        ], Response::HTTP_OK);
    }
}
