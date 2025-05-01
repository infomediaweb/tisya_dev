
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
                        <input type="text" class="form-control py-3 searchInput" placeholder="Search blog here">
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
        {{-- <div class="load-more-wrap pt-5 d-flex justify-content-center">
            <button class="btn btn-link fw-bold load-more-data">
                <i class="bi bi-arrow-down"></i>
                Load more
            </button>
        </div> --}}
        @if($blogs->isEmpty())
            <div class="load-more-wrap pt-5 d-flex justify-content-center">
                <button class="btn btn-link fw-bold">
                    <center><p>No blog found.</p></center>
                </button>
            </div>
        @endif
        @csrf
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let offset = 1
        $(document).on('keyup', '.searchInput', function () {
            // Construct the URL with the search query as a parameter
            $.ajax({
                url: "blogs/search",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
                data: {
                    str: $(this).val()
                },
                success: function(html) {
                   $('.blog-cards').html(html);
                }
            });
        });

        $(document).on('click', '.load-more-data', function () {
            // Construct the URL with the search query as a parameter
            $.ajax({
                url: "blogs/loadmore",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
                data: {
                    offset: offset
                },
                success: function(res) {
                   $('.blog-cards').html(res.html);
                   offset = res.offset
                }
            });
        });
    });
</script>
    <script>
        document.addEventListener("DOMContentLoaded", function(){

        })
    </script>
@endsection
