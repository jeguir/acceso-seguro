# Visor de logs

## Objetivo

El visor de logs permite consultar desde el área de administración los eventos registrados por Acceso Seguro.

Su finalidad es facilitar la revisión de intentos bloqueados, motivos de rechazo, puntuaciones y señales detectadas por el sistema antispam.

## Acceso

La pantalla está disponible para usuarios con permisos de administración.

Durante la auditoría se ha identificado el uso del permiso:

```text
manage_options
```

## Listado principal

El visor muestra un listado de registros recientes.

Durante la auditoría se ha identificado un límite de visualización de:

```text
50 registros
```

## Columnas del listado

El listado principal muestra las siguientes columnas:

* ID
* Fecha
* Acción
* Origen
* Motivo
* Score
* IP (hash)
* Detalles

## Filtros disponibles

### Filtro por acción

Permite filtrar los registros por acción.

Acciones identificadas en el filtro:

* Login
* Registro

### Filtro por motivo

Permite filtrar los registros por código de motivo.

Los motivos se obtienen dinámicamente a partir de los registros existentes.

## Acciones disponibles

### Filtrar

Aplica los filtros seleccionados al listado de logs.

### Quitar filtro

Permite volver al listado sin filtros activos.

### Exportar CSV

Permite exportar los logs en formato CSV.

La exportación respeta los filtros activos cuando existen.

### Vaciar logs

Permite eliminar todos los registros almacenados.

Esta acción utiliza nonce de seguridad y solicita confirmación antes de ejecutarse.

## Vista de detalle

Cada registro incluye un enlace para ver sus detalles.

La vista de detalle muestra:

* ID
* Fecha
* Acción
* Origen
* Motivo
* Score
* IP hash

## Señales y metadatos

La vista de detalle muestra los metadatos asociados al registro en formato JSON.

Estos datos pueden incluir información sobre las señales antispam detectadas durante la evaluación.

## Bloqueo progresivo

Cuando el motivo del registro está asociado a un bloqueo progresivo, la vista de detalle puede mostrar información adicional:

* número de reincidencias
* fecha/hora de finalización del bloqueo
* estado activo o expirado

## Exportación CSV

La exportación CSV incluye los siguientes campos:

* created_at
* action
* source
* reason_code
* score
* ip_hash
* signals_json

## Seguridad

La pantalla comprueba permisos de administración antes de mostrar, exportar o vaciar logs.

La acción de vaciado utiliza verificación mediante nonce.

## Consideraciones

El visor de logs es una herramienta de análisis interno.

No forma parte de la interfaz pública del plugin y está orientado a administradores del sitio.
