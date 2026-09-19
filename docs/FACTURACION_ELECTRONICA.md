# Facturacion Electronica (SUNAT - Peru)

Modulo de emision de comprobantes de pago electronicos (Boletas y Facturas)
ante SUNAT bajo el estandar UBL 2.1, integrado al sistema de la clinica.

## 1. Instalar la libreria de firma/envio

La emision real usa [Greenter](https://greenter.dev). Instalala con Composer:

```bash
composer require greenter/greenter
```

Mientras no este instalada, el modulo funciona en modo configuracion y el
driver "Ninguno" deja los comprobantes en estado pendiente.

## 2. Ejecutar la migracion

Anade los campos SUNAT a la tabla `facturas`:

```bash
php artisan migrate
```

## 3. Certificado digital (.pem)

1. Convierte tu certificado a formato PEM (clave publica + privada en un solo
   archivo).
2. Colocalo en `storage/app/facturacion/pe/` (o en la ruta que prefieras).
3. Indica la ruta absoluta en el modulo de configuracion.

En el entorno **beta** de SUNAT puedes probar sin certificado propio usando el
certificado de demostracion de Greenter y:

- RUC: `20000000001`
- Usuario Clave SOL: `MODDATOS`
- Clave SOL: `MODDATOS`

## 4. Configurar

Menu lateral -> **Administracion -> Facturacion Electronica** (solo admin):

- **Estado y modo**: habilitar el modulo, emision automatica, driver y entorno
  (Beta / Produccion), series y comprobante por defecto.
- **Datos del emisor**: RUC, razon social, direccion fiscal y ubigeo.
- **Credenciales SUNAT**: usuario y Clave SOL (se guarda **cifrada**) y ruta
  del certificado.

Usa **Probar conexion con SUNAT** para validar certificado y credenciales.

## 5. Emitir un comprobante

En el detalle de cualquier factura (**Facturacion -> ver factura**) aparece el
panel *Facturacion Electronica · SUNAT* con el boton **Emitir a SUNAT**, que
firma el XML, lo envia y guarda la respuesta (CDR). El XML firmado y el CDR se
almacenan en `storage/app/facturacion/pe/`.

Estados posibles: `no_enviado`, `pendiente`, `aceptado`, `observado`,
`rechazado`, `anulado`. El estado SUNAT tambien se muestra como columna en el
listado de facturas.

**Emision automatica**: si en *Estado y modo* activas "Emitir automaticamente al
registrar la factura", cada factura nueva se firma y envia a SUNAT en el momento
de crearse (siempre que el modulo este habilitado y el driver no sea "Ninguno").

## 6. Anular con Nota de Credito

Una vez que un comprobante fue **aceptado** por SUNAT, en su detalle aparece la
seccion *Nota de Credito (anulacion)*:

1. Elige el **motivo** (catalogo 09; por defecto "01 - Anulacion de la
   operacion") y una descripcion opcional.
2. Pulsa **Anular con NC**. Se emite una Nota de Credito (tipo 07) que referencia
   al comprobante afectado, se firma y se envia a SUNAT.
3. Si SUNAT la acepta, la factura queda como **anulada** (`estado = anulada`,
   `sunat_estado = anulado`) y se guarda el XML + CDR de la nota.

Las series de las notas se configuran en *Estado y modo* (por defecto `FC01`
para facturas y `BC01` para boletas).

## 7. Reporte de comprobantes electronicos

Menu **Administracion -> Comprobantes SUNAT** (`facturacion.comprobantes`):
listado de todos los comprobantes emitidos con tarjetas de resumen (aceptados,
pendientes, rechazados, anulados), filtros por estado SUNAT, tipo, texto y rango
de fechas, descarga rapida de XML/CDR y **exportacion a CSV** del resultado
filtrado.

## 8. Representacion impresa (QR) y descargas

En el detalle de la factura, una vez emitida:

- El boton **Imprimir** genera la representacion impresa con el **codigo QR**
  segun el formato SUNAT
  (`RUC|Tipo|Serie|Correlativo|IGV|Total|Fecha|TipoDocCliente|NumDoc|Hash`).
- El panel *Facturacion Electronica · SUNAT* incluye botones para **descargar**
  el XML firmado y el CDR, tanto del comprobante como de su Nota de Credito.

El QR se dibuja en el navegador con la libreria `qrcodejs` (CDN). Para un PDF de
alta fidelidad server-side se puede integrar el modulo de reportes de Greenter
mas adelante.

## IGV: precios con o sin impuesto

En *Estado y modo* existe la opcion **"Los precios ya incluyen IGV"**:

- **Activado (por defecto)**: el importe de cada linea se toma como precio de
  venta al publico y el sistema **extrae** la base y el IGV (18%).
- **Desactivado**: el importe de cada linea se toma como **base imponible** y el
  IGV se **suma** encima; el total del comprobante sera mayor al importe base.

## Arquitectura

- `config/facturacion.php` — catalogos SUNAT y claves de configuracion.
- `App\Support\Facturacion\FacturacionElectronica` — fachada: configuracion,
  estado y resolucion del driver.
- `App\Support\Facturacion\Contracts\EmisorSunat` — contrato de los drivers.
- `App\Support\Facturacion\Drivers\GreenterEmisor` — emision real (UBL 2.1).
- `App\Support\Facturacion\Drivers\NullEmisor` — no envia (deja pendiente).
- `App\Http\Controllers\FacturacionElectronicaController` — configuracion,
  prueba de conexion y emision.

Para agregar Notas de Credito u otro proveedor, crea un nuevo driver que
implemente `EmisorSunat` y registralo en `FacturacionElectronica::emisor()`.
