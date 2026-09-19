<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\FacturacionElectronicaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PapeleraController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SuperAdmin\ClinicaController;
use App\Http\Controllers\SuperAdmin\DashboardController as SaDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\ReporteController as SaReporteController;
use App\Http\Controllers\SuperAdmin\SuscripcionController;
use App\Http\Controllers\SuperAdmin\UsuarioController as SaUsuarioController;
use Illuminate\Support\Facades\Route;

// Pagina publica y registro
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/registro', [RegistroController::class, 'show'])->name('registro.show');
Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store');

// Autenticacion
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recuperar contrasena
Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Salir de impersonacion (volver a Super Admin)
Route::post('/impersonar/salir', [ImpersonateController::class, 'stop'])->middleware('auth')->name('impersonate.stop');

// Area autenticada
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Exportaciones CSV (declaradas antes de los resources para no chocar con {id})
    Route::get('pacientes/export', [PacienteController::class, 'export'])->middleware('role:recepcion,medico,enfermeria')->name('pacientes.export');
    Route::get('citas/export', [CitaController::class, 'export'])->middleware('role:recepcion,medico,enfermeria')->name('citas.export');
    Route::get('citas/calendario', [CitaController::class, 'calendario'])->middleware('role:recepcion,medico,enfermeria')->name('citas.calendario');
    Route::get('reportes/export', [ReporteController::class, 'export'])->middleware('role:admin')->name('reportes.export');

    // Modulos con CRUD (admin tiene acceso total; abajo los roles adicionales permitidos)
    Route::resource('especialidades', EspecialidadController::class)->except('show')->middleware('role:admin');
    Route::resource('medicos', MedicoController::class)->except('show')->middleware('role:admin');
    Route::resource('pacientes', PacienteController::class)->middleware('role:recepcion,medico,enfermeria');
    Route::resource('citas', CitaController::class)->except('show')->middleware('role:recepcion,medico,enfermeria');
    Route::patch('citas/{cita}/recordar', [CitaController::class, 'recordar'])->middleware('role:recepcion,medico,enfermeria')->name('citas.recordar');
    Route::resource('historias', HistoriaClinicaController::class)->middleware('role:medico,enfermeria');
    Route::post('historias/{historia}/archivos', [HistoriaClinicaController::class, 'storeArchivo'])->middleware('role:medico,enfermeria')->name('historias.archivos.store');
    Route::delete('historias/{historia}/archivos/{archivo}', [HistoriaClinicaController::class, 'destroyArchivo'])->middleware('role:medico,enfermeria')->name('historias.archivos.destroy');
    Route::resource('recetas', RecetaController::class)->middleware('role:medico');
    Route::resource('laboratorio', LaboratorioController::class)->middleware('role:medico,enfermeria');

    // Facturacion
    Route::resource('facturas', FacturaController::class)->middleware('role:recepcion');
    Route::patch('facturas/{factura}/pagar', [FacturaController::class, 'pagar'])->middleware('role:recepcion')->name('facturas.pagar');
    Route::post('facturas/{factura}/emitir', [FacturacionElectronicaController::class, 'emitir'])->middleware('role:recepcion')->name('facturas.emitir');
    Route::post('facturas/{factura}/anular', [FacturacionElectronicaController::class, 'anular'])->middleware('role:recepcion')->name('facturas.anular');
    Route::get('facturas/{factura}/descargar/{archivo}', [FacturacionElectronicaController::class, 'descargar'])->middleware('role:recepcion')->whereIn('archivo', ['xml', 'cdr', 'nc-xml', 'nc-cdr'])->name('facturas.descargar');

    // Facturacion Electronica (SUNAT - Peru)
    Route::get('facturacion/comprobantes', [FacturacionElectronicaController::class, 'comprobantes'])->middleware('role:recepcion')->name('facturacion.comprobantes');
    Route::get('facturacion', [FacturacionElectronicaController::class, 'index'])->middleware('role:admin')->name('facturacion.index');
    Route::put('facturacion', [FacturacionElectronicaController::class, 'update'])->middleware('role:admin')->name('facturacion.update');
    Route::post('facturacion/probar', [FacturacionElectronicaController::class, 'probar'])->middleware('role:admin')->name('facturacion.probar');

    // Farmacia e Inventario
    Route::resource('productos', ProductoController::class)->middleware('role:enfermeria');
    Route::post('productos/{producto}/movimiento', [ProductoController::class, 'movimiento'])->middleware('role:enfermeria')->name('productos.movimiento');

    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])->middleware('role:admin')->name('reportes.index');

    // Configuracion
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->middleware('role:admin')->name('configuracion.index');
    Route::put('configuracion', [ConfiguracionController::class, 'update'])->middleware('role:admin')->name('configuracion.update');

    // Auditoria (bitacora)
    Route::get('auditoria', [AuditoriaController::class, 'index'])->middleware('role:admin')->name('auditoria.index');

    // Papelera (registros eliminados)
    Route::middleware('role:admin')->group(function () {
        Route::get('papelera', [PapeleraController::class, 'index'])->name('papelera.index');
        Route::put('papelera/{tipo}/{id}/restaurar', [PapeleraController::class, 'restaurar'])->name('papelera.restaurar');
        Route::delete('papelera/{tipo}/{id}', [PapeleraController::class, 'forzar'])->name('papelera.forzar');
    });

    // Solo administradores gestionan usuarios
    Route::resource('usuarios', UsuarioController::class)
        ->except('show')
        ->middleware('role:admin');

    // Fallback para cualquier modulo aun sin CRUD (placeholder)
    Route::get('/modulo/{module}', [ModuleController::class, 'show'])->name('module.show');
});

// ===================== PANEL SUPER ADMIN =====================
Route::middleware(['auth', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/', [SaDashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [SaDashboardController::class, 'index'])->name('dashboard.alt');

        Route::resource('clinicas', ClinicaController::class);
        Route::patch('clinicas/{clinica}/estado', [ClinicaController::class, 'estado'])->name('clinicas.estado');

        Route::resource('planes', PlanController::class)->parameters(['planes' => 'plan'])->except('show');
        Route::resource('suscripciones', SuscripcionController::class)->parameters(['suscripciones' => 'suscripcion'])->except('show');
        Route::resource('usuarios', SaUsuarioController::class)->except('show');

        // Reportes globales
        Route::get('reportes', [SaReporteController::class, 'index'])->name('reportes.index');

        // Impersonar (entrar como) una clinica
        Route::post('clinicas/{clinica}/impersonar', [ImpersonateController::class, 'start'])->name('clinicas.impersonar');
    });
