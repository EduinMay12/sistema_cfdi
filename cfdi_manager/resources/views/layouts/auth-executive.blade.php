<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Acceso') | CFDI Manager
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
</head>

<body class="auth-executive-body">

    <main class="auth-executive-layout">

        {{-- Panel corporativo --}}
        <aside class="auth-brand-panel">

            <div class="auth-brand-content">

                <a href="{{ url('/') }}"
                   class="auth-brand-logo">

                    <span class="auth-brand-logo-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </span>

                    <span>
                        <strong>CFDI Manager</strong>
                        <small>FINANCIAL SUITE</small>
                    </span>
                </a>

                <div class="auth-brand-main">

                    <span class="auth-brand-eyebrow">
                        PLATAFORMA DE GESTIÓN FISCAL
                    </span>

                    <h1>
                        Control fiscal.
                        <span>Visión ejecutiva.</span>
                    </h1>

                    <p>
                        Centraliza tus CFDI, administra tus
                        contribuyentes y supervisa tus
                        operaciones fiscales desde una
                        plataforma diseñada para tu negocio.
                    </p>

                    <div class="auth-feature-list">

                        <div>
                            <i class="bi bi-check-circle-fill"></i>
                            Gestión centralizada de CFDI
                        </div>

                        <div>
                            <i class="bi bi-check-circle-fill"></i>
                            Seguimiento de solicitudes SAT
                        </div>

                        <div>
                            <i class="bi bi-check-circle-fill"></i>
                            Indicadores ejecutivos contables
                        </div>

                    </div>

                    <div class="auth-brand-preview">

                        <div class="auth-preview-heading">
                            <span>
                                <i class="bi bi-bar-chart-line me-2"></i>
                                PANEL EJECUTIVO
                            </span>

                            <i class="bi bi-three-dots"></i>
                        </div>

                        <div class="auth-preview-grid">

                            <div class="auth-preview-stat">
                                <small>Comprobantes</small>
                                <strong>
                                    <i class="bi bi-file-earmark-check"></i>
                                    CFDI
                                </strong>
                            </div>

                            <div class="auth-preview-stat">
                                <small>Operaciones</small>
                                <strong>
                                    <i class="bi bi-cloud-check"></i>
                                    SAT
                                </strong>
                            </div>

                            <div class="auth-preview-stat">
                                <small>Análisis</small>
                                <strong>
                                    <i class="bi bi-graph-up"></i>
                                    Reportes
                                </strong>
                            </div>

                        </div>

                        <div class="auth-preview-bars">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                    </div>

                </div>

                <div class="auth-brand-footer">
                    © {{ date('Y') }} CFDI Manager
                    <span>Plataforma de administración fiscal</span>
                </div>

            </div>

        </aside>

        {{-- Formulario --}}
        <section class="auth-form-panel">

            <div class="auth-form-container">

                <div class="auth-mobile-brand">
                    <i class="bi bi-graph-up-arrow"></i>
                    CFDI Manager
                </div>

                @yield('content')

                <div class="auth-form-footer">
                    <i class="bi bi-shield-lock me-1"></i>
                    Acceso protegido
                </div>

            </div>

        </section>

    </main>

    @stack('scripts')

</body>
</html>