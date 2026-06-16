# Panel de ajustes

## Objetivo

El panel de ajustes permite configurar el comportamiento general de Acceso Seguro desde el área de administración de WordPress.

Desde esta pantalla el administrador puede activar o desactivar funcionalidades, configurar el sistema antispam, personalizar la apariencia del popup y gestionar el sistema de logs.

## Ubicación

El plugin añade una pantalla propia dentro del área de administración de WordPress.

Durante la auditoría se han identificado las siguientes páginas:

* Ajustes
* Logs

Este documento describe la página de ajustes.

## Estructura general

La pantalla de ajustes está organizada en varias secciones funcionales.

Secciones identificadas:

```text
General
Estilos del popup
Seguridad
Puntuación
Email
Username
Logs
```

Cada sección agrupa configuraciones relacionadas con un componente concreto del plugin.

## General

Permite configurar el comportamiento básico del plugin.

Durante la auditoría se han identificado opciones relacionadas con:

* Activación global del plugin.
* Apertura automática del popup.
* Permitir registro de usuarios.
* URLs de redirección.
* Página de privacidad.

La configuración exacta se documenta en:

```text
general.md
```

## Estilos del popup

Permite personalizar la apariencia visual del popup de acceso.

Durante la auditoría se han identificado opciones relacionadas con:

* Dimensiones.
* Bordes.
* Sombras.
* Colores.
* Aspecto de los campos de formulario.

La configuración exacta se documenta en:

```text
estilos-del-popup.md
```

## Seguridad

Permite configurar los mecanismos de protección frente a abuso y automatización.

Durante la auditoría se han identificado opciones relacionadas con:

* Rate limiting.
* Almacenamiento de límites.
* Bloqueo progresivo.
* Umbrales de seguridad.

La configuración exacta se documenta en:

```text
seguridad.md
```

## Puntuación

Permite configurar el sistema de scoring utilizado por el motor antispam.

Durante la auditoría se han identificado opciones relacionadas con:

* Activación del sistema de puntuación.
* Umbral de rechazo.

La configuración exacta se documenta en:

```text
puntuacion.md
```

## Email

Permite configurar las comprobaciones aplicadas a direcciones de correo electrónico.

Durante la auditoría se han identificado opciones relacionadas con:

* Activación de la señal.
* Verificación MX.
* TLD bloqueados.
* Dominios bloqueados.
* Penalizaciones asociadas.

La configuración exacta se documenta en:

```text
email.md
```

## Username

Permite configurar las comprobaciones aplicadas a nombres de usuario.

Durante la auditoría se han identificado opciones relacionadas con:

* Longitud mínima.
* Ratio de vocales.
* Secuencias de consonantes.
* Penalizaciones asociadas.

La configuración exacta se documenta en:

```text
username.md
```

## Logs

Permite configurar el sistema de registro de eventos.

Durante la auditoría se han identificado opciones relacionadas con:

* Activación del sistema de logs.
* Hash de IP.
* Retención de registros.

La configuración exacta se documenta en:

```text
logs.md
```

## Relación con la documentación técnica

Este documento describe la organización funcional del panel de administración.

La implementación interna de cada componente se documenta en la sección técnica del proyecto.
