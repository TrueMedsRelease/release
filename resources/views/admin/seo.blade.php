@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')
<div id="properties_content">

</div>

<script>
    $(document).ready(function() {
        $.ajax({
        method: 'GET',
        data: { },
            url: "{{ route('admin.admin_seo_content') }}",
            dataType: 'html',
            success : function(data) {
                data = JSON.parse(data);
                $('#properties_content').html(data.html);
            }
        });
    });
</script>

@endsection