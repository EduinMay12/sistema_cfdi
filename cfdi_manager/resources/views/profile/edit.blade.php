@extends('layouts.admin')

@section('title', 'Mi perfil | CFDI Manager')
@section('page-title', 'Mi perfil')

@section('content')

<div class="container-fluid px-0 executive-profile">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">

        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-person-gear me-1"></i>
                CFDI MANAGER / MI CUENTA
            </span>

            <h2 class="premium-page-title">
                Configuración de perfil
            </h2>

            <p class="text-body-secondary mb-0">
                Administra tu información personal, seguridad
                y configuración de cuenta.
            </p>
        </div>

        <div class="premium-header-icon">
            <i class="bi bi-person-circle"></i>
        </div>

    </div>

    {{-- RESUMEN DEL USUARIO --}}
    <div class="content-card profile-identity-card mb-4">

        <div class="profile-identity-avatar">
            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
        </div>

        <div class="profile-identity-details">

            <span class="premium-section-label">
                CUENTA ACTIVA
            </span>

            <h4 class="fw-bold mb-1">
                {{ auth()->user()->name }}
            </h4>

            <p class="text-body-secondary mb-2">
                <i class="bi bi-envelope me-1"></i>
                {{ auth()->user()->email }}
            </p>

            <span class="profile-account-badge">
                <i class="bi bi-person-check me-1"></i>
                Usuario registrado
            </span>

        </div>

    </div>

    <div class="row g-4">

        {{-- INFORMACIÓN PERSONAL --}}
        <div class="col-12 col-xl-6">

            <div class="content-card profile-settings-card h-100">

                <div class="profile-section-heading">

                    <span class="profile-section-icon profile-icon-blue">
                        <i class="bi bi-person-vcard"></i>
                    </span>

                    <div>
                        <span class="premium-section-label">
                            DATOS PERSONALES
                        </span>

                        <h5 class="fw-bold mb-1">
                            Información del perfil
                        </h5>

                        <p class="text-body-secondary small mb-0">
                            Actualiza tu nombre y correo electrónico.
                        </p>
                    </div>

                </div>

                <div class="profile-section-body">
                    @include('profile.partials.update-profile-information-form')
                </div>

            </div>

        </div>

        {{-- SEGURIDAD --}}
        <div class="col-12 col-xl-6">

            <div class="content-card profile-settings-card h-100">

                <div class="profile-section-heading">

                    <span class="profile-section-icon profile-icon-green">
                        <i class="bi bi-shield-lock"></i>
                    </span>

                    <div>
                        <span class="premium-section-label">
                            SEGURIDAD DE ACCESO
                        </span>

                        <h5 class="fw-bold mb-1">
                            Cambiar contraseña
                        </h5>

                        <p class="text-body-secondary small mb-0">
                            Mantén protegida tu cuenta.
                        </p>
                    </div>

                </div>

                <div class="profile-section-body">
                    @include('profile.partials.update-password-form')
                </div>

            </div>

        </div>

        {{-- ELIMINAR CUENTA --}}
        <div class="col-12">

            <div class="content-card profile-danger-card">

                <div class="profile-section-heading">

                    <span class="profile-section-icon profile-icon-red">
                        <i class="bi bi-exclamation-triangle"></i>
                    </span>

                    <div>
                        <span class="premium-section-label">
                            ZONA DE SEGURIDAD
                        </span>

                        <h5 class="fw-bold mb-1">
                            Eliminar cuenta
                        </h5>

                        <p class="text-body-secondary small mb-0">
                            Esta acción es irreversible.
                            Revisa sus consecuencias antes de continuar.
                        </p>
                    </div>

                </div>

                <div class="profile-section-body">
                    @include('profile.partials.delete-user-form')
                </div>

            </div>

        </div>

    </div>

</div>

@endsection