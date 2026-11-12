@extends('admin.layouts.app')
@section('title', 'Admin sign in')
@section('content')
<div class="admin-login"><span class="eyebrow">Secure administration</span><h1>Sign in</h1>
    <form method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></div>
        <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required autocomplete="current-password"></div>
        <div class="form-check mb-3"><input class="form-check-input" id="remember" type="checkbox" name="remember" value="1"><label class="form-check-label" for="remember">Remember me</label></div>
        <button class="button button-dark w-100" type="submit">Sign in</button>
    </form>
</div>
@endsection
