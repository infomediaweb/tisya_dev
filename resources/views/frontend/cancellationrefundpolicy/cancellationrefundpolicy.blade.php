@extends('layout.main')
@section('content')

<section class="section">
    <div class="container">  
     @foreach ($cancellationpolicys as $cancellationpolicy)
        <div class="section-heading">
            <h1 class="fs-2">{!!  $cancellationpolicy->title ?? '' !!}</h1> 
        </div>
        <div class="content">
               <p>{!!  $cancellationpolicy->cancellation_policy ?? '' !!}</p>
            <!--<h3>EVERYTHING. RIGHT WHERE YOU NEED IT.</h3>-->
            <!--<p>TISYASTAYS is a boutique hotel nestled in a tiny village in Parra, Mapusa, northside of Goa. It’s the serene location and laid-back life, slows down time, and invites relaxation. This ultimate getaway destination is visibly cladded with laterite stone giving it a subtle blend of luxury and grandeur. The hotel building is divided into a total of thirty-two aesthetically-designed rooms, including villa rooms, luxury rooms, and County side Pacheco’s luxury rooms. All these spaces blend old-world architecture and contemporary furnishings, making them perfect for leisure travelers and family holidays.</p>-->

            <!--<h3>Cancellation Policy</h3>-->
            <!--<p>1. 0-14 Days is Non Refundable<br>-->
            <!--2. Long Weekends and Super Peak dates such as 22 nd December to 4 th Jan are Non Refundable Non Amendable.<br>-->
            <!--3. No Amendments after check in, Bookings cannot be cancelled or refunded.<br>-->
            <!--4. Failure to arrive at the accommodation will be treated as No show &amp; no refund will be given<br>-->
            <!--5. No Refund will be given in a case of Act of God, etc.<br>-->
            <!--6. Terms and Conditions apply</p>-->
        
        </div>
    </div>
       @endforeach
</div>
</section>

@endsection