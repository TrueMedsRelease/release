@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="modal_cart hidden">
    <div class="bloktext">
       <p>{{ __('text.common_cart1') }}<b>{{ ucfirst(session('location.country_name')) }} {{ __('text.common_cart2') }}</b></p>
    </div>
</div>
<script>
    flagc = true;
</script>
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
