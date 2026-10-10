@extends('layouts.auth-executive')

@section('title', 'Crear cuenta')

@section('content')

<div class="auth-heading">

    <span class="auth-heading-tag">
        <i class="bi bi-person-plus me-1"></i>
        NUEVA CUENTA
    </span>

    <h2>Comienza con CFDI Manager</h2>

    <p>
        Crea tu cuenta y comienza a administrar
        tus comprobantes fiscales.
    </p>

</div>

<form
    method="POST"
    action="{{ route('register') }}"
    class="auth-executive-form"
>
    @csrf

    {{-- Nombre --}}
    <div class="mb-3">

        <label for="name" class="auth-label">
            Nombre completo
        </label>

        <div class="auth-input-group">

            <i class="bi bi-person"></i>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Nombre y apellidos"
                autocomplete="name"
                required
                autofocus
                class="auth-input @error('name') is-invalid @enderror"
            >

        </div>

        @error('name')
            <div class="auth-error">{{ $message }}</div>
        @enderror

    </div>

    {{-- Email --}}
    <div class="mb-3">

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
                autocomplete="email"
                required
                class="auth-input @error('email') is-invalid @enderror"
            >

        </div>

        @error('email')
            <div class="auth-error">{{ $message }}</div>
        @enderror

    </div>

    {{-- Contraseña --}}
    <div class="mb-3">

        <label for="password" class="auth-label">
            Contraseña
        </label>

        <div class="auth-input-group">

            <i class="bi bi-lock"></i>

            <input
                id="password"
                type="password"
                name="password"
                placeholder="Crea una contraseña segura"
                autocomplete="new-password"
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
            <div class="auth-error">{{ $message }}</div>
        @enderror

    </div>

    {{-- Confirmación --}}
    <div class="mb-3">

        <label for="password_confirmation" class="auth-label">
            Confirmar contraseña
        </label>

        <div class="auth-input-group">

            <i class="bi bi-shield-lock"></i>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="Repite tu contraseña"
                autocomplete="new-password"
                required
                class="auth-input"
            >

            <button
                type="button"
                class="auth-password-toggle"
                data-toggle-password="password_confirmation"
                aria-label="Mostrar contraseña"
                aria-pressed="false"
            >
                <i class="bi bi-eye"></i>
            </button>

        </div>

    </div>

    <div class="auth-security-note">
        <i class="bi bi-shield-check"></i>
        <span>
            Tu contraseña se almacenará de forma segura.
            Nunca compartas tu e.firma ni su contraseña.
        </span>
    </div>

    <button
        type="submit"
        class="auth-submit-button"
    >
        <span>Crear mi cuenta</span>
        <i class="bi bi-arrow-right"></i>
    </button>

</form>

<div class="auth-switch">

    ¿Ya tienes una cuenta?

    <a href="{{ route('login') }}">
        Iniciar sesión
    </a>

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

            this.setAttribute('aria-pressed', String(visible));

            this.setAttribute(
                'aria-label',
                visible ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );

        });

    });
</script>
@endpush

@endsection