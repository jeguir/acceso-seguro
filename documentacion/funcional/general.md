# General

## Objetivo

La sección General permite configurar el comportamiento básico de Acceso Seguro.

Agrupa las opciones principales que afectan a la activación del plugin, la visualización del popup, el registro de usuarios, los mensajes públicos y la página de política de privacidad.

## Opciones disponibles

### Activar protección

Permite activar o desactivar la protección principal del plugin.

Cuando esta opción está desactivada, el sistema de protección no actúa sobre las solicitudes gestionadas por Acceso Seguro.

### Activar popup automático

Permite inyectar automáticamente el popup de acceso en todo el sitio.

Esta opción evita tener que insertar manualmente el popup en cada plantilla o página.

### Permitir registro de usuarios

Permite mostrar la opción de registro dentro del popup.

Cuando esta opción está desactivada, el registro no se muestra desde la interfaz del popup.

### Mensaje genérico de registro

Permite configurar el mensaje público mostrado cuando una solicitud de registro es rechazada o no puede completarse.

Esta opción ayuda a evitar mensajes demasiado específicos que puedan revelar información sensible.

### Mensaje genérico de login

Permite configurar el mensaje público mostrado cuando una solicitud de login es rechazada o no puede completarse.

Esta opción ayuda a reducir la exposición de información sobre el motivo exacto del rechazo.

### Página de política de privacidad

Permite seleccionar la página de política de privacidad utilizada por el plugin.

Si no se selecciona una página específica, el plugin puede utilizar la página configurada en WordPress desde:

```text
Ajustes → Privacidad
```

## Relación con otras secciones

La sección General afecta al comportamiento base del plugin y condiciona el funcionamiento de otras áreas como:

* Popup de acceso
* Registro de usuarios
* Sistema antispam
* Mensajes públicos
* Política de privacidad
