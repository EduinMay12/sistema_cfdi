<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\SolicitudDescargaController;
use App\Http\Controllers\CfdiController;
use App\Http\Controllers\CredencialSatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatVerificationController;
use App\Http\Controllers\SatDownloadController;
use App\Http\Controllers\SatOperationsController;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get(
        '/sat/operaciones',
        [SatOperationsController::class, 'index']
    )->name('sat.operaciones');

    Route::get('/cfdis', [CfdiController::class, 'index'])
        ->name('cfdis.index');

    Route::get('/cfdis/{cfdi}/xml', [CfdiController::class, 'descargarXml'])
        ->name('cfdis.xml');

    Route::post('/cfdis/descargar-masivo', [
        CfdiController::class,
        'descargarMasivo'
    ])->name('cfdis.descargar-masivo');

    Route::post(
        '/sat/paquetes/{paquete}/descargar',
        [SatDownloadController::class, 'descargar']
    )->name('sat.paquetes.descargar');

    Route::get('/', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    Route::resource(
        'contribuyentes',
        ContribuyenteController::class
    )->except(['create', 'edit']);

    Route::post(
        '/contribuyentes/{contribuyente}/credenciales',
        [CredencialSatController::class, 'store']
    )->name('contribuyentes.credenciales.store');

    Route::get(
        '/solicitudes',
        [SolicitudDescargaController::class, 'index']
    )->name('solicitudes.index');

    Route::get(
        '/cfdis',
        [CfdiController::class, 'index']
    )->name('cfdis.index');

    Route::post(
        '/solicitudes',
        [SolicitudDescargaController::class, 'store']
    )->name('solicitudes.store');

    Route::post(
        '/solicitudes/{solicitud}/enviar-sat',
        [SolicitudDescargaController::class, 'enviarSat']
    )->name('solicitudes.enviar-sat');

    Route::post(
        '/solicitudes/{solicitud}/verificar',
        [SatVerificationController::class, 'verificar']
    )->name('solicitudes.verificar');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

