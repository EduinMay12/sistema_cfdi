@extends('layouts.admin')

@section('title', 'Solicitudes SAT | CFDI Manager')
@section('page-title', 'Solicitudes SAT')

@section('content')

@php
    $totalSolicitudes = $solicitudes->count();
    $pendientes = $solicitudes->where('estado', 'pendiente')->count();
    $conIdentificadorSat = $solicitudes->filter(
        fn ($solicitud) => filled($solicitud->solicitud_sat_id)
    )->count();
@endphp

<div class="container-fluid px-0 executive-sat-requests">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">

        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-cloud-arrow-down me-1"></i>
                CFDI MANAGER / SERVICIOS FISCALES
            </span>

            <h2 class="premium-page-title">
                Solicitudes de descarga SAT
            </h2>

            <p class="text-body-secondary mb-0">
                Administra las solicitudes de descarga de comprobantes fiscales.
            </p>
        </div>

        <div class="premium-header-icon">
            <i class="bi bi-cloud-download"></i>
        </div>

    </div>

    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="content-card sat-stat-card">
                <span class="sat-stat-icon sat-stat-blue">
                    <i class="bi bi-file-earmark-text"></i>
                </span>

                <div>
                    <span class="text-body-secondary small">
                        Solicitudes registradas
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($totalSolicitudes) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card sat-stat-card">
                <span class="sat-stat-icon sat-stat-amber">
                    <i class="bi bi-hourglass-split"></i>
                </span>

                <div>
                    <span class="text-body-secondary small">
                        Pendientes de envío
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($pendientes) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card sat-stat-card">
                <span class="sat-stat-icon sat-stat-green">
                    <i class="bi bi-cloud-check"></i>
                </span>

                <div>
                    <span class="text-body-secondary small">
                        Con identificador SAT
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($conIdentificadorSat) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        {{-- NUEVA SOLICITUD --}}
        <div class="col-12 col-xl-4">

            <div class="content-card sat-request-panel">

                <div class="sat-panel-heading mb-4">
                    <span class="sat-panel-icon">
                        <i class="bi bi-file-earmark-plus"></i>
                    </span>

                    <div>
                        <span class="premium-section-label">
                            NUEVA OPERACIÓN
                        </span>

                        <h5 class="fw-bold mb-0">
                            Nueva solicitud CFDI
                        </h5>
                    </div>
                </div>

                <form id="formSolicitud">

                    <div class="mb-3">
                        <label for="solicitudContribuyente" class="form-label">
                            Contribuyente
                        </label>

                        <select
                            id="solicitudContribuyente"
                            name="contribuyente_id"
                            class="form-select"
                            required
                        >
                            <option value="">Seleccionar RFC</option>

                            @foreach($contribuyentes as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->rfc }} —
                                    {{ $item->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="solicitudTipo" class="form-label">
                            Tipo de CFDI
                        </label>

                        <select
                            id="solicitudTipo"
                            name="tipo"
                            class="form-select"
                            required
                        >
                            <option value="recibidos">Recibidos</option>
                            <option value="emitidos">Emitidos</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="solicitudFormato" class="form-label">
                            Formato de descarga
                        </label>

                        <select
                            id="solicitudFormato"
                            name="formato"
                            class="form-select"
                            required
                        >
                            <option value="xml">XML</option>
                            <option value="metadata">Metadata</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">

                        <div class="col-sm-6">
                            <label for="solicitudFechaInicio" class="form-label">
                                Fecha inicial
                            </label>

                            <input
                                type="date"
                                id="solicitudFechaInicio"
                                name="fecha_inicio"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="col-sm-6">
                            <label for="solicitudFechaFin" class="form-label">
                                Fecha final
                            </label>

                            <input
                                type="date"
                                id="solicitudFechaFin"
                                name="fecha_fin"
                                class="form-control"
                                required
                            >
                        </div>

                    </div>

                    <div class="sat-form-note mb-3">
                        <i class="bi bi-info-circle"></i>
                        <span>
                            Registrar una solicitud no significa que el SAT
                            haya aceptado su procesamiento. Debes enviarla
                            posteriormente.
                        </span>
                    </div>

                    <div id="solicitudMensaje"
                         class="alert d-none"
                         role="alert"></div>

                    <button
                        type="submit"
                        id="btnSolicitud"
                        class="btn btn-primary w-100 sat-primary-button"
                    >
                        <i class="bi bi-plus-circle me-2"></i>
                        Registrar solicitud
                    </button>

                </form>

            </div>

        </div>

        {{-- HISTORIAL --}}
        <div class="col-12 col-xl-8">

            <div class="content-card sat-request-panel">

                <div class="d-flex flex-wrap justify-content-between
                            align-items-center gap-3 mb-4">

                    <div>
                        <span class="premium-section-label">
                            CONTROL DE OPERACIONES
                        </span>

                        <h5 class="fw-bold mb-1">
                            Historial de solicitudes
                        </h5>

                        <p class="text-body-secondary small mb-0">
                            Seguimiento de solicitudes y paquetes de descarga.
                        </p>
                    </div>

                    <span class="sat-record-count">
                        {{ $totalSolicitudes }} registros
                    </span>

                </div>

                <div id="operacionMensaje"
                     class="alert d-none"
                     role="alert"></div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle datatable sat-request-table">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>RFC</th>
                                <th>Tipo</th>
                                <th>Formato</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($solicitudes as $item)

                                @php
                                    $estado = strtolower($item->estado ?? '');

                                    $estadoClase = match($estado) {
                                        'pendiente' => 'pending',
                                        'aceptada', 'en_proceso', 'procesando' => 'processing',
                                        'terminada', 'completada', 'descargada' => 'completed',
                                        'error', 'rechazada', 'fallida' => 'failed',
                                        default => 'neutral'
                                    };
                                @endphp

                                <tr>

                                    <td>
                                        <span class="sat-request-id">
                                            #{{ $item->id }}
                                        </span>
                                    </td>

                                    <td>
                                        <strong class="sat-request-rfc">
                                            {{ $item->contribuyente->rfc ?? 'N/D' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ ucfirst($item->tipo) }}
                                    </td>

                                    <td>
                                        <span class="sat-format-badge">
                                            {{ strtoupper($item->formato) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="sat-status sat-status-{{ $estadoClase }}">
                                            <span class="sat-status-dot"></span>
                                            {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                        </span>
                                    </td>

                                    <td class="text-nowrap"
                                        data-order="{{ $item->created_at?->format('Y-m-d H:i:s') }}">
                                        {{ $item->created_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-2">

                                            @if($item->estado === 'pendiente')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary btn-enviar-sat"
                                                    data-url="{{ route('solicitudes.enviar-sat', $item) }}"
                                                    data-accion="enviar"
                                                    data-solicitud="{{ $item->id }}"
                                                >
                                                    <i class="bi bi-cloud-upload me-1"></i>
                                                    Enviar
                                                </button>
                                            @endif

                                            @if($item->solicitud_sat_id)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary btn-verificar-sat"
                                                    data-url="{{ route('solicitudes.verificar', $item) }}"
                                                    data-accion="verificar"
                                                    data-solicitud="{{ $item->id }}"
                                                >
                                                    <i class="bi bi-arrow-repeat me-1"></i>
                                                    Verificar
                                                </button>
                                            @endif

                                            @foreach($item->paquetes as $paquete)
                                                @if($paquete->estado === 'pendiente')
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-success btn-descargar-paquete"
                                                        data-url="{{ route('sat.paquetes.descargar', $paquete) }}"
                                                        data-accion="descargar"
                                                        data-solicitud="{{ $item->id }}"
                                                    >
                                                        <i class="bi bi-file-earmark-zip me-1"></i>
                                                        ZIP
                                                    </button>
                                                @endif
                                            @endforeach

                                            @if(
                                                $item->estado !== 'pendiente' &&
                                                !$item->solicitud_sat_id &&
                                                $item->paquetes->isEmpty()
                                            )
                                                <span class="text-body-secondary small">
                                                    Sin acciones
                                                </span>
                                            @endif

                                        </div>
                                    </td>

                                </tr>

                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- MODAL DE CONTRASEÑA E.FIRMA --}}
<div class="modal fade"
     id="modalOperacionSat"
     tabindex="-1"
     aria-labelledby="tituloOperacionSat"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sat-operation-modal">

            <form id="formOperacionSat">

                <div class="modal-header sat-operation-modal-header">

                    <div class="d-flex align-items-center gap-3">

                        <span class="sat-panel-icon">
                            <i class="bi bi-shield-lock"></i>
                        </span>

                        <div>
                            <span class="premium-section-label">
                                AUTENTICACIÓN FISCAL
                            </span>

                            <h5 class="modal-title fw-bold"
                                id="tituloOperacionSat">
                                Autorizar operación SAT
                            </h5>
                        </div>

                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>

                </div>

                <div class="modal-body p-4">

                    <p id="descripcionOperacionSat"
                       class="text-body-secondary small">
                        Introduce la contraseña de tu e.firma
                        para continuar.
                    </p>

                    <label for="passwordFiel" class="form-label">
                        Contraseña de la e.firma
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="passwordFiel"
                            class="form-control"
                            autocomplete="off"
                            required
                        >

                        <button
                            type="button"
                            id="togglePasswordFiel"
                            class="btn btn-outline-secondary"
                            aria-label="Mostrar contraseña"
                        >
                            <i class="bi bi-eye"></i>
                        </button>

                    </div>

                    <div class="sat-form-note mt-3">
                        <i class="bi bi-shield-check"></i>
                        <span>
                            La contraseña se envía para esta operación.
                            No debe registrarse en logs ni almacenarse
                            en el navegador.
                        </span>
                    </div>

                    <div id="operacionModalMensaje"
                         class="alert alert-danger d-none mt-3"
                         role="alert"></div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            id="btnConfirmarOperacionSat"
                            class="btn btn-primary">
                        <i class="bi bi-shield-check me-1"></i>
                        Confirmar operación
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrf = document.querySelector(
        'meta[name="csrf-token"]'
    ).content;

    const formSolicitud = document.getElementById('formSolicitud');
    const btnSolicitud = document.getElementById('btnSolicitud');
    const solicitudMensaje = document.getElementById('solicitudMensaje');

    const modalElement = document.getElementById('modalOperacionSat');
    const modalSat = bootstrap.Modal.getOrCreateInstance(modalElement);

    const formOperacion = document.getElementById('formOperacionSat');
    const passwordInput = document.getElementById('passwordFiel');
    const btnConfirmar = document.getElementById('btnConfirmarOperacionSat');
    const modalMensaje = document.getElementById('operacionModalMensaje');
    const operacionMensaje = document.getElementById('operacionMensaje');

    let operacionActual = null;

    function mostrarMensaje(element, texto, tipo = 'danger') {
        element.className = `alert alert-${tipo}`;
        element.textContent = texto;
    }

    function limpiarMensaje(element) {
        element.className = 'alert d-none';
        element.textContent = '';
    }

    function estadoBoton(button, cargando, texto = 'Procesando...') {
        if (cargando) {
            button.dataset.originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"
                      aria-hidden="true"></span>
                ${texto}
            `;
        } else {
            button.disabled = false;
            if (button.dataset.originalHtml) {
                button.innerHTML = button.dataset.originalHtml;
            }
        }
    }

    async function peticionSat(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            const details = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message;

            throw new Error(details || 'No se pudo completar la operación.');
        }

        return result;
    }

    // ========================================
    // REGISTRAR SOLICITUD
    // ========================================

    formSolicitud.addEventListener('submit', async function (event) {
        event.preventDefault();
        limpiarMensaje(solicitudMensaje);

        const payload = Object.fromEntries(
            new FormData(formSolicitud).entries()
        );

        if (payload.fecha_inicio > payload.fecha_fin) {
            mostrarMensaje(
                solicitudMensaje,
                'La fecha inicial no puede ser posterior a la fecha final.'
            );
            return;
        }

        estadoBoton(btnSolicitud, true, 'Registrando...');

        try {
            const result = await peticionSat(
                @json(route('solicitudes.store')),
                payload
            );

            mostrarMensaje(
                solicitudMensaje,
                result.message || 'Solicitud registrada correctamente.',
                'success'
            );

            window.setTimeout(() => window.location.reload(), 1100);

        } catch (error) {
            mostrarMensaje(solicitudMensaje, error.message);
        } finally {
            estadoBoton(btnSolicitud, false);
        }
    });

    // ========================================
    // ABRIR MODAL DE OPERACIÓN
    // ========================================

    document.addEventListener('click', function (event) {
        const button = event.target.closest(
            '.btn-enviar-sat, .btn-verificar-sat, .btn-descargar-paquete'
        );

        if (!button) return;

        const accion = button.dataset.accion;

        const configuracion = {
            enviar: {
                titulo: 'Enviar solicitud al SAT',
                descripcion: 'Introduce tu contraseña de e.firma para enviar esta solicitud.',
                boton: 'Enviar al SAT'
            },
            verificar: {
                titulo: 'Verificar solicitud SAT',
                descripcion: 'Autoriza la consulta del estado de esta solicitud.',
                boton: 'Verificar estado'
            },
            descargar: {
                titulo: 'Descargar paquete SAT',
                descripcion: 'Autoriza la descarga y procesamiento del paquete ZIP.',
                boton: 'Descargar paquete'
            }
        };

        const config = configuracion[accion];

        if (!config) return;

        operacionActual = {
            accion,
            url: button.dataset.url
        };

        document.getElementById('tituloOperacionSat').textContent =
            config.titulo;

        document.getElementById('descripcionOperacionSat').textContent =
            config.descripcion;

        btnConfirmar.innerHTML = `
            <i class="bi bi-shield-check me-1"></i>
            ${config.boton}
        `;

        passwordInput.value = '';
        passwordInput.type = 'password';

        limpiarMensaje(modalMensaje);

        modalSat.show();
    });

    // ========================================
    // MOSTRAR / OCULTAR CONTRASEÑA
    // ========================================

    document.getElementById('togglePasswordFiel')
        .addEventListener('click', function () {
            const visible = passwordInput.type === 'password';

            passwordInput.type = visible ? 'text' : 'password';

            this.querySelector('i').className = visible
                ? 'bi bi-eye-slash'
                : 'bi bi-eye';

            this.setAttribute(
                'aria-label',
                visible ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );
        });

    modalElement.addEventListener('hidden.bs.modal', function () {
        passwordInput.value = '';
        passwordInput.type = 'password';
        operacionActual = null;
        limpiarMensaje(modalMensaje);
    });

    // ========================================
    // EJECUTAR OPERACIÓN SAT
    // ========================================

    formOperacion.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (!operacionActual) return;

        const { accion, url } = operacionActual;

        const password = passwordInput.value;

        if (!password) {
            mostrarMensaje(
                modalMensaje,
                'Introduce la contraseña de la e.firma.'
            );
            return;
        }

        limpiarMensaje(modalMensaje);
        estadoBoton(btnConfirmar, true, 'Procesando...');

        // Limpiar el campo después de obtener la contraseña.
        passwordInput.value = '';

        try {
            const result = await peticionSat(url, {
                password_fiel: password
            });

            let texto = result.message || 'Operación realizada correctamente.';

            if (accion === 'enviar' && result.solicitud_sat_id) {
                texto += ` Identificador SAT: ${result.solicitud_sat_id}`;
            }

            if (accion === 'verificar') {
                texto += ` Estado: ${result.estado ?? 'No disponible'}.`;
                texto += ` Paquetes: ${result.paquetes ?? 0}.`;
            }

            if (accion === 'descargar') {
                texto += ` CFDI procesados: ${result.cfdis_procesados ?? 'N/D'}.`;
            }

            modalSat.hide();

            mostrarMensaje(operacionMensaje, texto, 'success');

            window.setTimeout(() => window.location.reload(), 1800);

        } catch (error) {
            mostrarMensaje(modalMensaje, error.message);
        } finally {
            estadoBoton(btnConfirmar, false);
        }
    });

});
</script>
@endpush