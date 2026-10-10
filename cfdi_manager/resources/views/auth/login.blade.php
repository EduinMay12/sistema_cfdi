@extends('layouts.auth-executive')

@section('title', 'Iniciar sesión')

@section('content')

<div class="auth-heading">

    <span class="auth-heading-tag">
        <i class="bi bi-shield-check me-1"></i>
        ACCESO CORPORATIVO
    </span>

    <h2>Bienvenido de nuevo</h2>

    <p>
        Ingresa tus credenciales para acceder
        a tu centro de gestión contable.
    </p>

</div>

@if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('login') }}"
    class="auth-executive-form"
>
    @csrf

    <div class="mb-4">

        <label for="email" class="auth-label">
            Correo electrónico
        </label>

        <div class="auth-input-group">

            <i class="bi bi-envelope"></i>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nombre@empresa.com"
                autocomplete="username"
                required
                autofocus
                class="auth-input @error('email') is-invalid @enderror"
            >

        </div>

        @error('email')
            <div class="auth-error">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="mb-3">

        <div class="d-flex justify-content-between
                    align-items-center mb-2">

            <label for="password" class="auth-label mb-0">
                Contraseña
            </label>

            @if(Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="auth-small-link"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

        </div>

        <div class="auth-input-group">

            <i class="bi bi-lock"></i>

            <input
                id="password"
                type="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                autocomplete="current-password"
                required
                class="auth-input @error('password') is-invalid @enderror"
            >

            <button
                type="button"
                class="auth-password-toggle"
                data-toggle-password="password"
                aria-label="Mostrar contraseña"
                aria-pressed="false"
            >
                <i class="bi bi-eye"></i>
            </button>

        </div>

        @error('password')
            <div class="auth-error">
                {{ $message }}
            </div>
        @enderror

    </div>

    <div class="auth-remember-row">

        <label class="auth-checkbox-label">

            <input
                type="checkbox"
                name="remember"
                value="1"
            >

            <span>Recordar mi sesión</span>

        </label>

    </div>

    <button
        type="submit"
        class="auth-submit-button"
    >
        <span>Iniciar sesión</span>
        <i class="bi bi-arrow-right"></i>
    </button>

</form>

<div class="auth-switch">

    ¿Aún no tienes una cuenta?

    @if(Route::has('register'))
        <a href="{{ route('register') }}">
            Crear cuenta
        </a>
    @endif

</div>

@push('scripts')
<script>
document.querySelectorAll('[data-toggle-password]')
    .forEach(button => {

        button.addEventListener('click', function () {

            const input = document.getElementById(
                this.dataset.togglePassword
            );

            if (!input) return;

            const visible = input.type === 'password';

            input.type = visible ? 'text' : 'password';

            this.querySelector('i').className = visible
                ? 'bi bi-eye-slash'
                : 'bi bi-eye';

            this.setAttribute(
                'aria-pressed',
                String(visible)
            );

            this.setAttribute(
                'aria-label',
                visible ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );

        });

    });
</script>
@endpush

@endsection