# Sistema antispam

## Objetivo

El sistema antispam de Acceso Seguro añade una capa de protección sobre los procesos de acceso, registro y recuperación de contraseña de WordPress.

Su función es detectar comportamientos sospechosos, limitar abusos y aplicar bloqueos temporales progresivos cuando se producen reincidencias.

## Componentes

El sistema está formado por los siguientes módulos:

* Engine
* RateLimiter
* ProgressiveBlocker
* EmailSignal
* UsernameSignal

## Flujo de validación

El flujo de evaluación identificado en el código es el siguiente:

```text
Solicitud
│
├─ ProgressiveBlocker
│
├─ RateLimiter
│
├─ EmailSignal
│
├─ UsernameSignal
│
└─ Evaluación final
```

## Engine

Engine es el coordinador principal del sistema antispam.

Su responsabilidad es:

* Ejecutar las validaciones en el orden definido.
* Acumular puntuaciones.
* Determinar si la solicitud se acepta o se rechaza.
* Registrar incidencias que generen reincidencia.

## RateLimiter

Controla la frecuencia de uso de determinadas acciones.

Actualmente se han identificado límites independientes para:

* Login
* Registro
* Recuperación de contraseña

Cada acción mantiene sus propios contadores y ventanas temporales.

## ProgressiveBlocker

Gestiona los bloqueos temporales por reincidencia.

Cuando una acción genera una infracción, se registra un strike asociado a la acción y al identificador de origen.

La duración del bloqueo aumenta progresivamente según la configuración definida.

## Señales antispam

Las señales son responsables de evaluar distintos indicadores de riesgo.

Actualmente se han identificado:

### EmailSignal

Evalúa direcciones de correo electrónico.

Puede utilizar:

* TLD bloqueados
* Dominios bloqueados
* Verificación MX

### UsernameSignal

Evalúa nombres de usuario.

Actualmente analiza:

* Longitud mínima
* Ratio de vocales
* Secuencias de consonantes
* Patrón nombre.apellido

## Sistema de puntuación

Las señales pueden aportar puntuación de riesgo.

La puntuación acumulada es utilizada posteriormente por Engine para determinar si la solicitud debe aceptarse o rechazarse.

## Registro de incidencias

Las incidencias relevantes pueden generar:

* Incremento de puntuación
* Registro en logs
* Nuevos strikes
* Bloqueos progresivos
