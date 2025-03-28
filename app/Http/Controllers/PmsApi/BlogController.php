<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblBlog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = TblBlog::orderBy('date', 'desc');
            
            // Filtering by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Searching by title if search parameter is provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('title', 'like', '%' . $search . '%');
            }
            
            // Retrieve results with pagination
            $perPage = $request->has('per_page') ? (int)$request->per_page : 10;
            $data = $query->paginate($perPage);
    
            // Append image_path attribute to each blog
            $data->each(function ($blog) {
                $blog->image_path = $blog->image ? '/storage/blogs/' . $blog->image : '';
                // $blog->image_path = '/storage/blogs/' . $blog->image;
            });
    
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Successfully retrieved.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


// public function store(Request $request)
// {
//     try {
//         // Validate request data
//         $validator = Validator::make($request->all(), [
//             'title' => 'required|string|max:200',
//             'description' => 'required|string',
//             'image' => 'required|string|max:200',
//             'status' => 'boolean',
//         ]);

//         // Return validation errors if any
//         if ($validator->fails()) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Validation failed',
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         // Check for duplicates based on title (if needed)
//         $checkDuplicate = TblBlog::where('title', '=', $request->title)->first();

//         if (!is_null($checkDuplicate)) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Blog already exists'
//             ], 422);
//         }
          

//         $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
//         $path = 'public/';
//         $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
//         $storingPath = $path."blogs/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);

//         Storage::move($oldpath, $storingPath);

//         $slug = 

//         // Create new blog record
//         $blog = new TblBlog();
//         $blog->image = $request->image;
//         $blog->title = $request->title;
//         $blog->description = $request->description; 
//         $blog->date = $request->date;
//         $blog->facebook_url = $request->facebook_url;
//         $blog->linkedin_url = $request->linkedin_url;
//         $blog->position = $request->position ?? 0;
//         $blog->status = $request->status ?? 1; 
//         $blog->add_ip = $request->ip();
//         $blog->add_by = Auth::user()->name;
//         $blog->save();

//         return response()->json([
//             'status' => true,
//             'message' => 'Added successfully'
//         ], 201);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Internal Error',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }




public function store(Request $request)
{
    try {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'image' => 'required|string|max:200',
            'status' => 'boolean',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        // Check for duplicates based on title
        $checkDuplicate = TblBlog::where('title', '=', $request->title)->first();

        if (!is_null($checkDuplicate)) {
            return response()->json([
                'status' => false,
                'message' => 'Blog already exists'
            ], 422);
        }

        $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
        $path = 'public/';
        $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
        $storingPath = $path."blogs/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);

        Storage::move($oldpath, $storingPath);

        // Generate slug from title
        $slug = Str::slug($request->title);

        // Create new blog record
        $blog = new TblBlog();
        $blog->image = $request->image;
        $blog->title = $request->title;
        $blog->description = $request->description; 
        $blog->date = $request->date;
        $blog->facebook_url = $request->facebook_url;
        $blog->linkedin_url = $request->linkedin_url;
        $blog->slug = $slug; // Store slug in database
        $blog->position = $request->position ?? 0;
        $blog->status = $request->status ?? 1; 
        $blog->add_ip = $request->ip();
        $blog->add_by = Auth::user()->name;
        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Added successfully'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}




     
public function show(string $id)
{
    try {
        $data = TblBlog::findOrFail($id);  

        // Add path to the image value
        $data->image_path = $data->image ? '/storage/blogs/' . $data->image : '';
        // $data->image_path = '/storage/blogs/' . $data->image;

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Successfully Retrieved.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    

public function update(Request $request, string $id)
{
    
    try {
          
     $duplicateCheck = TblBlog::where('title', $request->title)
    ->where('id', '!=', $id)
    ->withTrashed(false)
    ->whereNull('deleted_at')
    ->get();

    
        $updateSQL = TblBlog::findOrFail($id);

        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'image' => 'required|string|max:200',
            'status' => 'boolean',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($duplicateCheck->count() > 0) {
            $duplicateFound = 1;
            return response()->json([
                'status' => false,
                'message' => 'Blog Already Exists',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $duplicateFound = 0;
            $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."blogs/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
    
            Storage::move($oldpath, $storingPath);

            $slug = Str::slug($request->title);
    
            // Update the blog record
            $updateSQL->title = $request->title;
            $updateSQL->slug = $slug;
            $updateSQL->description = $request->description;
            $updateSQL->image = $request->image; // Assuming the image URL is stored directly
            $updateSQL->date = $request->date;
            $updateSQL->facebook_url = $request->facebook_url;
            $updateSQL->linkedin_url = $request->linkedin_url;
            $updateSQL->update_ip    = $request->ip();
            $updateSQL->update_by    = Auth::user()->name;
            // $updateSQL->status = $request->status ?? 1; // Assuming default status is 1 (active)
            $updateSQL->update();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);
    
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}









public function destroy(string $id)
{
    try {
        $blog = TblBlog::findOrFail($id);
        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function updateStatus(Request $request, string $id)
{
    try {
        $blog = TblBlog::findOrFail($id);

        // Validate request data
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update status
        $blog->status = $request->status;
        $blog->save();

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function deleteImage($id)
{
    // echo "123";die;
    $blog = TblBlog::find($id);
    
   
    if(!$blog) {
        return response()->json([
            'status' => false,
            'message' => 'Blog not found.'
        ], 404);
    }

    $blogImagePath = "public/blogs/" . $blog->image;

    if(!empty($blog->image) && Storage::exists($blogImagePath)) {
        if(Storage::delete($blogImagePath)) {
           $blog->update(['image' => '']);
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete the image.'
            ], 500);
        }
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Image not found.'
        ], 404);
    }
}


public function deleteMultipleRecord(Request $request)
{
    try {
        $ids = $request->get('ids');
        
        if (!empty($ids)) {
            TblBlog::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Successfully deleted selected records.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or empty IDs provided.',
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateShowOnBlog(Request $request, $id){
    try{
        $query = TblBlog::findOrFail($id);
        $request->validate([
            'show_on_blog'=> 'required|boolean',
        ]);

        $query->show_on_blog_page = $request->show_on_blog;
        if ($query->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Show on home page updated successfully'
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong'
            ], 400);
        }
    }catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
