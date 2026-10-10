@extends('layouts.admin')

@section('title', 'Contribuyentes | CFDI Manager')
@section('page-title', 'Contribuyentes')

@section('content')

<div class="container-fluid px-0 executive-contributors">

    {{-- ENCABEZADO --}}
    <div class="premium-page-header mb-4">
        <div>
            <span class="premium-eyebrow">
                <i class="bi bi-buildings me-1"></i>
                CFDI MANAGER / GESTIÓN CONTABLE
            </span>

            <h2 class="premium-page-title">
                Directorio de contribuyentes
            </h2>

            <p class="text-body-secondary mb-0">
                Administra los contribuyentes y sus credenciales fiscales.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary contributor-new-button"
            data-bs-toggle="modal"
            data-bs-target="#modalContribuyente"
            id="btnNuevo"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo contribuyente
        </button>
    </div>

    {{-- ESTADÍSTICAS --}}
    @php
        $totalContribuyentes = $contribuyentes->count();
        $totalActivos = $contribuyentes->where('activo', true)->count();
        $totalInactivos = $totalContribuyentes - $totalActivos;
    @endphp

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="content-card contributor-stat h-100">
                <div class="contributor-stat-icon contributor-stat-blue">
                    <i class="bi bi-people"></i>
                </div>

                <div>
                    <span class="text-body-secondary small">
                        Total registrados
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($totalContribuyentes) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card contributor-stat h-100">
                <div class="contributor-stat-icon contributor-stat-green">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <span class="text-body-secondary small">
                        Contribuyentes activos
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($totalActivos) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card contributor-stat h-100">
                <div class="contributor-stat-icon contributor-stat-amber">
                    <i class="bi bi-person-x"></i>
                </div>

                <div>
                    <span class="text-body-secondary small">
                        Contribuyentes inactivos
                    </span>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ number_format($totalInactivos) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLA --}}
    <div class="content-card contributor-table-card">

        <div class="d-flex flex-wrap justify-content-between
                    align-items-center gap-3 mb-4">

            <div>
                <span class="premium-section-label">
                    ADMINISTRACIÓN FISCAL
                </span>

                <h5 class="fw-bold mb-1">
                    Contribuyentes registrados
                </h5>

                <p class="text-body-secondary small mb-0">
                    Consulta, actualiza y administra credenciales.
                </p>
            </div>

            <span class="contributor-counter">
                <i class="bi bi-database me-1"></i>
                {{ $totalContribuyentes }} registros
            </span>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable contributor-table">

                <thead>
                    <tr>
                        <th>RFC</th>
                        <th>Contribuyente</th>
                        <th>Correo electrónico</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($contribuyentes as $item)
                        <tr>

                            <td>
                                <span class="contributor-rfc">
                                    {{ $item->rfc }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    <span class="contributor-table-avatar">
                                        {{ mb_strtoupper(mb_substr($item->razon_social, 0, 1)) }}
                                    </span>

                                    <span class="fw-semibold">
                                        {{ $item->razon_social }}
                                    </span>

                                </div>
                            </td>

                            <td>
                                <span class="text-body-secondary">
                                    {{ $item->correo ?? 'No registrado' }}
                                </span>
                            </td>

                            <td>
                                @if($item->activo)
                                    <span class="contributor-status contributor-status-active">
                                        <span></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="contributor-status contributor-status-inactive">
                                        <span></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex justify-content-end gap-2">

                                    <button
                                        type="button"
                                        class="btn contributor-action-button btn-editar"
                                        data-id="{{ $item->id }}"
                                        title="Editar contribuyente"
                                        aria-label="Editar contribuyente {{ $item->rfc }}"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    @if($item->activo)
                                        <button
                                            type="button"
                                            class="btn contributor-action-button contributor-action-danger btn-desactivar"
                                            data-id="{{ $item->id }}"
                                            title="Desactivar contribuyente"
                                            aria-label="Desactivar contribuyente {{ $item->rfc }}"
                                        >
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        class="btn contributor-action-button btn-credenciales"
                                        data-id="{{ $item->id }}"
                                        title="Administrar e.firma"
                                        aria-label="Administrar e.firma de {{ $item->rfc }}"
                                    >
                                        <i class="bi bi-shield-lock"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>

</div>

{{-- MODAL CONTRIBUYENTE --}}
<div class="modal fade" id="modalContribuyente" tabindex="-1"
     aria-labelledby="tituloModal" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content contributor-modal">

            <form id="formContribuyente">

                <div class="modal-header contributor-modal-header">

                    <div class="d-flex align-items-center gap-3">
                        <div class="contributor-modal-icon">
                            <i class="bi bi-building-add"></i>
                        </div>

                        <div>
                            <span class="premium-section-label mb-1">
                                GESTIÓN DE CONTRIBUYENTES
                            </span>

                            <h5 class="modal-title fw-bold" id="tituloModal">
                                Nuevo contribuyente
                            </h5>
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">

                    <input type="hidden" id="contribuyenteId">

                    <div class="row g-3">

                        <div class="col-md-5">
                            <label for="rfc" class="form-label">
                                RFC <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="rfc"
                                name="rfc"
                                class="form-control text-uppercase"
                                maxlength="13"
                                minlength="12"
                                placeholder="RFC del contribuyente"
                                autocomplete="off"
                                required
                            >

                            <small class="text-body-secondary">
                                12 o 13 caracteres.
                            </small>
                        </div>

                        <div class="col-md-7">
                            <label for="razon_social" class="form-label">
                                Razón social o nombre
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="razon_social"
                                name="razon_social"
                                class="form-control"
                                placeholder="Nombre o razón social"
                                required
                            >
                        </div>

                        <div class="col-12">
                            <label for="correo" class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                class="form-control"
                                placeholder="contacto@empresa.com"
                            >
                        </div>

                        <div class="col-12" id="campoActivo">
                            <div class="contributor-switch-panel">
                                <div>
                                    <strong>Estado del contribuyente</strong>
                                    <small class="d-block text-body-secondary">
                                        Controla su disponibilidad en el sistema.
                                    </small>
                                </div>

                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="activo"
                                        checked
                                        aria-label="Contribuyente activo"
                                    >
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="formError"
                         class="alert alert-danger d-none mt-3"
                         role="alert"></div>

                </div>

                <div class="modal-footer contributor-modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="btnGuardar">

                        <i class="bi bi-check2-circle me-1"></i>
                        Guardar contribuyente

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

{{-- MODAL E.FIRMA --}}
<div class="modal fade" id="modalCredenciales" tabindex="-1"
     aria-labelledby="tituloCredenciales" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content contributor-modal">

            <form id="formCredenciales" enctype="multipart/form-data">

                <div class="modal-header contributor-modal-header">

                    <div class="d-flex align-items-center gap-3">
                        <div class="contributor-modal-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>

                        <div>
                            <span class="premium-section-label mb-1">
                                SEGURIDAD FISCAL
                            </span>

                            <h5 class="modal-title fw-bold"
                                id="tituloCredenciales">
                                Administrar e.firma
                            </h5>
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">

                    <input type="hidden" id="credencialContribuyenteId">

                    <div class="contributor-security-banner mb-4">
                        <i class="bi bi-shield-check"></i>

                        <div>
                            <strong>Credenciales fiscales</strong>

                            <p class="mb-0">
                                Los archivos deben manejarse mediante
                                almacenamiento privado y cifrado.
                                Utiliza certificados válidos únicamente
                                en entornos autorizados.
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="certificado" class="form-label">
                            Certificado digital (.cer)
                        </label>

                        <input
                            type="file"
                            id="certificado"
                            name="certificado"
                            class="form-control"
                            accept=".cer"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="llave" class="form-label">
                            Llave privada (.key)
                        </label>

                        <input
                            type="file"
                            id="llave"
                            name="llave"
                            class="form-control"
                            accept=".key"
                            required
                        >
                    </div>

                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Utiliza esta carga únicamente en un entorno local
                        de pruebas hasta completar la validación
                        criptográfica y los controles de seguridad.
                    </div>

                    <div id="credencialError"
                         class="alert alert-danger d-none mt-3"
                         role="alert"></div>

                </div>

                <div class="modal-footer contributor-modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="btnGuardarCredenciales">

                        <i class="bi bi-shield-check me-1"></i>
                        Guardar credenciales

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

    const modalElement = document.getElementById('modalContribuyente');

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    const credencialModal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modalCredenciales')
    );

    const form = document.getElementById('formContribuyente');
    const credencialForm = document.getElementById('formCredenciales');

    const errorBox = document.getElementById('formError');
    const credencialError = document.getElementById('credencialError');

    const btnGuardar = document.getElementById('btnGuardar');
    const btnGuardarCredenciales = document.getElementById(
        'btnGuardarCredenciales'
    );

    function mostrarError(element, error) {
        element.textContent = error;
        element.classList.remove('d-none');
    }

    function limpiarError(element) {
        element.textContent = '';
        element.classList.add('d-none');
    }

    function cambiarEstadoBoton(button, loading, texto) {
        button.disabled = loading;

        if (loading) {
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"
                      aria-hidden="true"></span>
                ${texto}
            `;
        } else {
            button.innerHTML = button.dataset.originalHtml;
        }
    }

    async function enviar(url, method, body = null) {
        const response = await fetch(url, {
            method,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                ...(body instanceof FormData
                    ? {}
                    : {'Content-Type': 'application/json'})
            },
            body: body instanceof FormData
                ? body
                : body
                    ? JSON.stringify(body)
                    : null
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessages = data.errors
                ? Object.values(data.errors).flat().join('\n')
                : null;

            throw new Error(
                validationMessages ||
                data.message ||
                'Ocurrió un error al procesar la solicitud.'
            );
        }

        return data;
    }

    document.getElementById('btnNuevo').addEventListener('click', () => {
        form.reset();

        document.getElementById('contribuyenteId').value = '';
        document.getElementById('rfc').readOnly = false;

        document.getElementById('campoActivo').classList.add('d-none');
        document.getElementById('tituloModal').textContent =
            'Nuevo contribuyente';

        limpiarError(errorBox);
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        limpiarError(errorBox);

        const id = document.getElementById('contribuyenteId').value;

        const payload = {
            razon_social: document.getElementById('razon_social').value.trim(),
            correo: document.getElementById('correo').value.trim() || null
        };

        if (id) {
            payload.activo = document.getElementById('activo').checked ? 1 : 0;
        } else {
            payload.rfc = document.getElementById('rfc').value.toUpperCase().trim();
        }

        cambiarEstadoBoton(btnGuardar, true, 'Guardando...');

        try {
            await enviar(
                id ? `/contribuyentes/${id}` : '/contribuyentes',
                id ? 'PUT' : 'POST',
                payload
            );

            modal.hide();
            window.location.reload();

        } catch (error) {
            mostrarError(errorBox, error.message);
        } finally {
            cambiarEstadoBoton(btnGuardar, false);
        }
    });

    const tablaContribuyentes = document.querySelector(
        '.contributor-table'
    );

    tablaContribuyentes?.addEventListener('click', async function (event) {

        const button = event.target.closest('button[data-id]');

        if (!button) return;

        const id = button.dataset.id;

        // ====================================
        // EDITAR
        // ====================================

        if (button.classList.contains('btn-editar')) {

            button.disabled = true;

            try {

                const data = await enviar(
                    `/contribuyentes/${id}`,
                    'GET'
                );

                document.getElementById('contribuyenteId').value = data.id;
                document.getElementById('rfc').value = data.rfc;
                document.getElementById('rfc').readOnly = true;

                document.getElementById('razon_social').value =
                    data.razon_social;

                document.getElementById('correo').value =
                    data.correo || '';

                document.getElementById('activo').checked =
                    Boolean(Number(data.activo));

                document.getElementById('campoActivo')
                    .classList.remove('d-none');

                document.getElementById('tituloModal').textContent =
                    'Editar contribuyente';

                limpiarError(errorBox);

                modal.show();

            } catch (error) {

                alert(error.message);

            } finally {

                button.disabled = false;

            }
        }

        // ====================================
        // DESACTIVAR
        // ====================================

        if (button.classList.contains('btn-desactivar')) {

            if (!confirm('¿Desactivar este contribuyente?')) {
                return;
            }

            button.disabled = true;

            try {

                await enviar(
                    `/contribuyentes/${id}`,
                    'DELETE'
                );

                window.location.reload();

            } catch (error) {

                alert(error.message);
                button.disabled = false;

            }
        }

        // ====================================
        // CREDENCIALES
        // ====================================

        if (button.classList.contains('btn-credenciales')) {

            document.getElementById(
                'credencialContribuyenteId'
            ).value = id;

            credencialForm.reset();
            limpiarError(credencialError);

            credencialModal.show();

        }

    });

    document.querySelectorAll('.btn-desactivar').forEach(button => {
        button.addEventListener('click', async () => {
            if (!confirm('¿Desactivar este contribuyente?')) return;

            button.disabled = true;

            try {
                await enviar(
                    `/contribuyentes/${button.dataset.id}`,
                    'DELETE'
                );

                window.location.reload();

            } catch (error) {
                alert(error.message);
                button.disabled = false;
            }
        });
    });

    document.querySelectorAll('.btn-credenciales').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('credencialContribuyenteId').value =
                button.dataset.id;

            credencialForm.reset();
            limpiarError(credencialError);
            credencialModal.show();
        });
    });

    credencialForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        limpiarError(credencialError);

        const id = document.getElementById(
            'credencialContribuyenteId'
        ).value;

        const data = new FormData(this);

        cambiarEstadoBoton(
            btnGuardarCredenciales,
            true,
            'Guardando...'
        );

        try {
            await enviar(
                `/contribuyentes/${id}/credenciales`,
                'POST',
                data
            );

            credencialModal.hide();
            alert('Credenciales almacenadas correctamente.');

        } catch (error) {
            mostrarError(credencialError, error.message);
        } finally {
            cambiarEstadoBoton(btnGuardarCredenciales, false);
        }
    });

});
</script>
@endpush