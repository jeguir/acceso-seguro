# Almacenamiento y base de datos

## Objetivo

Acceso Seguro utiliza distintos mecanismos de almacenamiento para conservar configuración, contadores temporales, bloqueos progresivos y logs internos.

## Opciones de WordPress

El plugin utiliza el sistema de opciones de WordPress para almacenar su configuración.

La estructura exacta y las opciones utilizadas se documentan en el documento específico de configuración.

## Tablas propias identificadas

Durante la auditoría se han identificado tres tablas propias:

```text
wp_as_rate_limit
wp_as_blocks
wp_as_logs
```

El prefijo real depende de la configuración de WordPress.

## Tabla de rate limiting

Tabla:

```text
wp_as_rate_limit
```

Campos identificados:

* rl_key
* start_ts
* count
* expires_ts

Uso identificado:

* almacenar contadores por acción
* controlar ventanas temporales
* aplicar límites de frecuencia

## Tabla de bloqueos progresivos

Tabla:

```text
wp_as_blocks
```

Campos identificados:

* id
* ip_hash
* action
* strikes
* until_ts
* last_ts

Uso identificado:

* almacenar strikes
* gestionar bloqueos activos
* calcular reincidencias por acción

## Tabla de logs

Tabla:

```text
wp_as_logs
```

Campos identificados:

* id
* created_at
* action
* source
* ip_hash
* user_agent
* identifier
* reason_code
* score
* meta_json

Uso identificado:

* registrar eventos internos
* conservar información de análisis antispam
* facilitar revisión posterior desde administración

## Creación de tablas

Las tablas de rate limiting y bloqueos se crean mediante la lógica de base de datos del plugin.

La tabla de logs se gestiona desde el repositorio de logs.

Durante la auditoría se ha identificado el uso de mecanismos compatibles con WordPress para la creación o actualización de tablas.

## Limpieza de datos

Se han identificado mecanismos de limpieza para:

* rate limits expirados
* logs antiguos

La retención de logs depende de la configuración del plugin.

## Privacidad

Durante la auditoría no se ha identificado almacenamiento de IPs reales en texto plano dentro de las tablas propias principales.

El plugin utiliza hashes derivados de IP para rate limiting, bloqueos y logs.
