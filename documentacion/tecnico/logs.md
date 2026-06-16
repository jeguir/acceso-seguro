# Logs

## Objetivo

El sistema de logs de Acceso Seguro permite registrar eventos internos relacionados con el funcionamiento del sistema antispam.

Su finalidad es facilitar el análisis posterior de incidencias, comportamientos sospechosos y decisiones tomadas por el motor de protección.

## Arquitectura

Durante la auditoría se han identificado los siguientes componentes relacionados con el sistema de logs:

* Logger
* LogRepository
* LogsPage

## Almacenamiento

Los logs se almacenan en una tabla propia.

Tabla identificada:

```text
wp_as_logs
```

El prefijo real depende de la configuración de WordPress.

## Estructura identificada

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

## Información registrada

Durante la auditoría se ha identificado el almacenamiento de:

### Fecha y hora

Campo:

```text
created_at
```

Permite conocer cuándo se produjo el evento.

### Acción

Campo:

```text
action
```

Permite identificar la acción asociada al evento registrado.

### Origen

Campo:

```text
source
```

Permite identificar el componente que generó el registro.

### Identificador anonimizado

Campo:

```text
ip_hash
```

El sistema utiliza un hash derivado de la IP.

No se ha identificado almacenamiento de la IP en texto plano.

### User Agent

Campo:

```text
user_agent
```

Permite registrar información sobre el cliente utilizado durante la solicitud.

### Identificador

Campo:

```text
identifier
```

Permite asociar información adicional relacionada con la solicitud.

### Motivo

Campo:

```text
reason_code
```

Permite registrar el motivo asociado al evento.

### Puntuación

Campo:

```text
score
```

Permite almacenar la puntuación calculada por el sistema antispam cuando procede.

### Metadatos

Campo:

```text
meta_json
```

Permite almacenar información adicional estructurada en formato JSON.

## Integración con el sistema antispam

Los logs pueden registrar información relacionada con:

* señales antispam
* puntuaciones obtenidas
* decisiones de aceptación o rechazo
* incidencias detectadas

## Retención

Durante la auditoría se ha identificado un mecanismo de eliminación de logs antiguos.

La duración de la retención depende de la configuración del plugin.

## Administración

La existencia de los componentes LogsPage y LogRepository indica la disponibilidad de funcionalidades de consulta y gestión de logs desde el área de administración.

La funcionalidad concreta se documentará tras la auditoría completa del módulo de administración.

## Privacidad

Durante la auditoría realizada no se ha identificado almacenamiento de direcciones IP en texto plano dentro del sistema de logs.

El almacenamiento utiliza identificadores hash derivados de la IP.
