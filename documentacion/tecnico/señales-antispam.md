# Señales antispam

## Objetivo

Las señales antispam son componentes encargados de analizar indicadores concretos de riesgo dentro de una solicitud.

El resultado de cada señal puede aportar puntuación al sistema antispam o provocar una denegación, según la lógica interna de cada señal y la configuración activa.

## Señales identificadas

Durante la auditoría se han identificado dos señales activas:

* EmailSignal
* UsernameSignal

## EmailSignal

EmailSignal analiza direcciones de correo electrónico.

### Comprobaciones identificadas

La señal puede evaluar:

* TLD del dominio
* Dominio completo
* Existencia de registros MX

### TLD bloqueados

El sistema permite definir una lista de TLD considerados sospechosos.

Ejemplos de valores por defecto identificados:

```text
xyz
top
zip
mov
```

Cuando el TLD del email coincide con la lista configurada, la señal puede sumar puntuación o denegar la solicitud según la configuración activa.

### Dominios bloqueados

El sistema permite definir dominios completos bloqueados.

Cuando el dominio del email coincide con la lista configurada, la señal puede denegar la solicitud directamente.

### Verificación MX

Si la comprobación MX está activada, el sistema verifica si el dominio del email tiene registros MX.

Cuando no se detectan registros MX, la señal puede sumar puntuación de riesgo.

## UsernameSignal

UsernameSignal analiza nombres de usuario.

### Aplicación por acción

Durante la auditoría se ha identificado que UsernameSignal no se aplica en la acción de registro.

El propio código indica que, en el registro AJAX, el nombre de usuario puede derivarse del email y no se considera fiable para este análisis.

### Comprobaciones identificadas

La señal puede evaluar:

* Longitud mínima
* Ratio mínimo de vocales
* Secuencias largas de consonantes
* Patrón nombre.apellido

### Longitud mínima

Permite penalizar nombres de usuario demasiado cortos según la configuración definida.

### Ratio de vocales

Permite detectar nombres con una proporción baja de vocales respecto al total de letras.

### Secuencias de consonantes

Permite detectar secuencias largas de consonantes consecutivas.

### Patrón nombre.apellido

Permite detectar nombres de usuario con estructura similar a:

```text
nombre.apellido
```

Este patrón suma puntuación, pero no deniega directamente.

## Integración con Engine

Engine ejecuta las señales después de comprobar:

* Bloqueos progresivos activos
* Límites de frecuencia

Las puntuaciones generadas por las señales se acumulan y se utilizan para determinar el resultado final.

## Diferencia entre señales

EmailSignal puede generar puntuación o denegación directa.

UsernameSignal, según la auditoría realizada, únicamente aporta puntuación y no deniega directamente.

## Trazabilidad

Las señales devuelven información sobre los indicadores detectados.

Esta información puede utilizarse posteriormente en logs y análisis interno del comportamiento antispam.
