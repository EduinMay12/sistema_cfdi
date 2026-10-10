<div align="center">

# CFDI Manager

**Sistema de gestión fiscal y administración de CFDI desarrollado con Laravel**

[![Laravel](https://img.shields.io/badge/Laravel-PHP-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![GitHub](https://img.shields.io/badge/Repositorio-GitHub-181717?logo=github&logoColor=white)](https://github.com/EduinMay12/sistema_cfdi)

</div>

## Descripción

**CFDI Manager** es una aplicación web para administrar contribuyentes, registrar credenciales de e.firma de manera protegida, gestionar solicitudes de descarga de CFDI y consultar información de comprobantes fiscales XML. Incluye un panel administrativo, un centro de operaciones SAT y componentes para procesar solicitudes mediante trabajos en segundo plano.

> **Estado del proyecto:** en desarrollo. La autenticación, solicitud, verificación y descarga real ante el SAT requieren una e.firma válida, autorización del titular y una configuración operativa de los servicios correspondientes. La existencia de módulos en el código no garantiza que la integración de extremo a extremo esté habilitada.

## Funcionalidades

- Autenticación y gestión de perfil de usuarios mediante Laravel Breeze.
- Panel administrativo con indicadores y navegación.
- Administración de contribuyentes.
- Registro y gestión de credenciales SAT asociadas a contribuyentes.
- Registro y seguimiento de solicitudes de descarga masiva.
- Consulta de CFDI y datos de comprobantes XML.
- Centro de Operaciones SAT para supervisión.
- Estructura de servicios SAT y trabajos en cola para procesamiento asíncrono.
- Interfaz con Bootstrap 5, JavaScript y tablas interactivas.

## Tecnologías

- PHP y Laravel
- MySQL
- Composer
- Node.js, npm y Vite
- Bootstrap 5, JavaScript y jQuery
- Laravel Queues y Scheduler

## Requisitos previos

Instala y verifica:

- Git
- PHP compatible con la versión de Laravel definida en `composer.json`, con extensiones necesarias (por ejemplo, `pdo_mysql`, `mbstring`, `xml`, `openssl`, `curl`, `zip` y `fileinfo`)
- Composer
- MySQL o MariaDB compatible
- Node.js y npm compatibles con las dependencias del proyecto

```bash
php -v
composer --version
mysql --version
node -v
npm -v
```

## Instalación desde cero

### 1. Clonar el repositorio

```bash
git clone https://github.com/EduinMay12/sistema_cfdi.git
cd sistema_cfdi
```

### 2. Instalar dependencias PHP y JavaScript

```bash
composer install
npm install
```

### 3. Crear el archivo de configuración

```bash
cp .env.example .env
php artisan key:generate
```

> Nunca publiques `.env`, certificados `.cer`, llaves `.key`, contraseñas de e.firma ni otros secretos en GitHub.

### 4. Crear la base de datos MySQL

Inicia el servicio MySQL en Ubuntu, si aún no está activo:

```bash
sudo systemctl start mysql
```

Accede a MySQL con una cuenta administrativa:

```bash
sudo mysql
```

Crea una base de datos y un usuario **exclusivos para desarrollo local**:

```sql
CREATE DATABASE cfdi_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER 'cfdi_app'@'localhost'
    IDENTIFIED BY 'CAMBIA_ESTA_CLAVE_POR_UNA_SEGURA';

GRANT ALL PRIVILEGES ON cfdi_manager.*
    TO 'cfdi_app'@'localhost';

EXIT;
```

Si la base de datos o el usuario ya existen, no repitas las instrucciones de creación sin revisar tu instalación.

### 5. Configurar la conexión en `.env`

Edita `.env` y establece:

```dotenv
APP_NAME="CFDI Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cfdi_manager
DB_USERNAME=cfdi_app
DB_PASSWORD="CAMBIA_ESTA_CLAVE_POR_UNA_SEGURA"

QUEUE_CONNECTION=database
```

La contraseña de `DB_PASSWORD` debe coincidir con la creada en MySQL. `APP_DEBUG=true` es únicamente para desarrollo; en producción utiliza `APP_DEBUG=false`.

### 6. Crear tablas mediante migraciones

```bash
php artisan config:clear
php artisan migrate
```

Esto crea las tablas definidas en `database/migrations`. Si alguna migración falla, revisa el error antes de continuar.

Si el proyecto utiliza una cola de base de datos y **no existe** una migración para la tabla `jobs`, créala y migra:

```bash
php artisan make:queue-table
php artisan migrate
```

> Ejecuta `make:queue-table` **solo si falta** la migración de `jobs`; de lo contrario crearías una migración duplicada. No utilices `php artisan migrate:fresh` sobre una base con información que deseas conservar: elimina las tablas y sus datos.

### 7. Preparar almacenamiento y compilar recursos

```bash
php artisan storage:link
npm run build
php artisan optimize:clear
```

`storage:link` solo debe usarse para archivos destinados a ser públicos. **Los certificados, llaves privadas y paquetes fiscales deben permanecer en almacenamiento privado**, nunca dentro de un directorio expuesto por el servidor web.

## Cómo ejecutar el sistema

Abre terminales independientes dentro de la carpeta del proyecto.

**Terminal 1 — Servidor Laravel**

```bash
php artisan serve
```

Abre **http://127.0.0.1:8000** en el navegador.

**Terminal 2 — Compilación de frontend durante el desarrollo (opcional)**

```bash
npm run dev
```

Si ya ejecutaste `npm run build`, no necesitas mantener Vite activo para servir los recursos compilados.

**Terminal 3 — Trabajador de colas**

```bash
php artisan queue:work --tries=3 --timeout=120
```

El trabajador permanecerá esperando trabajos. Para detenerlo, presiona `Ctrl+C` una vez. Si `QUEUE_CONNECTION=sync`, los trabajos se ejecutan inmediatamente y no pasan por este trabajador.

**Scheduler — Tareas programadas (si hay tareas registradas)**

Para comprobarlas:

```bash
php artisan schedule:list
```

En desarrollo puedes utilizar, en otra terminal:

```bash
php artisan schedule:work
```

Esto solo ejecutará las tareas que realmente estén registradas en el planificador del proyecto.

## Primer acceso

Abre `http://127.0.0.1:8000` y registra un usuario **si el registro está habilitado** en la aplicación. Después inicia sesión para acceder al panel administrativo.

No se incluyen credenciales predeterminadas en este README.

## Estructura principal

```text
app/
├── Http/Controllers/
│   ├── ContribuyenteController.php
│   ├── CredencialSatController.php
│   ├── SolicitudDescargaController.php
│   ├── CfdiController.php
│   └── SatOperationsController.php
├── Jobs/
│   └── ProcesarSolicitudSat.php
├── Models/
│   ├── Contribuyente.php
│   ├── CredencialSat.php
│   ├── SolicitudDescarga.php
│   ├── PaqueteSat.php
│   └── Cfdi.php
└── Services/Sat/
    ├── SatAuthenticationService.php
    ├── SatRequestService.php
    ├── SatVerifyService.php
    ├── SatDownloadService.php
    └── CfdiXmlProcessor.php
```

## Comandos útiles

```bash
# Consultar rutas registradas
php artisan route:list

# Ver estado de las migraciones
php artisan migrate:status

# Revisar trabajos fallidos
php artisan queue:failed

# Ver tareas programadas
php artisan schedule:list

# Ejecutar pruebas, si están configuradas
php artisan test

# Limpiar cachés
php artisan optimize:clear
```

## Seguridad y consideraciones SAT

- Utiliza únicamente certificados y e.firmas cuyo titular haya autorizado su uso.
- No guardes contraseñas de la llave privada en código fuente, logs, argumentos serializados de Jobs ni repositorios Git.
- Protege certificados, llaves y paquetes XML mediante almacenamiento privado, cifrado y permisos de acceso.
- No confundas RFC genéricos (`XAXX010101000`, `XEXX010101000`) con identidades aptas para autenticación SAT mediante e.firma.
- Para pruebas locales puedes simular respuestas SAT; una e.firma simulada no permite descargar CFDI reales.
- Antes de usar el sistema en producción, valida el flujo de autenticación, manejo de errores, reintentos, trazabilidad, respaldo y obligaciones de conservación de documentos fiscales.

## Estado y próximos pasos

- [x] Estructura de módulos de contribuyentes, solicitudes, CFDI y operaciones SAT.
- [x] Job `ProcesarSolicitudSat` y servicios SAT creados en el proyecto.
- [ ] Completar y validar el flujo integral de autenticación y descarga real con el SAT.
- [ ] Automatizar verificación, descarga y procesamiento de paquetes mediante Jobs.
- [ ] Agregar pruebas automatizadas para integraciones y manejo de errores.
- [ ] Preparar despliegue de producción y supervisión de workers.

## Repositorio

[github.com/EduinMay12/sistema_cfdi](https://github.com/EduinMay12/sistema_cfdi)

## Licencia

Consulta el archivo `LICENSE` del repositorio, si está disponible. La licencia de Laravel no determina automáticamente la licencia de esta aplicación.
