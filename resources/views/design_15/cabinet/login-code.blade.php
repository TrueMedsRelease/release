{{-- Превью логина design_10, шаг 3: код из почты (2FA). --}}
@extends($design . '.cabinet.login-layout')

@section('cab_content')
    @include('cabinet-parts.logincode-body')
@endsection
