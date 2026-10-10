
<section class="executive-delete-account">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div class="flex-grow-1">

            <h6 class="fw-bold mb-2">
                <i class="bi bi-shield-exclamation text-danger me-2"></i>
                Eliminación permanente de cuenta
            </h6>

            <p class="text-body-secondary small mb-0">
                Al eliminar tu cuenta, perderás el acceso a CFDI Manager.
                Esta acción puede eliminar permanentemente los datos
                asociados a tu usuario.
            </p>

        </div>

        <button
            type="button"
            class="btn btn-outline-danger delete-account-trigger"
            data-bs-toggle="modal"
            data-bs-target="#modalEliminarCuenta"
        >
            <i class="bi bi-trash3 me-2"></i>
            Eliminar cuenta
        </button>

    </div>

    <div class="delete-account-warning mt-4">

        <i class="bi bi-exclamation-triangle-fill"></i>

        <div>
            <strong>Acción irreversible</strong>

            <p class="mb-0">
                Antes de continuar, asegúrate de respaldar los
                comprobantes fiscales y la información que necesites conservar.
            </p>
        </div>

    </div>

</section>

{{-- MODAL DE CONFIRMACIÓN --}}
<div
    class="modal fade"
    id="modalEliminarCuenta"
    tabindex="-1"
    aria-labelledby="tituloEliminarCuenta"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content executive-delete-modal">

            <form
                method="POST"
                action="{{ route('profile.destroy') }}"
                id="formEliminarCuenta"
            >
                @csrf
                @method('DELETE')

                <div class="modal-header">

                    <div class="d-flex align-items-center gap-3">

                        <span class="delete-modal-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>

                        <div>
                            <span class="premium-section-label">
                                CONFIRMACIÓN DE SEGURIDAD
                            </span>

                            <h5
                                class="modal-title fw-bold"
                                id="tituloEliminarCuenta"
                            >
                                Eliminar cuenta
                            </h5>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>

                </div>

                <div class="modal-body p-4">

                    <p class="text-body-secondary mb-3">
                        ¿Estás seguro de que deseas eliminar tu cuenta?
                        Esta operación no se puede deshacer.
                    </p>

                    <div class="alert alert-danger small">
                        <i class="bi bi-info-circle me-2"></i>
                        Introduce tu contraseña actual para confirmar
                        la eliminación definitiva.
                    </div>

                    <div class="mb-3">

                        <label
                            for="deleteAccountPassword"
                            class="form-label fw-semibold"
                        >
                            Contraseña actual
                        </label>

                        <div class="input-group">

                            <input
                                type="password"
                                id="deleteAccountPassword"
                                name="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Introduce tu contraseña"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                id="toggleDeletePassword"
                                aria-label="Mostrar contraseña"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-2">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        id="btnConfirmarEliminacion"
                        class="btn btn-danger"
                    >
                        <i class="bi bi-trash3 me-1"></i>
                        Eliminar definitivamente
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement = document.getElementById('modalEliminarCuenta');
    const passwordInput = document.getElementById('deleteAccountPassword');
    const togglePassword = document.getElementById('toggleDeletePassword');
    const form = document.getElementById('formEliminarCuenta');
    const submitButton = document.getElementById('btnConfirmarEliminacion');

    if (!modalElement || !window.bootstrap) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    togglePassword?.addEventListener('click', function () {

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

        if (togglePassword) {
            togglePassword.querySelector('i').className = 'bi bi-eye';
            togglePassword.setAttribute(
                'aria-label',
                'Mostrar contraseña'
            );
        }

    });

    // Reabrir el modal si Laravel devuelve errores
    // en el bag de validación userDeletion.
    @if($errors->userDeletion->isNotEmpty())
        modal.show();
    @endif

    form?.addEventListener('submit', function (event) {

        if (!form.checkValidity()) {
            return;
        }

        if (submitButton.disabled) {
            event.preventDefault();
            return;
        }

        submitButton.disabled = true;

        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  aria-hidden="true"></span>
            Eliminando...
        `;

    });

});
</script>
@endpush
