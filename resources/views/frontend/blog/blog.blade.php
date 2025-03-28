
@extends('layout.main')
@section('content')
<section class="section">
    <div class="container">        
        <div class="section-heading">
            <h1 class="fs-2">Blogs</h1> 
        </div>
        <div class="blog-list">
            <div class="row g-4">
                @foreach ($blogs as $blog)   
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{route('blogdetails', ['slug' => $blog->slug])}}" class="card card-blog">    
                        <div class="card-img-top cls-img" style="--cls-img-height: 71%;">
                            <img loading="lazy" 
                                src="{{ asset($blog->image ? 'storage/blogs/'.$blog->image : 'assets/images/download.png') }}">
                        </div>
                        <div class="card-body pb-0">
                            <div class="content">
                                <h3>{!! $blog->title ?? '' !!}</h3>
                               {!! \Illuminate\Support\Str::words($blog->description, 20, '...') !!}
                            </div>
                        </div>
                        <div class="card-footer">
                            <span class="link">Learn more</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>            
        </div>
      <nav class="pt-5">
    @if ($blogs->lastPage() > 1)
        <ul class="pagination">
            @foreach (range(max(1, $blogs->currentPage() - 1), min($blogs->currentPage() + 1, $blogs->lastPage())) as $page)
                <li class="page-item {{ $page == $blogs->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $blogs->url($page) }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</nav>
    </div>
</section>

@endsection