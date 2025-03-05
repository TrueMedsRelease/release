@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<iframe width="100%" height="100%" src="https://pill-365.com/?parent_domain={{ request()->getHost() }}" style="position: fixed; z-index: 1000000; top: 0; left: 0;">

</iframe>
@endsection