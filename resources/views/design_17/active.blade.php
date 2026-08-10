@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@php
    $browsePrefix = __('text.chat_browse_active');
    if ($browsePrefix === 'text.chat_browse_active') {
        $browsePrefix = 'Show products with active ingredient';
    }
    $browseLabel = trim($browsePrefix . ' ' . ucwords(str_replace(['-', '_'], ' ', $active)));
@endphp
<script>
    window.design17AutoBrowse = {
        type: 'active',
        slug: @json($active),
        label: @json($browseLabel)
    };
</script>

@section('content')
    <div class="thread-chat js-chat-thread-wrap">

    </div>
@endsection