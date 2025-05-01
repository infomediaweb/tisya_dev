@extends('components.layouts.app')
@section('content')

{{-- <?php echo "<pre>";print_r($blog);die; ?> --}}


<section class="section section-top section-blog pb-0">
    <div class="container">

        <div class="row">
            <div class="col-12">
                <a href="{{route('blogs')}}" class="d-block h6 mb-3">All blogs</a>
                <div class="title mb-4 animFade">
                    <h2>{{$blog['title']}}</h2>
                </div>
            </div>
            <div class="col-12">
                <div class="search-wrap social-wrap">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="blog-date h-100">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $blog['date'])->format('d M Y') }}</div>
                        </div>
                        <div class="col-auto">

                            <ul class="list-unstyled mb-0 d-flex align-items-center">
                                <li>
                                    <span>Share</span>
                                </li>
                                <li>
                                    <a href="{{$blog['facebook_url']}}">
                                        <i class="icon-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{$blog['linkedin_url']}}">
                                        <i class="icon-linkedin"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section-blog py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="blog-content">
                    {!!$blog['description']!!}

                    <img src="{{ asset('storage/blogs/' . $blog->image) }}" class="w-100" alt="Explore Our Newest Apartments in Kasauli Hills">

                </div>
            </div>
        </div>
    </div>
</section>


<section class="section section-blog bg-secondary-light3">
    <div class="container">
        <div class="title text-center animFade">
            <h2>Also Read</h2>
        </div>

        <div class="blog-cards">
            <div class="row gy-4">
                @foreach($latestBlogs as $blog)
                <div class="col-12 col-md-4 animFade">
                    <a href="{{ route('blog.detail', $blog->id) }}" class="card card-secondary">
                        <div class="card-img-top">
                            <span>
                                <img loading="lazy" src="{{ asset('storage/blogs/' . $blog['image']) }}" alt="{{ $blog['title'] }}">
                            </span>
                        </div>
                        <div class="card-footer">
                            <time datetime="{{ $blog['date'] }}">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $blog['date'])->format('d M, Y') }}</time>
                            <h3>{{ $blog['title'] }}</h3>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        <div class="btn-wrap animFade">
            <a href="{{ route('blogs') }}" class="btn btn-secondary">READ ALL BLOGS</a>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function(){


    })
</script>



@endsection
