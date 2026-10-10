@extends('layouts.admin')

@section('title', 'Dashboard Ejecutivo | CFDI Manager')
@section('page-title', 'Dashboard Ejecutivo')

@section('content')

<div class="container-fluid px-0 executive-dashboard premium-dashboard">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">

        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-graph-up-arrow me-1"></i>
                CFDI MANAGER / FINANCIAL SUITE
            </span>

            <h2 class="premium-page-title">
                Resumen ejecutivo
            </h2>

            <p class="text-body-secondary mb-0">
                Inteligencia fiscal y contable · Ejercicio {{ $year }}
            </p>
        </div>

        <div class="premium-header-icon">
            <i class="bi bi-bar-chart-line"></i>
        </div>

    </div>

    {{-- FILTROS --}}
    <div class="content-card premium-filter-card mb-4">

        <form method="GET"
              action="{{ route('dashboard') }}"
              class="row g-3 align-items-end">

            <div class="col-lg-7 col-md-6">

                <label class="form-label">
                    <i class="bi bi-building me-1"></i>
                    Contribuyente
                </label>

                <select name="contribuyente_id" class="form-select">
                    <option value="">Todos los contribuyentes</option>

                    @foreach($contribuyentes as $contribuyente)
                        <option value="{{ $contribuyente->id }}"
                            @selected($contribuyenteId == $contribuyente->id)>
                            {{ $contribuyente->razon_social }}
                            — {{ $contribuyente->rfc }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="col-lg-3 col-md-3">

                <label class="form-label">
                    <i class="bi bi-calendar3 me-1"></i>
                    Ejercicio fiscal
                </label>

                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @selected($year == $y)>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

            </div>

            <div class="col-lg-2 col-md-3">

                <button type="submit"
                        class="btn btn-primary w-100 premium-filter-button">
                    <i class="bi bi-funnel me-1"></i>
                    Aplicar
                </button>

            </div>

        </form>
    </div>

    {{-- INDICADORES --}}
    @php
        $cards = [
            [
                'label' => 'CFDI emitidos',
                'value' => number_format($resumen['emitidos']),
                'icon' => 'bi-file-earmark-arrow-up',
                'accent' => 'emerald',
                'description' => 'Comprobantes de emisión'
            ],
            [
                'label' => 'CFDI recibidos',
                'value' => number_format($resumen['recibidos']),
                'icon' => 'bi-file-earmark-arrow-down',
                'accent' => 'blue',
                'description' => 'Comprobantes recibidos'
            ],
            [
                'label' => 'Ingresos documentados',
                'value' => '$' . number_format($resumen['ingresos'], 2),
                'icon' => 'bi-graph-up-arrow',
                'accent' => 'emerald',
                'description' => 'CFDI de ingreso emitidos'
            ],
            [
                'label' => 'Gastos documentados',
                'value' => '$' . number_format($resumen['egresos'], 2),
                'icon' => 'bi-wallet2',
                'accent' => 'amber',
                'description' => 'CFDI de ingreso recibidos'
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">

        @foreach($cards as $card)

            <div class="col-xl-3 col-md-6">

                <div class="content-card premium-kpi
                            premium-kpi-{{ $card['accent'] }} h-100">

                    <div class="d-flex align-items-start
                                justify-content-between gap-2">

                        <div class="premium-kpi-label">
                            {{ $card['label'] }}
                        </div>

                        <div class="premium-kpi-icon">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>

                    </div>

                    <div class="premium-kpi-value text-break">
                        {{ $card['value'] }}
                    </div>

                    <div class="premium-kpi-footer">
                        <span>{{ $card['description'] }}</span>
                        <span>{{ $year }}</span>
                    </div>

                </div>

            </div>

        @endforeach

    </div>

    {{-- GRÁFICA Y BALANCE --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-8">

            <div class="content-card premium-panel h-100">

                <div class="premium-panel-header">

                    <div>
                        <span class="premium-section-label">
                            ANÁLISIS DE OPERACIONES
                        </span>

                        <h5 class="fw-bold mb-1">
                            Actividad mensual de CFDI
                        </h5>

                        <p class="text-body-secondary small mb-0">
                            Comprobantes por mes de emisión
                        </p>
                    </div>

                    <div class="premium-panel-icon">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                </div>

                <div class="premium-chart-container">
                    <canvas id="chartActividad"></canvas>
                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="content-card premium-panel h-100">

                <div class="premium-panel-header">

                    <div>
                        <span class="premium-section-label">
                            RESUMEN FINANCIERO
                        </span>

                        <h5 class="fw-bold mb-0">
                            Balance documental
                        </h5>
                    </div>

                    <div class="premium-panel-icon">
                        <i class="bi bi-calculator"></i>
                    </div>

                </div>

                <p class="text-body-secondary small mt-3">
                    Diferencia entre ingresos y gastos documentados.
                </p>

                <div class="premium-balance-amount
                    {{ $resumen['diferencia'] >= 0
                        ? 'text-success'
                        : 'text-danger' }}">

                    ${{ number_format($resumen['diferencia'], 2) }}

                </div>

                <div class="premium-balance-divider"></div>

                <div class="premium-balance-row">
                    <span>Total CFDI</span>
                    <strong>
                        {{ number_format($resumen['total_cfdis']) }}
                    </strong>
                </div>

                <div class="premium-balance-row">
                    <span>Emitidos</span>
                    <strong>
                        {{ number_format($resumen['emitidos']) }}
                    </strong>
                </div>

                <div class="premium-balance-row">
                    <span>Recibidos</span>
                    <strong>
                        {{ number_format($resumen['recibidos']) }}
                    </strong>
                </div>

                <div class="premium-accounting-note">
                    <i class="bi bi-info-circle"></i>

                    <span>
                        Indicador documental. No representa
                        utilidad fiscal, impuestos determinados
                        ni saldo bancario.
                    </span>
                </div>

            </div>

        </div>

    </div>

    {{-- SOLICITUDES SAT --}}
    <div class="content-card premium-panel">

        <div class="premium-panel-header mb-4">

            <div>
                <span class="premium-section-label">
                    OPERACIONES FISCALES
                </span>

                <h5 class="fw-bold mb-1">
                    Solicitudes SAT recientes
                </h5>

                <p class="text-body-secondary small mb-0">
                    Seguimiento de las últimas solicitudes registradas
                </p>
            </div>

            <a href="{{ route('solicitudes.index') }}"
               class="btn btn-outline-primary btn-sm">

                <i class="bi bi-arrow-up-right me-1"></i>
                Ver todas

            </a>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle premium-table">

                <thead>
                    <tr>
                        <th>Contribuyente</th>
                        <th>Tipo</th>
                        <th>Periodo solicitado</th>
                        <th>Estado</th>
                        <th>Fecha de registro</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($solicitudes as $solicitud)

                        @php
                            $estado = strtolower($solicitud->estado ?? '');

                            $badge = match($estado) {
                                'terminada', 'procesada', 'completada'
                                    => 'success',

                                'pendiente', 'solicitada'
                                    => 'warning',

                                'revision', 'enviando'
                                    => 'info',

                                'error', 'rechazada'
                                    => 'danger',

                                default => 'secondary'
                            };
                        @endphp

                        <tr>

                            <td>
                                <div class="fw-semibold">
                                    {{ $solicitud->contribuyente->razon_social ?? 'N/D' }}
                                </div>

                                <div class="small text-body-secondary">
                                    {{ $solicitud->contribuyente->rfc ?? 'N/D' }}
                                </div>
                            </td>

                            <td>
                                <span class="premium-type-label">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    {{ ucfirst($solicitud->tipo) }}
                                </span>
                            </td>

                            <td class="small">
                                {{ $solicitud->fecha_inicio }}
                                <span class="text-body-secondary mx-1">—</span>
                                {{ $solicitud->fecha_fin }}
                            </td>

                            <td>
                                <span class="badge text-bg-{{ $badge }}">
                                    {{ ucfirst($solicitud->estado) }}
                                </span>
                            </td>

                            <td class="small text-body-secondary">
                                {{ $solicitud->created_at?->format('d/m/Y H:i') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-5">

                                <div class="premium-empty-state">

                                    <i class="bi bi-inbox"></i>

                                    <h6>No hay solicitudes registradas</h6>

                                    <p>
                                        Las solicitudes al SAT aparecerán aquí
                                        cuando comiences a utilizarlas.
                                    </p>

                                    <a href="{{ route('solicitudes.index') }}"
                                       class="btn btn-primary btn-sm">
                                        Gestionar solicitudes
                                    </a>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const canvas = document.getElementById('chartActividad');

    if (!canvas || !window.Chart) {
        return;
    }

    const labels = @json($grafica['labels']);
    const values = @json($grafica['cfdis']);

    const chart = new window.Chart(canvas, {
        type: 'bar',

        data: {
            labels,

            datasets: [{
                label: 'CFDI registrados',
                data: values,
                backgroundColor: 'rgba(15, 118, 110, 0.78)',
                hoverBackgroundColor: '#0d9488',
                borderRadius: 7,
                maxBarThickness: 38
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    backgroundColor: '#0b1628',
                    padding: 12,
                    cornerRadius: 9
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(148,163,184,.12)'
                    }
                },

                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    const themeObserver = new MutationObserver(() => {
        const dark = document.documentElement
            .getAttribute('data-bs-theme') === 'dark';

        chart.options.scales.x.ticks.color =
            dark ? '#cbd5e1' : '#64748b';

        chart.options.scales.y.ticks.color =
            dark ? '#cbd5e1' : '#64748b';

        chart.update();
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });

});
</script>
@endpush