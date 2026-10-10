<aside class="admin-sidebar executive-sidebar" id="adminSidebar">

    {{-- IDENTIDAD CORPORATIVA --}}
    <div class="executive-sidebar-header">

        <a href="{{ route('dashboard') }}"
           class="executive-sidebar-brand">

            <span class="executive-sidebar-logo">
                <i class="bi bi-graph-up-arrow"></i>
            </span>

            <span class="executive-sidebar-brand-text">
                <strong>CFDI Manager</strong>
                <small>FINANCIAL SUITE</small>
            </span>

        </a>

        {{-- CERRAR EN MÓVIL --}}
        <button
            type="button"
            id="sidebarClose"
            class="executive-sidebar-close d-lg-none"
            aria-label="Cerrar menú"
        >
            <i class="bi bi-x-lg"></i>
        </button>

    </div>

    {{-- CUERPO DEL SIDEBAR --}}
    <div class="executive-sidebar-body">

        <div class="executive-sidebar-section">
            <span class="executive-sidebar-label">
                GENERAL
            </span>

            <nav class="sidebar-menu" aria-label="Navegación principal">

                <a href="{{ route('dashboard') }}"
                   class="sidebar-link executive-sidebar-link
                   {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   @if(request()->routeIs('dashboard')) aria-current="page" @endif>

                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>

                </a>

            </nav>
        </div>

        <div class="executive-sidebar-section">

            <span class="executive-sidebar-label">
                GESTIÓN CONTABLE
            </span>

            <nav class="sidebar-menu" aria-label="Gestión contable">

                <a href="{{ route('contribuyentes.index') }}"
                   class="sidebar-link executive-sidebar-link
                   {{ request()->routeIs('contribuyentes.*') ? 'active' : '' }}"
                   @if(request()->routeIs('contribuyentes.*')) aria-current="page" @endif>

                    <i class="bi bi-buildings"></i>
                    <span>Contribuyentes</span>

                </a>

                <a href="{{ route('cfdis.index') }}"
                   class="sidebar-link executive-sidebar-link
                   {{ request()->routeIs('cfdis.*') ? 'active' : '' }}"
                   @if(request()->routeIs('cfdis.*')) aria-current="page" @endif>

                    <i class="bi bi-file-earmark-text"></i>
                    <span>Comprobantes CFDI</span>

                </a>

            </nav>
        </div>

        <div class="executive-sidebar-section">

            <span class="executive-sidebar-label">
                SERVICIOS FISCALES
            </span>

            <nav class="sidebar-menu" aria-label="Servicios fiscales">

                <a href="{{ route('solicitudes.index') }}"
                   class="sidebar-link executive-sidebar-link
                   {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}"
                   @if(request()->routeIs('solicitudes.*')) aria-current="page" @endif>

                    <i class="bi bi-cloud-arrow-down"></i>
                    <span>Solicitudes SAT</span>

                </a>

                <a href="{{ route('sat.operaciones') }}"
                   class="sidebar-link executive-sidebar-link
                   {{ request()->routeIs('sat.operaciones') ? 'active' : '' }}"
                   @if(request()->routeIs('sat.operaciones')) aria-current="page" @endif>

                    <i class="bi bi-hdd-network"></i>
                    <span>Operaciones SAT</span>

                </a>

            </nav>
        </div>

        {{-- ACCESO RÁPIDO --}}
        <div class="executive-sidebar-shortcut">

            <div class="executive-sidebar-shortcut-icon">
                <i class="bi bi-shield-check"></i>
            </div>

            <strong>Centro de control fiscal</strong>

            <p>
                Consulta solicitudes y administra
                tus operaciones SAT.
            </p>

            <a href="{{ route('sat.operaciones') }}">
                Ir a operaciones
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>

    </div>

    {{-- PERFIL INFERIOR --}}
    <div class="executive-sidebar-footer">

        <a href="{{ route('profile.edit') }}"
           class="executive-sidebar-user">

            <span class="executive-sidebar-avatar">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </span>

            <span class="executive-sidebar-user-info">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->email }}</small>
            </span>

            <i class="bi bi-gear"></i>

        </a>

        <div class="executive-sidebar-copyright">
            CFDI Manager · {{ date('Y') }}
        </div>

    </div>

</aside>