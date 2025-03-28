@extends('layout.main')
@section('content')
<style>
    .editor-content img{
        max-width: 100%;
    }
</style>
<section class="section section-blog-details">
        <div class="container">        
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    
                    <div class="section-heading">
                        <h1 class="fs-2">{!! $blog->title !!}</h1> 
                    </div>
                    <div class="blog-img p-3 rounded-4 bg-white shadow overflow-hidden">
                      <img class="w-100 rounded-4" loading="lazy" 
                                 src="{{ asset($blog->image ? 'storage/blogs/'.$blog->image : 'assets/images/download.png') }}">
                                 
                    </div>
                    <div class="content editor-content pt-5">
                    
                            {!! $blog->description !!}

                           
                    </div>  
                </div>
            </div>         
            
        </div>
    </div>
</section>
@endsection