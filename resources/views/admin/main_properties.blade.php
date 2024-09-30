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
            url: "{{ route('admin.main_properties_content') }}",
            dataType: 'html',
            success : function(data) {
                data = JSON.parse(data);
                $('#properties_content').html(data.html);
            }
        });
    });
</script>

<input type="hidden" id="invalid_password_repeat" value="{{__('text.admin_common_form_invalid_password_repeat')}}">

@endsection