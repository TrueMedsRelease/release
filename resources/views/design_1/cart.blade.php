@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
{{-- <div class="cmcmodal">
    <div class="bloktext">
       <p><b>{{random_int(2, 30)}}{{__('text.common_product1')}}</b>{{__('text.common_product2')}}</p>
    </div>
</div> --}}
    <div class="main" id="scroll">
    <!-- content -->
    <section class="basket"  id="shopping_cart">



    </section>
    <!-- END content -->
    </div>
    </div>
    </div>
    </div>
@endsection
