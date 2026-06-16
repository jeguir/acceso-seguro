# Arquitectura general

## Objetivo

Acceso Seguro es un plugin WordPress que implementa mecanismos de validación, limitación de intentos y análisis de riesgo sobre los procesos de acceso, registro y recuperación de contraseña.

El plugin se integra con los mecanismos nativos de autenticación de WordPress y añade una capa adicional de validación, puntuación, limitación de intentos y bloqueo progresivo.

## Arquitectura de alto nivel

```text
Usuario
│
├─ Popup de acceso
│
├─ Login AJAX
├─ Registro AJAX
└─ Recuperación de contraseña AJAX
        │
        ▼
AjaxEndpoints
        │
        ▼
Engine
│
├─ ProgressiveBlocker
├─ RateLimiter
├─ EmailSignal
└─ UsernameSignal
        │
        ▼
WordPress
```

## Componentes principales

### Frontend

Responsable de mostrar la interfaz de acceso, registro y recuperación de contraseña al usuario.

### AjaxEndpoints

Punto de entrada de las peticiones AJAX del plugin.

Gestiona:

* Login
* Registro
* Recuperación de contraseña

### Engine

Motor central del sistema antispam.

Coordina:

* Bloqueo progresivo
* Limitación de intentos
* Señales antispam
* Sistema de puntuación
* Resultado final

### RateLimiter

Controla la frecuencia de uso de determinadas acciones mediante ventanas temporales configurables.

### ProgressiveBlocker

Aplica bloqueos temporales crecientes cuando se detectan reincidencias.

### Signals

Conjunto de señales utilizadas para calcular la puntuación de riesgo.

Actualmente se han identificado:

* EmailSignal
* UsernameSignal

### Persistencia

El plugin utiliza:

* Opciones de WordPress
* Tablas propias para rate limiting
* Tablas propias para bloqueos
* Tablas propias para logs

### WordPress

La autenticación y creación de usuarios se realiza mediante las APIs nativas de WordPress.
