@extends('layout.main')

@section('content')
<section class="section offers-section">
    <div class="container">
        <div class="section-heading">
            <div class="row gy-3">
                <div class="col-12 col-lg">
                    <h2>Special Offers</h2>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            @foreach ($specialoffers as $specialoffer) 
            <div class="col-12 col-xl-4">
                <div class="card card-offers">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3>{{ $specialoffer->offer_name }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="card-offer-img">
                                    <img loading="lazy" 
                                        src="{{ $specialoffer->image ? asset('storage/special_invitations/' . $specialoffer->image) : asset('assets/images/d1.jpg') }}" 
                                        alt="{{ $specialoffer->offer_name }}">
                                </div>
                            </div>
                        </div>
                        <p>{!! $specialoffer->description !!}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row gy-2 gx-3 align-items-center">
                            <div class="col-12 col-xxl-7">
                                <div class="offer-code">
                                    <span>{{ explode(',', $specialoffer->discountCoupon->codes ?? '')[0] ?? '' }}</span>
                                    <button class="btn btn-primary" data-clipboard-text="{{ explode(',', $specialoffer->discountCoupon->codes ?? '')[0] ?? '' }}"><i class="icon-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="exp-text">
                                    Expires on <strong>{{ \Carbon\Carbon::parse($specialoffer->validity)->format('d M, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => { 
      $(".offer-code .btn").each(function() {
        var clipboard = new ClipboardJS(this);
        clipboard.on('success', function(e) {
          $(e.trigger).addClass("active");
          setTimeout(() => {
            $(e.trigger).removeClass("active");
          }, 1000);
          e.clearSelection();
        });
      });
    });
  </script>
  
@endsection
