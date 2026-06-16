# Bloqueo progresivo

## Objetivo

El componente ProgressiveBlocker gestiona bloqueos temporales crecientes cuando se detectan reincidencias en acciones protegidas por Acceso Seguro.

Su finalidad es penalizar de forma progresiva comportamientos repetidos considerados sospechosos o abusivos.

## Acciones afectadas

Los bloqueos se gestionan de forma independiente por acción.

Actualmente se han identificado acciones como:

* Login
* Registro
* Recuperación de contraseña

## Identificación del origen

El sistema asocia los bloqueos a un identificador derivado de la IP del cliente.

La IP no se almacena en texto plano.

## Funcionamiento general

Cuando se registra una infracción, el sistema incrementa el número de strikes asociados a la acción y al origen.

A partir de ese número de strikes, calcula la duración del siguiente bloqueo.

## Escalado de bloqueos

La duración de los bloqueos se define mediante una lista configurable de tiempos en minutos.

Valores por defecto identificados durante la auditoría:

```text
5,30,120,1440
```

Equivalente a:

```text
1ª infracción → 5 minutos
2ª infracción → 30 minutos
3ª infracción → 120 minutos
4ª infracción → 1440 minutos
```

Si el número de infracciones supera los escalones definidos, se utiliza el último valor disponible.

## Integración con Engine

Engine consulta ProgressiveBlocker al inicio del flujo de validación.

Si existe un bloqueo activo, la solicitud se rechaza antes de ejecutar el resto de comprobaciones.

## Relación con RateLimiter

Cuando RateLimiter detecta que se ha superado un límite configurado, puede registrarse un nuevo strike en ProgressiveBlocker.

## Relación con las señales antispam

Determinadas denegaciones generadas por el sistema antispam pueden registrar strikes y alimentar el sistema de bloqueo progresivo.

## Almacenamiento

Los bloqueos progresivos se almacenan en una tabla propia.

Tabla identificada:

```text
wp_as_blocks
```

El prefijo real depende de la configuración de WordPress.

Campos identificados:

* id
* ip_hash
* action
* strikes
* until_ts
* last_ts

## Consideraciones de privacidad

El sistema almacena un hash derivado de la IP del cliente.

Durante la auditoría no se ha identificado almacenamiento de direcciones IP en texto plano dentro del sistema de bloqueo progresivo.
