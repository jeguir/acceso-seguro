# AJAX y endpoints

## Objetivo

Acceso Seguro utiliza endpoints AJAX para gestionar las operaciones de autenticación sin recargar la página.

Durante la auditoría se ha identificado una implementación centralizada en el componente AjaxEndpoints.

## Responsabilidades

El componente AjaxEndpoints actúa como punto de entrada para las solicitudes realizadas desde la interfaz del plugin.

Sus responsabilidades principales son:

* validar solicitudes
* aplicar medidas de seguridad
* ejecutar el sistema antispam
* interactuar con WordPress
* devolver respuestas JSON

## Endpoints identificados

Durante la auditoría se han identificado los siguientes endpoints AJAX:

### Login

```text
as_login
```

Responsable de autenticar usuarios mediante las APIs nativas de WordPress.

### Registro

```text
as_register
```

Responsable de crear nuevas cuentas de usuario cuando el registro está permitido.

### Recuperación de contraseña

```text
as_forgot
```

Responsable de iniciar el proceso de restablecimiento de contraseña.

## Protección CSRF

Todas las acciones auditadas utilizan validación mediante nonce.

Durante la auditoría se ha identificado el uso de:

```text
wp_verify_nonce()
```

y del método interno:

```text
requireNonce()
```

## Validación de redirecciones

Las URLs de redirección son validadas antes de utilizarse.

Durante la auditoría se ha identificado el uso de:

```text
wp_validate_redirect()
```

mediante el método:

```text
resolveRedirect()
```

Esto ayuda a prevenir redirecciones externas no autorizadas.

## Integración con el sistema antispam

Antes de ejecutar las operaciones de autenticación, los endpoints pueden delegar la evaluación al motor antispam.

Flujo identificado:

```text
Solicitud AJAX
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
```

## Login

La autenticación utiliza los mecanismos nativos de WordPress.

Durante la auditoría se ha identificado el uso de:

```text
wp_signon()
```

No se ha identificado un sistema de autenticación propio.

## Registro

La creación de usuarios utiliza las APIs nativas de WordPress.

Durante la auditoría se ha identificado el uso de:

```text
wp_create_user()
```

No se ha identificado una implementación personalizada de almacenamiento de usuarios.

## Recuperación de contraseña

La funcionalidad de recuperación de contraseña se implementa desde el propio plugin.

Durante la auditoría se ha identificado el siguiente flujo:

```text
Localizar usuario
        │
        ▼
Generar reset key
        │
        ▼
Construir URL de recuperación
        │
        ▼
Enviar correo electrónico
```

Se ha identificado el uso de:

```text
get_password_reset_key()
wp_mail()
```

## Protección anti-enumeración

Durante la auditoría se ha identificado una estrategia destinada a dificultar la enumeración de usuarios.

Se utiliza una duración mínima de respuesta mediante:

```text
enforceMinDuration()
```

con el objetivo de reducir diferencias temporales entre distintos resultados.

También se ha identificado el uso de mensajes genéricos en el proceso de recuperación de contraseña para evitar revelar si una cuenta existe o no.

## Formato de respuesta

Las respuestas son devueltas en formato JSON para su tratamiento desde la interfaz JavaScript del plugin.

## Dependencia de WordPress

La lógica de autenticación y gestión de usuarios depende de las APIs nativas de WordPress.

Acceso Seguro actúa como capa adicional de protección y validación sobre dichos mecanismos.
