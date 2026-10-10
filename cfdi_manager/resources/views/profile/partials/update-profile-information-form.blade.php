
<section class="executive-profile-information">

    {{-- FORMULARIO INDEPENDIENTE PARA REENVIAR VERIFICACIÓN --}}
    @if (
        $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
        && ! $user->hasVerifiedEmail()
    )
        <form
            id="send-verification"
            method="POST"
            action="{{ route('verification.send') }}"
        >
            @csrf
        </form>
    @endif

    {{-- ACTUALIZACIÓN DE INFORMACIÓN --}}
    <form
        id="formActualizarPerfil"
        method="POST"
        action="{{ route('profile.update') }}"
    >
        @csrf
        @method('PATCH')

        {{-- NOMBRE --}}
        <div class="mb-4">

            <label for="name" class="form-label fw-semibold">
                Nombre completo
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}"
                    placeholder="Introduce tu nombre completo"
                    autocomplete="name"
                    maxlength="255"
                    required
                >

            </div>

            @error('name')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- CORREO ELECTRÓNICO --}}
        <div class="mb-3">

            <label for="email" class="form-label fw-semibold">
                Correo electrónico
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}"
                    placeholder="correo@ejemplo.com"
                    autocomplete="username"
                    maxlength="255"
                    required
                >

            </div>

            @error('email')
                <div class="text-danger small mt-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror

        </div>

        {{-- ESTADO DE VERIFICACIÓN --}}
        @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)

            @if($user->hasVerifiedEmail())

                <div class="profile-email-verified mb-4">

                    <i class="bi bi-patch-check-fill"></i>

                    <div>
                        <strong>Correo electrónico verificado</strong>

                        <p class="mb-0">
                            Tu dirección de correo está verificada.
                        </p>
                    </div>

                </div>

            @else

                <div class="profile-email-unverified mb-4">

                    <i class="bi bi-exclamation-circle"></i>

                    <div>
                        <strong>Correo pendiente de verificación</strong>

                        <p class="mb-2">
                            Tu dirección de correo electrónico todavía
                            no ha sido verificada.
                        </p>

                        <button
                            type="submit"
                            form="send-verification"
                            class="btn btn-sm btn-outline-warning"
                        >
                            <i class="bi bi-envelope-arrow-up me-1"></i>
                            Reenviar verificación
                        </button>
                    </div>

                </div>

            @endif

        @endif

        {{-- MENSAJE DE VERIFICACIÓN ENVIADA --}}
        @if(session('status') === 'verification-link-sent')

            <div class="alert alert-success d-flex align-items-center gap-2"
                 role="status">
                <i class="bi bi-check-circle-fill"></i>
                Se ha enviado un nuevo enlace de verificación a tu correo.
            </div>

        @endif

        {{-- MENSAJE DE ACTUALIZACIÓN --}}
        @if(session('status') === 'profile-updated')

            <div class="alert alert-success d-flex align-items-center gap-2"
                 role="status">
                <i class="bi bi-check-circle-fill"></i>
                La información de tu perfil se actualizó correctamente.
            </div>

        @endif

        {{-- BOTÓN GUARDAR --}}
        <div class="d-flex justify-content-end">

            <button
                type="submit"
                id="btnActualizarPerfil"
                class="btn btn-primary profile-save-button"
            >
                <i class="bi bi-check-circle me-2"></i>
                Guardar cambios
            </button>

        </div>

    </form>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formActualizarPerfil');
    const button = document.getElementById('btnActualizarPerfil');

    if (!form || !button) return;

    form.addEventListener('submit', function (event) {

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        if (button.disabled) {
            event.preventDefault();
            return;
        }

        button.disabled = true;

        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  aria-hidden="true"></span>
            Guardando...
        `;

    });

});
</script>
@endpush
