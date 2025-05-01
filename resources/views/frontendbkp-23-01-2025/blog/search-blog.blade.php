<div class="row gy-5">
    @foreach ($blogs as $blog)
        <div class="col-12 col-md-4">
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
