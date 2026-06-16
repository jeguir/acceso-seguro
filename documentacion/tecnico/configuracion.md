# Configuración

## Objetivo

Acceso Seguro utiliza un sistema centralizado de configuración que permite controlar el comportamiento de los distintos componentes del plugin.

Durante la auditoría se ha identificado que la configuración es utilizada por el motor antispam, el sistema de limitación de intentos, los bloqueos progresivos y las señales de análisis.

## Arquitectura

Los distintos componentes del plugin consultan la configuración a través de la capa de opciones interna.

La configuración determina el comportamiento de:

* Engine
* RateLimiter
* ProgressiveBlocker
* EmailSignal
* UsernameSignal
* Sistema de logs

## Configuración general

Durante la auditoría se han identificado opciones relacionadas con:

* activación del sistema antispam
* umbrales de puntuación
* mensajes públicos mostrados al usuario
* bloqueo progresivo
* almacenamiento temporal
* retención de logs

## Sistema de puntuación

Se han identificado configuraciones relacionadas con el cálculo y evaluación del riesgo.

### Activación del sistema de puntuación

Permite habilitar o deshabilitar el uso de puntuaciones acumuladas durante el análisis.

### Umbral de puntuación

Define el valor a partir del cual una solicitud puede ser rechazada por el sistema antispam.

Durante la auditoría se han identificado referencias a umbrales configurables utilizados por Engine.

## Rate Limiting

Durante la auditoría se han identificado configuraciones independientes para:

* Login
* Registro
* Recuperación de contraseña

Cada acción puede definir:

* número máximo de intentos
* duración de la ventana temporal

## Almacenamiento del Rate Limiting

Se han identificado dos modos de almacenamiento:

### Transients

Utiliza la API de Transients de WordPress.

### Base de datos

Utiliza una tabla propia del plugin para almacenar los contadores.

## Bloqueo progresivo

El sistema permite definir una secuencia de tiempos de bloqueo.

Durante la auditoría se identificó la siguiente configuración por defecto:

```text
5,30,120,1440
```

Estos valores representan minutos de bloqueo progresivo.

## Configuración de EmailSignal

Durante la auditoría se han identificado opciones relacionadas con:

### TLD bloqueados

Permite definir extensiones de dominio consideradas sospechosas.

Ejemplos identificados:

```text
xyz
top
zip
mov
```

### Dominios bloqueados

Permite definir dominios completos bloqueados.

### Verificación MX

Permite habilitar o deshabilitar la comprobación de registros MX en los dominios de correo electrónico.

### Puntuaciones asociadas

Las distintas comprobaciones pueden aportar puntuaciones al sistema de riesgo.

## Configuración de UsernameSignal

Durante la auditoría se han identificado opciones relacionadas con:

### Longitud mínima

Define la longitud mínima considerada aceptable.

### Ratio mínimo de vocales

Define la proporción mínima de vocales respecto al total de letras.

### Secuencia máxima de consonantes

Define el número máximo de consonantes consecutivas permitido antes de generar una penalización.

### Puntuaciones asociadas

Cada comprobación puede aportar puntuación al sistema de riesgo.

## Configuración de logs

Durante la auditoría se han identificado opciones relacionadas con:

* activación del sistema de logs
* retención de registros

La duración de conservación de los logs depende de la configuración definida.

## Mensajes públicos

Engine utiliza mensajes configurables para mostrar información al usuario cuando una solicitud es rechazada.

Durante la auditoría se identificaron configuraciones específicas para:

* Login
* Registro

## Consideraciones

La configuración exacta disponible para el administrador deberá documentarse nuevamente tras la auditoría completa de los módulos de administración y de la interfaz de configuración.
