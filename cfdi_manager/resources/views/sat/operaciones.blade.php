@extends('layouts.admin')

@section('title', 'Centro de Operaciones SAT | CFDI Manager')
@section('page-title', 'Operaciones SAT')

@section('content')

@php
    $cards = [
        [
            'titulo' => 'Solicitudes registradas',
            'campo' => 'solicitudes',
            'icono' => 'bi-file-earmark-text',
            'color' => 'blue',
            'descripcion' => 'Total de solicitudes'
        ],
        [
            'titulo' => 'En seguimiento',
            'campo' => 'seguimiento',
            'icono' => 'bi-arrow-repeat',
            'color' => 'amber',
            'descripcion' => 'Operaciones en curso'
        ],
        [
            'titulo' => 'Paquetes SAT',
            'campo' => 'paquetes',
            'icono' => 'bi-file-earmark-zip',
            'color' => 'purple',
            'descripcion' => 'Paquetes registrados'
        ],
        [
            'titulo' => 'Procesados',
            'campo' => 'procesados',
            'icono' => 'bi-check-circle',
            'color' => 'green',
            'descripcion' => 'Operaciones completadas'
        ],
    ];
@endphp

<div class="container-fluid px-0 executive-sat-center">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">

        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-hdd-network me-1"></i>
                CFDI MANAGER / CONTROL FISCAL
            </span>

            <h2 class="premium-page-title">
                Centro de Operaciones SAT
            </h2>

            <p class="text-body-secondary mb-0">
                Supervisa solicitudes, estados de procesamiento
                y paquetes fiscales.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.index') }}"
            class="btn btn-primary sat-center-primary-button"
        >
            <i class="bi bi-plus-circle me-2"></i>
            Gestionar solicitudes
        </a>

    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row g-3 mb-4">

        @foreach($cards as $card)

            <div class="col-md-6 col-xl-3">

                <div class="content-card sat-center-stat h-100">

                    <div class="d-flex justify-content-between
                                align-items-start gap-3">

                        <div>
                            <span class="sat-center-stat-label">
                                {{ $card['titulo'] }}
                            </span>

                            <h2 class="sat-center-stat-number">
                                {{ number_format($estadisticas[$card['campo']] ?? 0) }}
                            </h2>

                            <span class="sat-center-stat-description">
                                {{ $card['descripcion'] }}
                            </span>
                        </div>

                        <span class="sat-center-stat-icon
                                     sat-center-stat-{{ $card['color'] }}">
                            <i class="bi {{ $card['icono'] }}"></i>
                        </span>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    {{-- INFORMACIÓN DEL CENTRO --}}
    <div class="sat-center-info mb-4">

        <div class="sat-center-info-icon">
            <i class="bi bi-shield-check"></i>
        </div>

        <div>
            <strong>
                Seguimiento de operaciones fiscales
            </strong>

            <p class="mb-0">
                Los estados mostrados corresponden a la información
                registrada en CFDI Manager. Para obtener resultados
                actualizados del SAT, utiliza la opción de verificación
                en el módulo de solicitudes.
            </p>
        </div>

        <a
            href="{{ route('solicitudes.index') }}"
            class="sat-center-info-link"
        >
            Ir a solicitudes
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>

    {{-- HISTORIAL --}}
    <div class="content-card sat-center-panel">

        <div class="sat-center-panel-heading mb-4">

            <div>
                <span class="premium-section-label">
                    MONITOREO Y SEGUIMIENTO
                </span>

                <h5 class="fw-bold mb-1">
                    Historial de operaciones
                </h5>

                <p class="text-body-secondary small mb-0">
                    Consulta el estado de las solicitudes y los paquetes asociados.
                </p>
            </div>

            <button
                type="button"
                id="btnActualizarOperaciones"
                class="btn btn-outline-secondary sat-center-refresh-button"
            >
                <i class="bi bi-arrow-clockwise me-1"></i>
                Actualizar
            </button>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle sat-center-table">

                <thead>
                    <tr>
                        <th>RFC</th>
                        <th>Identificador SAT</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Paquetes</th>
                        <th>Última revisión</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($solicitudes as $solicitud)

                        @php
                            $estado = strtolower($solicitud->estado ?? '');

                            $estadoClase = match($estado) {
                                'procesada',
                                'terminada',
                                'completada',
                                'descargada' => 'completed',

                                'pendiente',
                                'solicitada' => 'pending',

                                'revision',
                                'enviando',
                                'en_proceso',
                                'procesando',
                                'aceptada' => 'processing',

                                'error',
                                'rechazada',
                                'fallida' => 'failed',

                                default => 'neutral'
                            };
                        @endphp

                        <tr>

                            {{-- RFC --}}
                            <td>
                                <div class="sat-center-taxpayer">

                                    <span class="sat-center-taxpayer-icon">
                                        <i class="bi bi-building"></i>
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $solicitud->contribuyente->rfc ?? 'N/D' }}
                                        </strong>

                                        <small>
                                            Contribuyente
                                        </small>
                                    </div>

                                </div>
                            </td>

                            {{-- IDENTIFICADOR SAT --}}
                            <td>
                                @if($solicitud->solicitud_sat_id)

                                    <span
                                        class="sat-center-request-id"
                                        title="{{ $solicitud->solicitud_sat_id }}"
                                    >
                                        {{ $solicitud->solicitud_sat_id }}
                                    </span>

                                @else

                                    <span class="sat-center-pending-id">
                                        <i class="bi bi-clock me-1"></i>
                                        Sin identificador
                                    </span>

                                @endif
                            </td>

                            {{-- TIPO --}}
                            <td>
                                <span class="sat-center-type">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    {{ ucfirst($solicitud->tipo ?? 'N/D') }}
                                </span>
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                <span class="sat-center-status
                                             sat-center-status-{{ $estadoClase }}">

                                    <span class="sat-center-status-dot"></span>

                                    {{ ucfirst(str_replace('_', ' ', $estado ?: 'desconocido')) }}

                                </span>
                            </td>

                            {{-- PAQUETES --}}
                            <td>
                                <span class="sat-center-packages">
                                    <i class="bi bi-file-earmark-zip"></i>
                                    {{ $solicitud->paquetes->count() }}
                                </span>
                            </td>

                            {{-- ÚLTIMA REVISIÓN --}}
                            <td>
                                @if($solicitud->ultima_verificacion)

                                    <span class="sat-center-date">
                                        <i class="bi bi-calendar3 me-1"></i>

                                        {{ \Carbon\Carbon::parse(
                                            $solicitud->ultima_verificacion
                                        )->format('d/m/Y H:i') }}
                                    </span>

                                @else

                                    <span class="sat-center-no-review">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Sin verificar
                                    </span>

                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-end">

                                <a
                                    href="{{ route('solicitudes.index') }}"
                                    class="btn sat-center-action-button"
                                    title="Gestionar solicitud"
                                >
                                    <i class="bi bi-arrow-up-right-square"></i>
                                    Gestionar
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <div class="sat-center-empty">

                                    <span class="sat-center-empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </span>

                                    <h6 class="fw-bold mt-3">
                                        Sin operaciones registradas
                                    </h6>

                                    <p class="text-body-secondary small mb-3">
                                        Las solicitudes registradas aparecerán
                                        en este historial.
                                    </p>

                                    <a
                                        href="{{ route('solicitudes.index') }}"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="bi bi-plus-lg me-1"></i>
                                        Crear solicitud
                                    </a>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINACIÓN LARAVEL --}}
        @if($solicitudes->hasPages())

            <div class="sat-center-pagination mt-4">

                <div class="text-body-secondary small">
                    Mostrando
                    {{ $solicitudes->firstItem() }}
                    –
                    {{ $solicitudes->lastItem() }}
                    de
                    {{ $solicitudes->total() }}
                    operaciones
                </div>

                <div>
                    {{ $solicitudes->links() }}
                </div>

            </div>

        @endif

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const refreshButton = document.getElementById(
        'btnActualizarOperaciones'
    );

    refreshButton?.addEventListener('click', function () {

        this.disabled = true;

        this.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  aria-hidden="true"></span>
            Actualizando...
        `;

        window.location.reload();

    });

});
</script>
@endpush