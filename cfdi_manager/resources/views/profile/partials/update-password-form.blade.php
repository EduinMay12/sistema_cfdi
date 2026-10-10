
<section class="executive-password-section">

    <form
        id="formActualizarPassword"
        method="POST"
        action="{{ route('password.update') }}"
    >
        @csrf
        @method('PUT')

        {{-- CONTRASEÑA ACTUAL --}}
        <div class="mb-4">

            <label
                for="update_password_current_password"
                class="form-label fw-semibold"
            >
                Contraseña actual
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>

                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password"
                    placeholder="Introduce tu contraseña actual"
                    required
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary toggle-profile-password"
                    data-target="update_password_current_password"
                    aria-label="Mostrar contraseña actual"
                    aria-pressed="false"
                >
                    <i class="bi bi-eye"></i>
                </button>

            </div>

            @error('current_password', 'updatePassword')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- NUEVA CONTRASEÑA --}}
        <div class="mb-3">

            <label
                for="update_password_password"
                class="form-label fw-semibold"
            >
                Nueva contraseña
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-shield-lock"></i>
                </span>

                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password"
                    placeholder="Introduce una nueva contraseña"
                    required
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary toggle-profile-password"
                    data-target="update_password_password"
                    aria-label="Mostrar nueva contraseña"
                    aria-pressed="false"
                >
                    <i class="bi bi-eye"></i>
                </button>

            </div>

            @error('password', 'updatePassword')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- INDICADOR DE SEGURIDAD --}}
        <div class="password-strength-panel mb-4">

            <div class="d-flex justify-content-between
                        align-items-center mb-2">

                <span class="small text-body-secondary">
                    Seguridad de la contraseña
                </span>

                <span
                    id="passwordStrengthLabel"
                    class="small fw-semibold text-body-secondary"
                    aria-live="polite"
                >
                    Sin evaluar
                </span>

            </div>

            <div class="progress password-strength-progress"
                 role="progressbar"
                 aria-label="Indicador orientativo de seguridad"
                 aria-valuemin="0"
                 aria-valuemax="100"
                 aria-valuenow="0">

                <div
                    id="passwordStrengthBar"
                    class="progress-bar"
                    style="width:0%"
                ></div>

            </div>

            <p class="text-body-secondary small mt-2 mb-0">
                Utiliza una contraseña larga, única y difícil de adivinar.
                El indicador es orientativo.
            </p>

        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div class="mb-4">

            <label
                for="update_password_password_confirmation"
                class="form-label fw-semibold"
            >
                Confirmar nueva contraseña
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-shield-check"></i>
                </span>

                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password"
                    placeholder="Repite tu nueva contraseña"
                    required
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary toggle-profile-password"
                    data-target="update_password_password_confirmation"
                    aria-label="Mostrar confirmación de contraseña"
                    aria-pressed="false"
                >
                    <i class="bi bi-eye"></i>
                </button>

            </div>

            <div
                id="passwordMatchMessage"
                class="small mt-2 d-none"
                aria-live="polite"
            ></div>

            @error('password_confirmation', 'updatePassword')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- CONSEJO DE SEGURIDAD --}}
        <div class="profile-password-tip mb-4">

            <i class="bi bi-info-circle"></i>

            <span>
                Para proteger tus comprobantes fiscales y tu información
                de acceso, evita reutilizar contraseñas de otros servicios.
            </span>

        </div>

        {{-- MENSAJE EXITOSO --}}
        @if(session('status') === 'password-updated')

            <div
                class="alert alert-success d-flex align-items-center gap-2"
                role="status"
            >
                <i class="bi bi-check-circle-fill"></i>
                Tu contraseña se actualizó correctamente.
            </div>

        @endif

        {{-- GUARDAR --}}
        <div class="d-flex justify-content-end">

            <button
                type="submit"
                id="btnActualizarPassword"
                class="btn btn-primary profile-password-save"
            >
                <i class="bi bi-shield-check me-2"></i>
                Actualizar contraseña
            </button>

        </div>

    </form>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formActualizarPassword');
    const newPassword = document.getElementById('update_password_password');
    const confirmation = document.getElementById(
        'update_password_password_confirmation'
    );
    const strengthLabel = document.getElementById('passwordStrengthLabel');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const matchMessage = document.getElementById('passwordMatchMessage');
    const saveButton = document.getElementById('btnActualizarPassword');

    if (!form || !newPassword || !confirmation) return;

    // MOSTRAR / OCULTAR CONTRASEÑAS
    document.querySelectorAll('.toggle-profile-password')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const input = document.getElementById(this.dataset.target);
                if (!input) return;

                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';

                this.querySelector('i').className = visible
                    ? 'bi bi-eye-slash'
                    : 'bi bi-eye';

                this.setAttribute('aria-pressed', String(visible));
                this.setAttribute(
                    'aria-label',
                    visible ? 'Ocultar contraseña' : 'Mostrar contraseña'
                );

            });

        });

    // INDICADOR ORIENTATIVO
    function actualizarSeguridad() {

        const value = newPassword.value;

        if (!value) {
            strengthLabel.textContent = 'Sin evaluar';
            strengthLabel.className =
                'small fw-semibold text-body-secondary';

            strengthBar.style.width = '0%';
            strengthBar.className = 'progress-bar';

            strengthBar.parentElement.setAttribute('aria-valuenow', '0');
            return;
        }

        let puntos = 0;

        if (value.length >= 8) puntos++;
        if (value.length >= 12) puntos++;
        if (value.length >= 16) puntos++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) puntos++;
        if (/[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value)) puntos++;

        const porcentaje = puntos * 20;

        let texto = 'Muy débil';
        let color = 'bg-danger';

        if (puntos >= 4) {
            texto = 'Alta';
            color = 'bg-success';
        } else if (puntos >= 3) {
            texto = 'Media';
            color = 'bg-warning';
        } else if (puntos >= 2) {
            texto = 'Baja';
            color = 'bg-warning';
        }

        strengthLabel.textContent = texto;
        strengthBar.style.width = `${porcentaje}%`;
        strengthBar.className = `progress-bar ${color}`;

        strengthBar.parentElement.setAttribute(
            'aria-valuenow',
            String(porcentaje)
        );
    }

    // COMPROBAR COINCIDENCIA
    function verificarCoincidencia() {

        if (!confirmation.value) {
            matchMessage.className = 'small mt-2 d-none';
            confirmation.setCustomValidity('');
            return;
        }

        const coincide = newPassword.value === confirmation.value;

        matchMessage.className = coincide
            ? 'small mt-2 text-success'
            : 'small mt-2 text-danger';

        matchMessage.textContent = coincide
            ? 'Las contraseñas coinciden.'
            : 'Las contraseñas no coinciden.';

        confirmation.setCustomValidity(
            coincide ? '' : 'Las contraseñas no coinciden.'
        );
    }

    newPassword.addEventListener('input', function () {
        actualizarSeguridad();
        verificarCoincidencia();
    });

    confirmation.addEventListener('input', verificarCoincidencia);

    form.addEventListener('submit', function (event) {

        verificarCoincidencia();

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        saveButton.disabled = true;

        saveButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  aria-hidden="true"></span>
            Actualizando...
        `;
    });

});
</script>
@endpush
