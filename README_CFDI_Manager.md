# CFDI Manager

**Plataforma web de gestión fiscal desarrollada con Laravel para administrar contribuyentes, credenciales SAT, solicitudes de descarga masiva y comprobantes fiscales digitales (CFDI).**

> **Estado del proyecto:** en desarrollo. La integración de extremo a extremo con los servicios del SAT, la automatización de solicitudes y el procesamiento de paquetes requieren pruebas y validación adicionales. No se garantiza que las descargas reales estén operativas.

## Descripción

CFDI Manager centraliza operaciones relacionadas con los Comprobantes Fiscales Digitales por Internet (CFDI) de México mediante un panel administrativo. Su arquitectura separa controladores, modelos, servicios de integración con el SAT y trabajos en cola para facilitar el mantenimiento y la evolución del sistema.

## Características

- **Autenticación y perfil:** inicio de sesión, administración de perfil y actualización de contraseña mediante Laravel Breeze.
- **Contribuyentes:** gestión de registros y asociación con credenciales SAT.
- **Credenciales SAT:** estructura para almacenar certificados y llaves privadas cifrados, asociados a un contribuyente.
- **Solicitudes CFDI:** registro de rangos de fechas, tipo de comprobantes (emitidos/recibidos) y formato solicitado.
- **Seguimiento SAT:** servicios para autenticación, envío, verificación y descarga de paquetes; integración y validación operativa en desarrollo.
- **Comprobantes XML:** componentes para consulta y procesamiento de CFDI.
- **Centro de operaciones:** panel para consultar solicitudes, estados y paquetes.
- **Procesamiento asíncrono:** Job `ProcesarSolicitudSat` preparado para Laravel Queues; la lógica de comunicación completa con SAT está pendiente de implementación.
- **Interfaz administrativa:** Bootstrap 5, tablas dinámicas, dashboard y diseño responsivo.

## Tecnologías

| Tecnología | Uso |
| --- | --- |
| PHP / Laravel | Backend, MVC, validaciones y servicios |
| MySQL | Persistencia de datos |
| Laravel Breeze | Autenticación de usuarios |
| Laravel Queues | Procesamiento de tareas en segundo plano |
| Bootstrap 5 | Interfaz responsiva |
| JavaScript / jQuery | Interacciones del frontend |
| DataTables | Tablas y búsqueda |
| Chart.js | Gráficas del dashboard |
| Vite | Compilación de recursos frontend |
| XML / SOAP | Integración y procesamiento fiscal |

## Estructura principal

```text
app/
├── Http/Controllers/
│   ├── CfdiController.php
│   ├── ContribuyenteController.php
│   ├── CredencialSatController.php
│   ├── DashboardController.php
│   ├── SatDownloadController.php
│   ├── SatOperationsController.php
│   ├── SatVerificationController.php
│   └── SolicitudDescargaController.php
├── Jobs/
│   └── ProcesarSolicitudSat.php
├── Models/
│   ├── Cfdi.php
│   ├── Contribuyente.php
│   ├── CredencialSat.php
│   ├── PaqueteSat.php
│   └── SolicitudDescarga.php
└── Services/Sat/
    ├── CfdiXmlProcessor.php
    ├── SatAuthenticationService.php
    ├── SatDownloadService.php
    ├── SatRequestService.php
    └── SatVerifyService.php
```

## Requisitos

- PHP compatible con la versión de Laravel definida en `composer.json`.
- Composer.
- Node.js y npm.
- MySQL o una base de datos compatible configurada en Laravel.
- Extensiones de PHP requeridas por las dependencias del proyecto.

## Instalación local

Clona el repositorio y entra en el directorio:

```bash
git clone https://github.com/TU-USUARIO/cfdi-manager-laravel.git
cd cfdi-manager-laravel
```

Instala las dependencias:

```bash
composer install
npm install
```

Configura el entorno:

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus credenciales de MySQL y una conexión de cola apropiada, por ejemplo:

```dotenv
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cfdi_manager
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
QUEUE_CONNECTION=database
```

Crea la base de datos en MySQL y ejecuta:

```bash
php artisan migrate
npm run build
php artisan serve
```

En otra terminal, para procesar trabajos de la cola:

```bash
php artisan queue:work --tries=3 --timeout=120
```

> Si tu versión de Laravel no incluye una migración para la tabla `jobs`, créala con `php artisan make:queue-table` y luego ejecuta `php artisan migrate`. Verifica también que las migraciones existentes correspondan al esquema de tu instalación.

## Flujo previsto de descargas SAT

1. Registrar un contribuyente.
2. Asociar sus credenciales e.firma autorizadas.
3. Crear una solicitud de CFDI emitidos o recibidos.
4. Autenticarse y enviar la solicitud al servicio SAT.
5. Consultar el estado de la solicitud.
6. Descargar los paquetes disponibles.
7. Procesar los XML y almacenar sus datos sin duplicados.

**Nota:** Los pasos de integración con SAT no deben considerarse funcionales en producción hasta completar pruebas con credenciales autorizadas y respuestas reales del servicio.

## Seguridad

- Nunca subas al repositorio archivos `.key`, `.cer` reales, contraseñas de e.firma, tokens ni el archivo `.env`.
- Mantén los certificados y llaves privadas en almacenamiento privado, cifrados y con permisos de acceso restringidos.
- Evita registrar contraseñas de e.firma en logs, jobs serializados o tablas de solicitudes.
- Implementa controles de autorización por contribuyente y políticas de retención de datos fiscales.
- Utiliza credenciales ficticias únicamente para pruebas locales; no autentican ante el SAT.

## Estado y próximos pasos

- [x] Estructura de modelos, controladores y servicios SAT.
- [x] Vistas administrativas para contribuyentes, solicitudes y CFDI.
- [x] Formularios de perfil adaptados a Bootstrap 5.
- [x] Job en cola creado.
- [ ] Completar el procesamiento real del Job `ProcesarSolicitudSat`.
- [ ] Validar la integración SOAP con los servicios SAT.
- [ ] Automatizar seguimiento, reintentos y descarga de paquetes.
- [ ] Implementar pruebas automatizadas y manejo de errores.
- [ ] Endurecer seguridad, auditoría y despliegue productivo.

## Aviso

Proyecto de desarrollo de software. No está afiliado ni respaldado por el Servicio de Administración Tributaria (SAT). El uso de servicios y certificados digitales debe realizarse con autorización y conforme a las disposiciones aplicables.
