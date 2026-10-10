<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>@yield('title', 'CFDI Manager')</title>

    {{-- Aplicar tema antes de renderizar --}}
    <script>
        (function () {
            const savedTheme = localStorage.getItem('cfdi-theme');

            document.documentElement.setAttribute(
                'data-bs-theme',
                savedTheme === 'dark' ? 'dark' : 'light'
            );
        })();
    </script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>

<body class="executive-admin-body">

    {{-- LOADER EJECUTIVO --}}
    <div id="executiveLoader"
         role="status"
         aria-live="polite"
         aria-label="Cargando información">

        <div class="executive-loader-content">

            <div class="executive-loader-brand">
                <i class="bi bi-graph-up-arrow"></i>
                <span>CFDI Manager</span>
            </div>

            <div class="executive-loader-subtitle">
                FINANCIAL SUITE
            </div>

            <div class="executive-loader-document">

                <div class="executive-loader-line short"></div>
                <div class="executive-loader-line"></div>
                <div class="executive-loader-line"></div>

                <div class="executive-loader-chart">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

            </div>

            <div class="executive-loader-title">
                Preparando tu centro financiero
            </div>

            <div class="executive-loader-message">
                Organizando información contable...
            </div>

            <div class="executive-loader-progress"></div>

        </div>

    </div>

    {{-- SIDEBAR EXISTENTE --}}
    @include('components.sidebar')

    {{-- OVERLAY MÓVIL --}}
    <div id="sidebarOverlay"
         class="sidebar-overlay"></div>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="admin-main executive-main">

        {{-- NAVBAR EJECUTIVO --}}
        <header class="admin-navbar executive-topbar">

            <div class="d-flex align-items-center gap-3">

                {{-- BOTÓN MÓVIL --}}
                <button
                    id="sidebarToggle"
                    class="btn executive-icon-button d-lg-none"
                    type="button"
                    aria-label="Abrir menú"
                    aria-expanded="false"
                >
                    <i class="bi bi-list fs-5"></i>
                </button>

                {{-- IDENTIDAD DEL MÓDULO --}}
                <div class="executive-topbar-heading">

                    <div class="executive-topbar-eyebrow">
                        CFDI MANAGER
                        <span class="mx-1">/</span>
                        FINANCIAL SUITE
                    </div>

                    <h5 class="mb-0 fw-bold">
                        @yield('page-title', 'Dashboard')
                    </h5>

                </div>

            </div>

            {{-- ACCIONES --}}
            <div class="d-flex align-items-center gap-2 gap-md-3">

                {{-- ESTADO DEL SISTEMA --}}
                <div class="executive-system-status d-none d-xl-flex">
                    <span class="executive-status-dot"></span>
                    Plataforma disponible
                </div>

                {{-- TEMA --}}
                <button
                    id="themeToggle"
                    class="btn executive-icon-button"
                    type="button"
                    aria-label="Cambiar tema"
                    title="Cambiar apariencia"
                >
                    <i id="themeIcon" class="bi bi-moon-stars"></i>
                </button>

                {{-- PERFIL --}}
                <div class="dropdown">

                    <button
                        class="executive-profile-button dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        <span class="executive-avatar">
                            {{ strtoupper(
                                mb_substr(auth()->user()->name, 0, 1)
                            ) }}
                        </span>

                        <span class="executive-profile-info d-none d-sm-flex">

                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                            <small>
                                Cuenta administrativa
                            </small>

                        </span>

                        <i class="bi bi-chevron-down
                                  executive-profile-chevron"></i>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end
                               executive-profile-menu">

                        <li class="px-3 py-2">

                            <div class="fw-semibold small">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="text-body-secondary small text-break">
                                {{ auth()->user()->email }}
                            </div>

                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="{{ route('profile.edit') }}">

                                <i class="bi bi-person-gear me-2"></i>
                                Mi perfil

                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>

                            <form method="POST"
                                  action="{{ route('logout') }}">

                                @csrf

                                <button
                                    type="submit"
                                    class="dropdown-item text-danger">

                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Cerrar sesión

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </div>

        </header>

        {{-- CONTENIDO DE LAS VISTAS --}}
        <section class="admin-content executive-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Cerrar">
                    </button>

                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"
                     role="alert">

                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Cerrar">
                    </button>

                </div>
            @endif

            @yield('content')

            {{-- FOOTER --}}
            <footer class="executive-footer">

                <span>
                    © {{ date('Y') }} CFDI Manager
                </span>

                <span>
                    Financial Suite · Gestión fiscal y contable
                </span>

            </footer>

        </section>

    </main>

    @stack('scripts')


    <script>
    (function () {

        function ocultarLoaderInicial() {
            if (typeof window.ocultarLoaderSat === 'function') {
                window.ocultarLoaderSat();
            }
        }

        if (document.readyState === 'complete') {
            ocultarLoaderInicial();
        } else {
            window.addEventListener(
                'load',
                ocultarLoaderInicial,
                { once: true }
            );
        }

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                ocultarLoaderInicial();
            }
        });

    })();
    </script>
    <script>
    (function () {

        const loader = document.getElementById('executiveLoader');
        const themeIcon = document.getElementById('themeIcon');

        // Actualizar el icono según el tema actual.
        function actualizarIconoTema() {
            if (!themeIcon) return;

            const dark =
                document.documentElement.getAttribute('data-bs-theme')
                === 'dark';

            themeIcon.className = dark
                ? 'bi bi-sun'
                : 'bi bi-moon-stars';
        }

        actualizarIconoTema();

        // Observa cambios realizados por el controlador
        // de temas existente en app.js.
        const observer = new MutationObserver(actualizarIconoTema);

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });

        // Mantener el loader disponible para operaciones SAT.
        function ocultarLoader() {
            if (!loader) return;

            loader.classList.add('loader-hidden');
            loader.setAttribute('aria-hidden', 'true');
        }

        if (document.readyState === 'complete') {
            ocultarLoader();
        } else {
            window.addEventListener('load', ocultarLoader, {
                once: true
            });
        }

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                ocultarLoader();
            }
        });

        // API reutilizable para peticiones AJAX.
        window.mostrarLoaderSat = function (
            mensaje = 'Procesando operación SAT...'
        ) {
            if (!loader) return;

            const message = loader.querySelector(
                '.executive-loader-message'
            );

            if (message) {
                message.textContent = mensaje;
            }

            loader.setAttribute('aria-hidden', 'false');
            loader.classList.remove('loader-hidden');
        };

        window.ocultarLoaderSat = ocultarLoader;

    })();
    </script>

</body>
</html>