
@extends('components.layouts.app')
@section('content')

<section class="section header-pad-top section-properties-filter bg-secondary-light3 pb-0 px-xl-3">
    <div class="container py-3 mw-xl-100 animFade">
        <div class="row gy-3">
            <div class="col-12">
                <h1 class="text-secondary mb-0">Experience our Difference</h1>
            </div>
            
        </div>
    </div>
</section>

<section class="section section-experience d-table w-100">
    <div class="swiper experience-slides d-none d-lg-block">
        <div class="swiper-wrapper">
            @foreach($our_diffrence as $diffrence)
                <div class="swiper-slide">
                    <div class="bg" style="background-image: url('{{ asset("storage/our_difference/{$diffrence->image}") }}');"></div>
                </div>
            @endforeach
        </div>
        
        <div class="swiper-pagination pagination-white swiper-pagination-vertical"></div>
    </div>
   <div class="d-table-cell align-middle">
          <div class="container">
                <div class="collapsible-wrapper animFade">
                
                    <ul class="list-unstyled mb-0">
                    
                        @foreach($our_diffrence as $index => $diffrence)
                        <li class="{{ $index === 0 ? 'active' : '' }}">
                            <h3>{{ $diffrence->title }}</h3>
                            <div class="collapse-text" style="{{ $index === 0 ? '' : 'display: none;' }}">
                                <div class="collapse-text-inner" style="font-style: normal;">
                                    {!! $diffrence->detail !!}
                                    <div class="collapse-img d-lg-none">
                                        <img src="{{ asset('storage/our_difference/' . $diffrence->image) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
   </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const differenceItems = document.querySelectorAll('.collapsible-wrapper ul li');
        differenceItems.forEach((item, index) => {
            item.addEventListener('click', function () {
                differenceItems.forEach((item) => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    });
</script>



@endsection