@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@php
    $autoPrefix = __('text.chat_browse_first_letter');
    if ($autoPrefix === 'text.chat_browse_first_letter') $autoPrefix = 'Products starting with';
    $autoSlug  = trim($letter);
    $autoLabel = trim($autoPrefix . ' ' . ucwords(str_replace(['-', '_'], ' ', $autoSlug)));
@endphp
<script>
    window.design17AutoBrowse = { type: 'first_letter', slug: @json($autoSlug), label: @json($autoLabel) };
</script>

@section('content')
    <div class="thread-chat js-chat-thread-wrap">

    </div>
@endsection