 
@extends('components.layouts.app')
@section('content')


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
                        <input type="text" id="searchInput" class="form-control py-3" placeholder="Search blog here">
                    </div>
                </div>
                <div class="col-12 col-md-auto col-lg-3">
                    <form id="searchForm" action="{{ route('blogs') }}" method="GET">
                        <ul class="list-unstyled mb-0 d-flex align-items-center">
                            {{-- <li class="dropdown w-100">
                                <button type="submit" class="btn btn-light rounded-pill btn-icon text-secondary dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5">
                                    <i class="bi bi-sort-down-alt me-2"></i> <span>Sort By</span>
                                </button>
                                <ul class="dropdown-menu bg-secondary-light3">
                                    <li>
                                        <a class="active dropdown-item" href="#">
                                            A-z
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            Price High to Low
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            Latest Properties
                                        </a>
                                    </li>
                                </ul>
                            </li> --}}
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-blog py-5">
    <div class="container">
        <div class="blog-cards">
            <div class="row gy-5">
                {{-- <?php echo "<pre>";print_r($blogs);die; ?> --}}
                @foreach ($blogs as $blog)
                <div class="col-12 col-md-4 animFade">
                    <a href="{{ route('blog.detail', $blog->slug) }}" class="card card-secondary"> 



                        <div class="card-img-top">
                            <span>
                                <img loading="lazy" src="{{ asset('storage/blogs/' . $blog->image) }}" alt="{{ $blog->title }}">
                            </span>
                        </div>
                        <div class="card-footer">
                            <time datetime="{{ $blog->date }}">{{ date('j M, Y', strtotime($blog->date)) }}</time>
                            <h3>{{ $blog->title }}</h3>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <section class="section section-properties-listing py-5">
            <div class="container">
                <div class="properties-listing">
                    
                </div>
            </div>
        </section>
        
        <div class="load-more-wrap pt-5 d-flex justify-content-center">
            <button class="btn btn-link fw-bold">
                @if($blogs->isEmpty())
                       <center><p>No blogs found.</p></center> 
                    @else
                        @foreach($blogs as $blog)
                            <!-- Your blog item HTML code goes here -->
                        @endforeach
                    @endif
                
            </button>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        searchInput.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission
                const searchQuery = searchInput.value.trim(); // Get the search query

                // Construct the URL with the search query as a parameter
                const url = "{{ route('blogs') }}" + "?search=" + encodeURIComponent(searchQuery);

                // Redirect the user to the new URL
                window.location.href = url;
            }
        });
    });
</script>



<script>
    document.addEventListener("DOMContentLoaded", function(){
         

    })
</script>



@endsection