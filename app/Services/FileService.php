<?php

namespace App\Services;

use Image;

class FileService
{
    public function updateImage($model, $request, $path)
    {
        $image = Image::make($request->file('image'));

        if (!empty($model->avatar_url)) {
            $currentImage = public_path() . $model->avatar_url;

            if (file_exists($currentImage) && $currentImage != public_path() . '/user-placeholder.png') {
                unlink($currentImage);
            }
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();

        $image->crop(
            $request->width,
            $request->height,
            $request->left,
            $request->top
        );

        $name = time() . '.' . $extension;
        $image->save(public_path() . $path . $name);
        $model->avatar_url = $path . $name;

        return $model;
    }

    public function addVideo($model, $request)
    {
        $video = $request->file('video');
        $extension = $video->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $video->move(public_path() . '/files/', $name);
        $model->video = '/files/' . $name;

        return $model;
    }
}
