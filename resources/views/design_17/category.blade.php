@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@php
    $autoPrefix = __('text.chat_browse_category');
    if ($autoPrefix === 'text.chat_browse_category') $autoPrefix = 'Show products from category';
    $autoSlug  = str_replace(' ', '-', trim($cur_category));
    $autoLabel = trim($autoPrefix . ' ' . ucwords(str_replace(['-', '_'], ' ', $autoSlug)));
@endphp
<script>
    window.design17AutoBrowse = { type: 'category', slug: @json($autoSlug), label: @json($autoLabel) };
</script>

@section('content')
    <div class="thread-chat js-chat-thread-wrap">

    </div>
@endsection