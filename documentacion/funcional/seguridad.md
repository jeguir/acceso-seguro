# Seguridad

## Objetivo

La sección Seguridad permite configurar los mecanismos principales de protección de Acceso Seguro frente a abuso, automatización y reincidencias.

Desde esta sección se gestionan las opciones relacionadas con la limitación de intentos, el almacenamiento de contadores, el score dinámico y el bloqueo progresivo.

## Opciones disponibles

### Activar limitación de intentos

Permite activar o desactivar el sistema de rate limiting.

Cuando está activo, el plugin limita el número de intentos permitidos para acciones concretas dentro de una ventana temporal.

### Almacenamiento del rate limit

Permite seleccionar el mecanismo utilizado para almacenar los contadores de intentos.

Opciones identificadas:

* Transients
* Base de datos

#### Transients

Modo recomendado desde la interfaz.

Utiliza la API de Transients de WordPress.

#### Base de datos

Modo persistente.

Utiliza una tabla propia del plugin para almacenar los contadores.

## Límites por acción

La sección permite configurar límites independientes para cada acción protegida.

Acciones identificadas:

* Login
* Registro
* Recuperar contraseña

Cada acción permite configurar:

* Ventana temporal en segundos.
* Número máximo de intentos.

## Valores por defecto identificados

### Login

```text
Ventana: 300 segundos
Máximo intentos: 8
```

### Registro

```text
Ventana: 900 segundos
Máximo intentos: 4
```

### Recuperar contraseña

```text
Ventana: 900 segundos
Máximo intentos: 4
```

## Activar score dinámico

Permite activar el sistema de score dinámico basado en señales.

Cuando está activo, el plugin puede sumar señales de riesgo antes de decidir si una solicitud debe ser aceptada o rechazada.

## Umbral de bloqueo por score dinámico

Permite definir el umbral a partir del cual una solicitud puede ser rechazada por acumulación de señales.

Valor por defecto identificado:

```text
8
```

## Activar bloqueo progresivo por reincidencia

Permite activar el sistema de bloqueo progresivo.

Cuando está activo, el plugin puede aplicar bloqueos temporales crecientes cuando se producen reincidencias.

## Pasos de bloqueo progresivo

Permite definir los tiempos de bloqueo en minutos, separados por coma.

Valor por defecto identificado:

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

## Relación con otras secciones

La sección Seguridad controla mecanismos transversales que afectan a:

* Login
* Registro
* Recuperación de contraseña
* Sistema de puntuación
* Señales antispam
* Bloqueo progresivo
* Logs
