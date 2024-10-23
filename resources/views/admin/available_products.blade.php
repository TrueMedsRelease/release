@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<div class="statistic" style="margin-top: 2%;">

    <div class="statistic__rows" id="available_product">

    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajax({
        method: 'GET',
        data: { },
            url: "{{ route('admin.available_products_content') }}",
            dataType: 'html',
            success : function(data) {
                data = JSON.parse(data);
                $('#available_product').html(data.html);
            }
        });
    });
</script>

@endsection