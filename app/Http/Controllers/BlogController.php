<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TblBlog;


class BlogController extends Controller{
// Controller Code (No Changes Needed)
public function blogs(){
    $query = TblBlog::query();
    $blogs = $query->where('status', 1)
                   ->orderBy('created_at', 'desc') 
                   ->paginate(3); 
    return view('frontend.blog.blog', compact('blogs'));
}




    // public function searchBlog(Request $request){
    //     // Get the search query from the URL parameter
    //     $searchQuery = $request->str;
    //     $query = TblBlog::query();
    //     $query->when($searchQuery != '', function ($q) use ($searchQuery) {
    //         return $q->where('title', 'like', '%' . $searchQuery . '%');
    //     });
    //     $blogs = $query->where('status', 1)->orderBy('position', 'asc')->paginate(3);
    //     $html = view('frontend.blog.search-blog', compact('blogs'));
    //     // Return the view with the filtered or all blogs
    //     return $html;
    // }

    // public function loadMore(Request $request){
    //     // Get the search query from the URL parameter
    //     $offset = $request->offset;
    //     $query = TblBlog::query();
    //     $blogs = $query->where('status', 1)->offset($offset)->limit(2)->orderBy('position', 'asc')->paginate(3);
    //     $html = view('frontend.blog.search-blog', compact('blogs'));
    //     // Return the view with the filtered or all blogs
    //     return response()->json([
    //         'html' => $html,
    //         'offset' => $offset + 1
    //     ]);
    //     return $html;
    // }

    public function blogdetails($slug){
        // Retrieve the blog from the database based on the provided id
        $blog = TblBlog::where('slug', $slug)->where('status', 1)->orderBy('position', 'asc')->firstOrFail();
        $latestBlogs = TblBlog::where('status', 1)->whereNull('deleted_at')->orderBy('position', 'asc')->latest()->take(3)->get();
        // Pass the retrieved blog to the view
        return view('frontend.blog.blogdetail', ['blog' => $blog, 'latestBlogs' => $latestBlogs]);
    }
}
