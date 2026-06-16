# Configuración general

## Introducción

Acceso Seguro incorpora un sistema de configuración que permite adaptar el comportamiento del plugin a las necesidades de cada sitio web.

Todas las opciones están disponibles desde el panel de administración de WordPress.

## Sección General

### Activar protección

Permite activar o desactivar completamente el sistema de protección de Acceso Seguro.

Valor recomendado:

```text
Activado
```

### Popup automático

Permite inyectar automáticamente el popup de acceso en todo el sitio.

Valor recomendado:

```text
Desactivado
```

Actívalo únicamente cuando desees mostrar el popup de forma global.

### Permitir registro de usuarios

Controla si el formulario de registro se muestra dentro del popup.

Valor recomendado:

```text
Activado
```

### Mensaje genérico de registro

Mensaje mostrado cuando un intento de registro no puede completarse.

Se recomienda utilizar mensajes genéricos para evitar revelar información técnica.

### Mensaje genérico de login

Mensaje mostrado cuando un inicio de sesión no puede completarse.

Se recomienda no indicar el motivo exacto del rechazo.

### Página de política de privacidad

Permite seleccionar la página de privacidad utilizada por el plugin.

Si no se selecciona ninguna página, se utilizará la definida en:

```text
Ajustes → Privacidad
```

## Estilos del popup

Esta sección permite personalizar la apariencia visual del popup sin necesidad de escribir CSS.

### Ancho máximo

Controla el ancho máximo del popup.

Valor por defecto:

```text
420 px
```

### Padding interno

Define el espacio interior del popup.

Valor por defecto:

```text
18 px
```

### Radio de borde

Permite redondear las esquinas del popup.

Valor por defecto:

```text
14 px
```

### Opacidad del fondo

Controla la transparencia del fondo oscuro que aparece detrás del popup.

Valor por defecto:

```text
0.55
```

### Color principal

Color utilizado para los elementos principales de la interfaz.

### Color de títulos

Color utilizado para los encabezados del popup.

### Fondo de los inputs

Color de fondo de los campos del formulario.

### Color de borde de los inputs

Color utilizado en los bordes de los campos.

### Grosor de borde de los inputs

Define el grosor del borde.

Valor recomendado:

```text
1–2 px
```

### Color de fondo

Color general del popup.

### Color de texto

Color principal del contenido textual.

### Restablecer colores

Permite recuperar la configuración visual predeterminada.

## Seguridad

### Activar rate limiting

Activa la limitación de intentos.

Valor recomendado:

```text
Activado
```

### Almacenamiento del rate limiting

Opciones disponibles:

```text
Transient
Base de datos
```

Recomendación general:

```text
Transient
```

por ofrecer mejor rendimiento.

### Límites de login

Permite definir:

* ventana temporal
* número máximo de intentos

Configuración inicial:

```text
300 segundos
8 intentos
```

### Límites de registro

Configuración inicial:

```text
900 segundos
4 intentos
```

### Límites de recuperación de contraseña

Configuración inicial:

```text
900 segundos
4 intentos
```

### Activar score dinámico

Permite utilizar el sistema de puntuación basado en señales.

Valor recomendado:

```text
Activado
```

### Umbral de score

Define la puntuación necesaria para considerar sospechosa una solicitud.

Valor inicial:

```text
8
```

### Activar bloqueo progresivo

Permite endurecer los bloqueos cuando existen reincidencias.

Valor recomendado:

```text
Activado
```

### Pasos del bloqueo progresivo

Configuración inicial:

```text
5,30,120,1440
```

Equivalente a:

```text
5 minutos
30 minutos
2 horas
24 horas
```

## Sistema de puntuación

### Umbral final de bloqueo

Determina la puntuación total necesaria para rechazar una solicitud.

Valor por defecto:

```text
70
```

Cuanto menor sea este valor, más agresiva será la protección.

## Reglas de email

### Activar reglas de email

Activa el análisis de direcciones de correo electrónico.

Valor recomendado:

```text
Activado
```

### Comprobación MX

Verifica la existencia de registros MX válidos.

Valor recomendado:

```text
Activado
```

### Puntos por ausencia de MX

Puntuación añadida cuando no se detectan registros MX.

Valor por defecto:

```text
45
```

### TLDs bloqueados

Permite bloquear extensiones de dominio específicas.

Configuración inicial:

```text
xyz
top
zip
mov
```

Importante:

```text
Se introducen sin el punto inicial.
```

Correcto:

```text
xyz
```

Incorrecto:

```text
.xyz
```

### Dominios bloqueados

Permite introducir dominios específicos que serán rechazados durante el proceso de validación.

Se utiliza habitualmente para servicios de correo temporal.

## Reglas de username

### Activar reglas de username

Activa el análisis de nombres de usuario.

Valor recomendado:

```text
Activado
```

### Longitud mínima

Define la longitud mínima permitida.

Valor por defecto:

```text
6
```

### Ratio mínimo de vocales

Permite detectar cadenas generadas automáticamente.

Valor por defecto:

```text
0.25
```

### Racha máxima de consonantes

Define la longitud máxima de secuencias consecutivas de consonantes.

Valor por defecto:

```text
6
```

### Puntuaciones asociadas

El sistema permite ajustar la puntuación aplicada a:

* patrón nombre.apellido
* username demasiado corto
* ratio bajo de vocales
* secuencias largas de consonantes

## Sistema de logs

### Activar logging

Permite registrar eventos internos del plugin.

Valor recomendado:

```text
Activado
```

### Guardar IP hasheada

Almacena un hash de la dirección IP en lugar de la IP original.

Valor recomendado:

```text
Activado
```

### Retención de logs

Define cuántos días se conservarán los registros.

Valor por defecto:

```text
60 días
```

## Recomendaciones generales

Para la mayoría de sitios WordPress se recomienda mantener:

```text
Protección activada
Rate limiting activado
Score dinámico activado
Bloqueo progresivo activado
Reglas de email activadas
Reglas de username activadas
Logs activados
Hash de IP activado
```

Esta configuración proporciona un equilibrio adecuado entre protección, rendimiento y facilidad de administración.
