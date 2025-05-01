<x-slot:description>Index Page</x-slot:description>
<x-slot:keywords>Index Page keyword</x-slot:description>

<section>
    <x-layouts.header />

<section class="section section-top section-blog pb-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="title text-center animFade">
                    <h2>Blogs</h2>
                </div>
            </div>
        </div>
         <div class="search-wrap">
            <div class="row">
                <div class="col-12 col-md">
                    <div class="input-group h-100">
                        <span class="input-group-text">
                            <i class="icon-search"></i>
                        </span>
                        <div>
                            <input type="text" class="form-control" placeholder="Search blog here">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-auto col-lg-3">
                    <ul class="list-unstyled mb-0 d-flex align-items-center">
                        <li class="dropdown w-100">
                            <button class="btn btn-light rounded-pill btn-icon text-secondary dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5">
                                <i class="bi bi-sort-down-alt me-2"></i> <span>Sort By</span>
                            </button>
                            <ul class="dropdown-menu bg-secondary-light3">
                                <li>
                                    <a class="dropdown-item {{ request('filter') === null ? 'active' : '' }}" href="{{ route('blogs', ['filter' => null]) }}">
                                        A - Z
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request('filter') === 'date' ? 'active' : '' }}" href="{{ route('blogs', ['filter' => 'date']) }}">
                                        Date
                                    </a>
                                </li>
                                {{-- <li>
                                    <a class="dropdown-item {{ request('filter') === 'latest_properties' ? 'active' : '' }}" href="{{ route('blogs', ['filter' => 'latest_properties']) }}">
                                        Latest Properties
                                    </a>
                                </li> --}}
                            </ul>
                            
                        </li>
                    </ul>
                </div>
            </div>
         </div>
    </div>
</section>
<section class="section section-blog py-5">
    <div class="container">
        <div class="blog-cards">         
            {{-- {{ route('blog_detail', $blog->id) }} --}}
            <div class="row gy-5">
                @foreach($recent_blog as $blog)
                    <div class="col-12 col-md-4 animFade">
                        <a href="{{ route('blog_detail', ['id' => $blog->id]) }}" class="card card-secondary">
                            <div class="card-img-top">
                                <span>
                                    <img loading="lazy" src="{{ asset('storage/blogs/'. $blog->image) }}" alt="{{ $blog->title }}">
                                </span>
                            </div>
                            <div class="card-footer">
                                <time datetime="{{ $blog->created_at->format('Y-m-d') }}">{{ $blog->created_at->format('d M, Y') }}</time>
                                <h3>{{ $blog->title }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            

        </div>
        <div class="load-more-wrap pt-5 d-flex justify-content-center">
            <button class="btn btn-link fw-bold">
                <i class="bi bi-arrow-down"></i>
                Load more
            </button>
        </div>
    </div>

</section>


<script>
    document.addEventListener("DOMContentLoaded", function(){
         

    })
</script>

  
<x-layouts.footer />
</section>


