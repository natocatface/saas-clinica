# SaaS Clínica

Aplicación web SaaS para la gestión integral de una clínica, construida con **Laravel 12 + MySQL** y un panel moderno con **Tailwind CSS** y **Chart.js**.

Esta primera entrega incluye: estructura base del proyecto, **login propio**, **dashboard** con KPIs y gráficos, y un **menú vertical** con todos los módulos del sistema (páginas placeholder listas para desarrollar).

---

## Requisitos

- PHP 8.2 o superior (extensiones: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer 2.x
- MySQL 8.x (o MariaDB) corriendo en `localhost:3306`

> No se necesita Node/npm: Tailwind y Chart.js se cargan por CDN.

---

## Instalación paso a paso

```bash
# 1. Desde la carpeta del proyecto, instalar dependencias de Laravel
composer install

# 2. Generar la clave de la aplicación (el .env ya viene configurado)
php artisan key:generate

# 3. Crear la base de datos en MySQL
#    (con tu cliente MySQL, o por consola)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS saas_clinica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Ejecutar migraciones y datos de prueba (usuarios)
php artisan migrate --seed

# 5. Levantar el servidor de desarrollo
php artisan serve
```

Luego abre **http://localhost:8000**

### Configuración de la base de datos

El archivo `.env` ya está configurado según lo solicitado. Si tu MySQL tiene contraseña, edítala:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_clinica
DB_USERNAME=root
DB_PASSWORD=        # <-- pon aquí tu contraseña si la tienes
```

---

## Cuentas de prueba

Creadas por el seeder (`php artisan migrate --seed`). Contraseña para todas: **`password`**

| Rol           | Correo                  | Accede a                |
|---------------|-------------------------|-------------------------|
| **Super Admin** | superadmin@saas.test  | Panel global `/superadmin` |
| Administrador | admin@clinica.test      | Panel de la clínica     |
| Médico        | medico@clinica.test     | Panel de la clínica     |
| Recepción     | recepcion@clinica.test  | Panel de la clínica     |

Al iniciar sesión, el sistema redirige automáticamente según el rol: el Super Admin entra a `/superadmin` y el resto al panel de su clínica.

---

## Multi-tenant y Super Admin

El sistema es **multi-tenant**: cada clínica (tenant) tiene sus datos aislados.

- Tablas `clinicas`, `planes` y `suscripciones`; todas las tablas operativas tienen `clinica_id`.
- El trait `App\Models\Concerns\BelongsToClinica` aplica un *global scope* que filtra automáticamente por la clínica del usuario autenticado y asigna `clinica_id` al crear. Así, un usuario de una clínica nunca ve datos de otra.
- El **Super Admin** (sin clínica) accede a `/superadmin` — un panel con diseño propio (tema oscuro) y middleware `superadmin`. Incluye:
  - **Dashboard global**: total de clínicas, MRR estimado, facturación global, usuarios/pacientes, y gráficos de crecimiento y distribución por plan.
  - **Clínicas**: alta de clínica con aprovisionamiento de su usuario administrador, edición, suspender/activar, y ficha con usuarios y suscripciones.
  - **Planes**: catálogo de planes de suscripción (precio, límites, características).
  - **Suscripciones**: gestión de suscripciones por clínica con MRR.
  - **Usuarios globales**: administración de usuarios de toda la plataforma.
  - **Reportes globales**: MRR, ARR, ARPU, churn, crecimiento de clínicas, ingresos por plan y ranking de clínicas por actividad.
  - **Impersonar clínica** ("Entrar como"): el super admin entra al panel de cualquier clínica; un banner permite volver a su sesión.

### Página pública y auto-registro

La raíz `/` muestra una **landing pública** (hero, funciones y planes/pricing desde la base de datos). Desde `/registro` una clínica se **registra sola**: elige plan, crea su cuenta de administrador, obtiene 14 días de prueba y entra directamente a su panel. Los usuarios autenticados que visitan `/` son redirigidos a su panel correspondiente.

---

## Módulos del sistema (menú vertical)

**Gestión Clínica:** Citas y Agenda · Pacientes · Médicos · Historias Clínicas · Recetas · Especialidades · Laboratorio
**Administración:** Farmacia e Inventario · Facturación y Pagos · Reportes
**Sistema:** Usuarios y Roles · Configuración

Los módulos se definen en `config/clinic.php`. Para agregar o quitar uno, edita ese archivo y el menú se actualiza automáticamente.

### CRUD ya implementado

Estos módulos tienen funcionalidad completa (listado con búsqueda y paginación, crear, editar, eliminar):

- **Especialidades** — nombre, descripción, tarifa, color, estado.
- **Médicos** — datos, especialidad, matrícula, horario, estado.
- **Pacientes** — ficha completa + vista de detalle con historial de citas.
- **Citas y Agenda** — paciente, médico, especialidad, fecha/hora, estado, filtros.
- **Historias Clínicas** — atención por paciente con signos vitales (IMC calculado), síntomas, diagnóstico, tratamiento + vista de detalle.
- **Recetas** — emisión con varios medicamentos (filas dinámicas), diagnóstico y notas + vista imprimible (`window.print`).
- **Usuarios y Roles** — gestión de usuarios con roles (admin, médico, recepción, enfermería). Protegido con `middleware('role:admin')`: solo administradores acceden.
- **Facturación y Pagos** — facturas con conceptos (ítems dinámicos y cálculo de total en vivo), descuento, estados (pendiente/pagada/anulada), acción "Cobrar", numeración automática y factura imprimible.
- **Laboratorio** — órdenes de examen por paciente/médico con resultados, unidades y valores de referencia, estados y orden imprimible.
- **Reportes** — panel con filtro por rango de fechas: ingresos por mes, citas por estado y por especialidad, productividad por médico, con KPIs y gráficos.
- **Farmacia e Inventario** — productos con stock, stock mínimo, precios, vencimiento; alertas de stock bajo, valor de inventario, y registro de **movimientos** (entradas/salidas) con historial.
- **Configuración** — datos generales de la clínica (nombre, NIT, dirección, contacto, moneda) guardados en base de datos con caché.

El **dashboard** está conectado a datos reales. **Todos los módulos del menú están implementados** con CRUD funcional.

---

## Estructura relevante

```
app/Http/Controllers/Auth/AuthController.php   Login / logout propio
app/Http/Controllers/DashboardController.php   Datos del dashboard
app/Http/Controllers/ModuleController.php      Resuelve cada módulo
app/Http/Middleware/EnsureUserHasRole.php      Control de acceso por rol (role:admin,...)
app/Models/User.php                            Usuario con rol, estado, helpers
config/clinic.php                              Definición de módulos y menú
resources/views/layouts/app.blade.php          Layout (sidebar + topbar)
resources/views/partials/                      sidebar, topbar, icon
resources/views/auth/login.blade.php           Pantalla de login
resources/views/dashboard/index.blade.php      Dashboard
resources/views/modules/placeholder.blade.php  Plantilla de cada módulo
routes/web.php                                 Rutas
```

---

## Próximos pasos sugeridos

1. Implementar el CRUD del módulo **Pacientes** como referencia (modelo, migración, controlador, vistas).
2. Desarrollar **Citas y Agenda** con calendario y estados.
3. Conectar el dashboard a datos reales (reemplazar los datos de ejemplo en `DashboardController`).
4. Añadir gestión de **Usuarios y Roles** con el middleware `role`.

---

## Notas técnicas

- **Sesiones, caché y colas** usan el driver `database` (por eso las migraciones crean esas tablas). Si prefieres archivos, cambia `SESSION_DRIVER=file` y `CACHE_STORE=file` en `.env`.
- El control de acceso por rol está disponible vía middleware: `Route::...->middleware('role:admin,recepcion')`.
- Para producción, se recomienda migrar de Tailwind CDN a una compilación con Vite.
