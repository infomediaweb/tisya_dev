<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // return $request->all();
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate a unique name for the file
            $filename = uniqid() . '_' . $file->getClientOriginalName();

            // Store the file in the storage/app/public directory
            if ($request->type == 'ckeditor') {
                $path = $file->storeAs('public/ckeditor', $filename);
            } else {
                $path = $file->storeAs('public/temp_images', $filename);
                $output = [
                    'uploadType' => true
                ];
            }

            $fullPath = Storage::url($path);

            $output['status'] = true;
            $output['filename'] = $filename;
            $output['filepath'] = $fullPath;
            $output['extension'] = $file->getClientOriginalExtension();

            return response()->json($output, 200);
        } else {
            return response()->json([
                'message' => 'No file uploaded'
            ], 400);
        }
    }
}
