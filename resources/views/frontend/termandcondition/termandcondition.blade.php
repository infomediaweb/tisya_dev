
@extends('layout.main')
@section('content')
<section class="section">
    <div class="container">   
      @foreach ($termandcondition as $termandconditions)
        <div class="section-heading">
            <h1 class="fs-2">{!! $termandconditions->title_text ?? '' !!}</h1> 
        </div>
        
             
        <div class="content">
            <p>{!! $termandconditions->terms_and_condition ?? '' !!}</p>
        </div>
           @endforeach
    </div>
</div>
</section>
@endsection