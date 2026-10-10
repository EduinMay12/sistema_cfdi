@extends('layouts.admin')

@section('title', 'Comprobantes CFDI | CFDI Manager')
@section('page-title', 'Gestión de CFDI')

@section('content')

@php
    $totalCfdis = $cfdis->count();
    $xmlDisponibles = $cfdis->filter(fn ($cfdi) => filled($cfdi->xml_path))->count();
    $sinXml = $totalCfdis - $xmlDisponibles;
@endphp

<div class="container-fluid px-0 executive-cfdis">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">
        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-file-earmark-code me-1"></i>
                CFDI MANAGER / GESTIÓN DOCUMENTAL
            </span>

            <h2 class="premium-page-title">
                Comprobantes fiscales
            </h2>

            <p class="text-body-secondary mb-0">
                Consulta, filtra y descarga comprobantes fiscales digitales.
            </p>
        </div>

        <div class="premium-header-icon">
            <i class="bi bi-files"></i>
        </div>
    </div>

    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="content-card cfdi-stat h-100">
                <span class="cfdi-stat-icon cfdi-stat-blue">
                    <i class="bi bi-files"></i>
                </span>

                <div>
                    <div class="text-body-secondary small">
                        CFDI registrados
                    </div>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($totalCfdis) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card cfdi-stat h-100">
                <span class="cfdi-stat-icon cfdi-stat-green">
                    <i class="bi bi-file-earmark-check"></i>
                </span>

                <div>
                    <div class="text-body-secondary small">
                        XML disponibles
                    </div>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($xmlDisponibles) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card cfdi-stat h-100">
                <span class="cfdi-stat-icon cfdi-stat-amber">
                    <i class="bi bi-file-earmark-x"></i>
                </span>

                <div>
                    <div class="text-body-secondary small">
                        Sin archivo XML
                    </div>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($sinXml) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- FILTROS --}}
    <div class="content-card cfdi-panel mb-4">

        <div class="cfdi-panel-heading mb-4">
            <div>
                <span class="premium-section-label">
                    CONSULTA DOCUMENTAL
                </span>

                <h5 class="fw-bold mb-1">
                    Filtrar comprobantes
                </h5>

                <p class="text-body-secondary small mb-0">
                    Selecciona un contribuyente y un periodo de emisión.
                </p>
            </div>

            <span class="cfdi-heading-icon">
                <i class="bi bi-funnel"></i>
            </span>
        </div>

        <form
            method="GET"
            action="{{ route('cfdis.index') }}"
            class="row g-3 align-items-end"
        >

            <div class="col-xl-5 col-md-6">
                <label for="filtroContribuyente" class="form-label">
                    Contribuyente
                </label>

                <select
                    id="filtroContribuyente"
                    name="contribuyente_id"
                    class="form-select"
                >
                    <option value="">Todos los contribuyentes</option>

                    @foreach($contribuyentes as $contribuyente)
                        <option
                            value="{{ $contribuyente->id }}"
                            @selected(request('contribuyente_id') == $contribuyente->id)
                        >
                            {{ $contribuyente->rfc }}
                            — {{ $contribuyente->razon_social }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-2 col-md-3">
                <label for="fechaInicio" class="form-label">
                    Fecha inicial
                </label>

                <input
                    type="date"
                    id="fechaInicio"
                    name="fecha_inicio"
                    value="{{ request('fecha_inicio') }}"
                    class="form-control"
                >
            </div>

            <div class="col-xl-2 col-md-3">
                <label for="fechaFin" class="form-label">
                    Fecha final
                </label>

                <input
                    type="date"
                    id="fechaFin"
                    name="fecha_fin"
                    value="{{ request('fecha_fin') }}"
                    class="form-control"
                >
            </div>

            <div class="col-xl-3 col-md-12">
                <div class="d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i>
                        Aplicar filtros
                    </button>

                    <a href="{{ route('cfdis.index') }}"
                       class="btn btn-outline-secondary"
                       title="Limpiar filtros"
                       aria-label="Limpiar filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>

        </form>

    </div>

    {{-- LISTADO --}}
    <div class="content-card cfdi-panel">

        <form
            id="formDescargaMasiva"
            method="POST"
            action="{{ route('cfdis.descargar-masivo') }}"
        >
            @csrf

            <div class="cfdi-panel-heading mb-4">

                <div>
                    <span class="premium-section-label">
                        ARCHIVO FISCAL DIGITAL
                    </span>

                    <h5 class="fw-bold mb-1">
                        Directorio de comprobantes XML
                    </h5>

                    <p class="text-body-secondary small mb-0">
                        Selecciona los XML que deseas incluir en el archivo ZIP.
                    </p>
                </div>

                <button
                    type="submit"
                    id="btnDescargaMasiva"
                    class="btn btn-success cfdi-download-button"
                    disabled
                >
                    <i class="bi bi-file-earmark-zip me-1"></i>
                    Descargar ZIP
                    (<span id="contadorSeleccionados">0</span>)
                </button>

            </div>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="cfdi-selection-info mb-3">
                <i class="bi bi-info-circle"></i>
                <span>
                    La selección se conserva al cambiar de página
                    o utilizar la búsqueda de la tabla.
                </span>
                <button
                    type="button"
                    id="btnLimpiarSeleccion"
                    class="btn btn-sm btn-link ms-auto text-nowrap"
                >
                    Limpiar selección
                </button>
            </div>

            <div class="table-responsive">

                <table
                    id="tablaCfdis"
                    class="table table-hover align-middle cfdi-table"
                    style="width:100%"
                >
                    <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    id="seleccionarTodos"
                                    class="form-check-input"
                                    aria-label="Seleccionar todos los CFDI de la página actual"
                                >
                            </th>
                            <th>UUID</th>
                            <th>Emisor</th>
                            <th>Receptor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($cfdis as $cfdi)
                            <tr>
                                <td>
                                    @if($cfdi->xml_path)
                                        <input
                                            type="checkbox"
                                            name="cfdis[]"
                                            value="{{ $cfdi->id }}"
                                            class="form-check-input check-cfdi"
                                            aria-label="Seleccionar CFDI {{ $cfdi->uuid }}"
                                        >
                                    @endif
                                </td>

                                <td>
                                    <span class="cfdi-uuid"
                                          title="{{ $cfdi->uuid }}">
                                        {{ $cfdi->uuid }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cfdi-rfc">
                                        {{ $cfdi->rfc_emisor }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cfdi-rfc">
                                        {{ $cfdi->rfc_receptor }}
                                    </span>
                                </td>

                                <td data-order="{{ $cfdi->fecha_emision
                                    ? \Carbon\Carbon::parse($cfdi->fecha_emision)->format('Y-m-d H:i:s')
                                    : '' }}">
                                    <span class="text-nowrap">
                                        {{ $cfdi->fecha_emision
                                            ? \Carbon\Carbon::parse($cfdi->fecha_emision)->format('d/m/Y')
                                            : 'N/D'
                                        }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cfdi-amount">
                                        ${{ number_format($cfdi->total ?? 0, 2) }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $tipo = strtoupper($cfdi->tipo_comprobante ?? '');

                                        $tipoClase = match($tipo) {
                                            'I' => 'income',
                                            'E' => 'expense',
                                            'P' => 'payment',
                                            'N' => 'payroll',
                                            default => 'other'
                                        };

                                        $tipoNombre = match($tipo) {
                                            'I' => 'Ingreso',
                                            'E' => 'Egreso',
                                            'P' => 'Pago',
                                            'N' => 'Nómina',
                                            'T' => 'Traslado',
                                            default => $tipo ?: 'N/D'
                                        };
                                    @endphp

                                    <span class="cfdi-type cfdi-type-{{ $tipoClase }}"
                                          title="{{ $tipoNombre }}">
                                        {{ $tipoNombre }}
                                    </span>
                                </td>

                                <td>
                                    @if($cfdi->xml_path)
                                        <a
                                            href="{{ route('cfdis.xml', $cfdi) }}"
                                            class="btn cfdi-xml-button"
                                            title="Descargar XML"
                                        >
                                            <i class="bi bi-download"></i>
                                            XML
                                        </a>
                                    @else
                                        <span class="cfdi-no-file">
                                            <i class="bi bi-file-earmark-x"></i>
                                            No disponible
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formDescargaMasiva');
    const selectAll = document.getElementById('seleccionarTodos');
    const counter = document.getElementById('contadorSeleccionados');
    const downloadButton = document.getElementById('btnDescargaMasiva');
    const clearButton = document.getElementById('btnLimpiarSeleccion');

    if (!form || !window.$?.fn?.DataTable) return;

    const $table = window.$('#tablaCfdis');

    const table = window.$.fn.DataTable.isDataTable('#tablaCfdis')
        ? $table.DataTable()
        : $table.DataTable({
            pageLength: 10,
            order: [[4, 'desc']],
            autoWidth: false,
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 },
                { orderable: false, targets: 7 }
            ],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Sin registros',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'No hay CFDI registrados',
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente'
                }
            }
        });

    const seleccionados = new Set();

    function inputsPaginaActual() {
        return table.rows({
            search: 'applied',
            page: 'current'
        }).nodes().toArray().flatMap(row =>
            Array.from(row.querySelectorAll('.check-cfdi'))
        );
    }

    function actualizarContador() {
        counter.textContent = seleccionados.size;
        downloadButton.disabled = seleccionados.size === 0;

        const inputs = inputsPaginaActual();
        const seleccionadosPagina = inputs.filter(input =>
            seleccionados.has(input.value)
        ).length;

        selectAll.checked =
            inputs.length > 0 &&
            seleccionadosPagina === inputs.length;

        selectAll.indeterminate =
            seleccionadosPagina > 0 &&
            seleccionadosPagina < inputs.length;
    }

    $table.on('change', '.check-cfdi', function () {
        if (this.checked) {
            seleccionados.add(this.value);
        } else {
            seleccionados.delete(this.value);
        }

        actualizarContador();
    });

    selectAll.addEventListener('change', function () {
        const checked = this.checked;

        inputsPaginaActual().forEach(input => {
            input.checked = checked;

            if (checked) {
                seleccionados.add(input.value);
            } else {
                seleccionados.delete(input.value);
            }
        });

        actualizarContador();
    });

    table.on('draw', function () {
        table.rows().nodes().toArray().forEach(row => {
            const input = row.querySelector('.check-cfdi');

            if (input) {
                input.checked = seleccionados.has(input.value);
            }
        });

        actualizarContador();
    });

    clearButton.addEventListener('click', function () {
        seleccionados.clear();

        table.rows().nodes().toArray().forEach(row => {
            const input = row.querySelector('.check-cfdi');

            if (input) input.checked = false;
        });

        actualizarContador();
    });

    form.addEventListener('submit', function (event) {
        if (seleccionados.size === 0) {
            event.preventDefault();
            return;
        }

        form.querySelectorAll('.selected-cfdi-hidden')
            .forEach(input => input.remove());

        // Evitar duplicados en el POST.
        table.rows().nodes().toArray().forEach(row => {
            row.querySelectorAll('.check-cfdi').forEach(input => {
                input.disabled = true;
            });
        });

        seleccionados.forEach(id => {
            const hidden = document.createElement('input');

            hidden.type = 'hidden';
            hidden.name = 'cfdis[]';
            hidden.value = id;
            hidden.className = 'selected-cfdi-hidden';

            form.appendChild(hidden);
        });
    });

    actualizarContador();
});
</script>
@endpush