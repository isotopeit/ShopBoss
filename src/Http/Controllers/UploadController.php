<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Upload;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    public function filepondUpload(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:png,jpeg,jpg'
        ]);

        if ($request->hasFile('image')) 
        {
            $uploaded_file = $request->file('image');
            $filename = now()->timestamp . '.' . $uploaded_file->getClientOriginalExtension();
            $folder = uniqid() . '-' . now()->timestamp;

            $file = Image::make($uploaded_file)->encode($uploaded_file->getClientOriginalExtension());

            Storage::put('temp/' . $folder . '/' . $filename, $file);

            Upload::create([
                'folder'   => $folder,
                'filename' => $filename
            ]);

            return $folder;
        }

        return false;
    }


    public function filepondDelete(Request $request) {
        $upload = Upload::where('folder', $request->getContent())->first();

        Storage::deleteDirectory('temp/' . $upload->folder);
        $upload->delete();

        return response(null);
    }


    public function dropzoneUpload(Request $request) {
        $file = $request->file('file');

        $filename = now()->timestamp . '.' . trim($file->getClientOriginalExtension());

        Storage::putFileAs('public/dropzone/', $file, $filename);

        return response()->json([
            'name'          => $filename,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function dropzoneDelete(Request $request) {
        Storage::delete('public/dropzone/' . $request->file_name);

        return response()->json($request->file_name, 200);
    }
}
