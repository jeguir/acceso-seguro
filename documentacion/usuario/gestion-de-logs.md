# Gestión de logs

## Introducción

Acceso Seguro incorpora un sistema de registro interno que permite almacenar información sobre eventos relevantes detectados por el plugin.

Los logs son una herramienta fundamental para comprender el comportamiento del sistema antispam y analizar posibles intentos de abuso.

## ¿Qué registra Acceso Seguro?

Dependiendo de la configuración aplicada, el sistema puede registrar información relacionada con:

* Intentos de login.
* Intentos de registro.
* Acciones bloqueadas.
* Eventos de rate limiting.
* Bloqueos progresivos.
* Señales antispam detectadas.
* Puntuaciones obtenidas durante la evaluación.

## Acceso al visor de logs

El visor de logs está disponible desde el área de administración de WordPress.

Solo los usuarios con permisos administrativos pueden acceder a esta información.

## Listado de registros

La pantalla principal muestra un listado de los registros almacenados.

Cada registro incluye:

* ID
* Fecha
* Acción
* Origen
* Motivo
* Score
* IP (hash)
* Enlace a los detalles

## Interpretación de los campos

### Fecha

Momento en el que se registró el evento.

### Acción

Indica la operación que estaba realizando el usuario.

Ejemplos:

```text
login
register
```

### Origen

Identifica el origen técnico del evento.

### Motivo

Código utilizado por el sistema para indicar la causa del registro o del bloqueo.

### Score

Puntuación acumulada por la solicitud.

Cuanto mayor sea la puntuación, mayor será el nivel de riesgo detectado.

### IP (hash)

Representa una versión anonimizada de la dirección IP.

Cuando la opción correspondiente está activada, la IP real no se almacena en texto plano.

## Filtrado de registros

El visor permite filtrar los registros para facilitar el análisis.

### Filtrar por acción

Permite mostrar únicamente:

* Logins
* Registros

### Filtrar por motivo

Permite mostrar únicamente registros asociados a un motivo concreto.

Los motivos disponibles se generan automáticamente a partir de los datos almacenados.

## Vista de detalle

Cada registro dispone de una vista detallada.

Esta pantalla muestra información ampliada sobre el evento seleccionado.

Información disponible:

* ID
* Fecha
* Acción
* Origen
* Motivo
* Score
* Hash de IP

## Señales detectadas

La vista de detalle incluye los metadatos almacenados por el sistema.

Esta información se presenta en formato JSON.

Puede resultar útil para:

* diagnosticar falsos positivos
* comprender la puntuación obtenida
* analizar decisiones del motor antispam

## Bloqueo progresivo

Cuando un registro está relacionado con un bloqueo progresivo, el visor puede mostrar información adicional.

Datos disponibles:

* número de reincidencias
* fecha de finalización del bloqueo
* estado actual del bloqueo

Estados posibles:

```text
Activo
Expirado
```

## Exportación CSV

El visor permite exportar los registros a un archivo CSV.

La exportación incluye:

* fecha
* acción
* origen
* motivo
* score
* hash de IP
* señales registradas

Si existen filtros activos, la exportación respetará dichos filtros.

## Vaciado de logs

La pantalla incluye una opción para eliminar todos los registros almacenados.

Antes de ejecutar esta acción:

* revisa si necesitas conservar información histórica
* exporta los registros si deseas mantener una copia

La eliminación afecta a todos los registros almacenados.

## Privacidad

Acceso Seguro permite almacenar direcciones IP de forma anonimizada mediante hash.

Esta configuración ayuda a minimizar la información personal almacenada.

Se recomienda mantener activada esta opción siempre que sea compatible con las necesidades de auditoría del sitio.

## Buenas prácticas

Se recomienda:

* revisar periódicamente los logs
* supervisar posibles falsos positivos
* exportar registros antes de eliminarlos
* mantener activado el hash de IP
* utilizar los logs para ajustar la configuración del sistema antispam

## Resolución de problemas

### No aparecen registros

Comprobar:

* que el sistema de logs está activado
* que se están produciendo eventos registrables
* que la retención de registros no es demasiado baja

### Se producen demasiados bloqueos

Revisar:

* reglas de email
* reglas de username
* umbrales de puntuación
* configuración de bloqueo progresivo

### Existen falsos positivos

Analizar los registros afectados y revisar las señales que provocaron la puntuación obtenida.
