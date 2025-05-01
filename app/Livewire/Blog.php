<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TblBlog;

class Blog extends Component
{
    
    public $recent_blog;

     
    public function mount()
    {
        $searchKey = request()->input('search');

        // dd($searchKey); 
    
        $query = TblBlog::query();
    
        if ($searchKey) {
            // If search key is provided, search for it in the blog titles
            $query->where('title', 'like', '%' . $searchKey . '%');
        } else {
            // If no search key, order by default
            $query->orderBy('created_at', 'desc');
        }
    
        // Fetch filtered or default ordered blogs
        $this->recent_blog = $query->where('status', 1)
                                   ->whereNull('deleted_at')
                                   ->get();
    }
    
   
    public function render()
    {
        return view('livewire.blog');
    }
}
