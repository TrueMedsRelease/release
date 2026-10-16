{{-- Превью логина design_9, шаг 3: код из почты (2FA). --}}
@extends($design . '.cabinet.login-layout')

@section('cab_content')
    @include('cabinet-parts.logincode-body')
@endsection
