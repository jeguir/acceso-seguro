# Primeros pasos

## Introducción

Tras instalar y activar Acceso Seguro, el plugin ya incorpora una configuración inicial diseñada para ofrecer protección desde el primer momento.

No obstante, se recomienda revisar la configuración antes de utilizarlo en producción.

## Comprobar que el plugin está activo

Accede a:

```text
Plugins → Plugins instalados
```

y verifica que Acceso Seguro aparece como activo.

## Acceder a la configuración

Desde el área de administración de WordPress, accede al menú de Acceso Seguro.

Desde esta pantalla podrás revisar y modificar todas las opciones del plugin.

## Configuración inicial por defecto

Acceso Seguro se instala con una configuración inicial orientada a ofrecer protección inmediata.

### Protección principal

```text
Activada
```

La protección antispam está habilitada por defecto.

### Registro de usuarios

```text
Activado
```

El formulario de registro está disponible desde el primer momento.

### Popup automático

```text
Desactivado
```

El popup no se inyecta automáticamente en todas las páginas.

Si deseas utilizarlo de forma global, deberás activarlo manualmente.

## Rate limiting

La limitación de intentos está activada por defecto.

Configuración inicial:

```text
Login:
8 intentos cada 300 segundos

Registro:
4 intentos cada 900 segundos

Recuperación de contraseña:
4 intentos cada 900 segundos
```

Almacenamiento utilizado:

```text
transient
```

## Sistema de puntuación

El sistema de puntuación dinámica está activado por defecto.

Configuración inicial:

```text
Score dinámico:
Activado

Umbral inicial:
8

Umbral final de bloqueo:
70
```

## Bloqueo progresivo

El sistema de bloqueo progresivo está activado por defecto.

Configuración inicial:

```text
5 minutos
30 minutos
120 minutos
1440 minutos
```

Esto permite aumentar progresivamente el tiempo de bloqueo cuando se detectan reincidencias.

## Reglas de email

Las reglas de email están activadas por defecto.

Configuración inicial:

```text
Comprobación MX:
Activada

Puntos por ausencia de MX:
45
```

TLDs bloqueados inicialmente:

```text
xyz
top
zip
mov
```

## Reglas de username

Las reglas de username están activadas por defecto.

Estas reglas permiten detectar nombres de usuario potencialmente automatizados o sospechosos.

## Sistema de logs

El registro de eventos está activado por defecto.

Configuración inicial:

```text
Logs activados

Hash de IP activado

Retención:
60 días
```

## Verificación recomendada

Después de la instalación se recomienda:

1. Guardar la configuración una primera vez.
2. Verificar el funcionamiento del login.
3. Verificar el funcionamiento del registro.
4. Verificar la recuperación de contraseña.
5. Revisar los logs generados durante las primeras pruebas.
6. Confirmar que no existen falsos positivos.

## Recomendaciones para producción

Antes de utilizar el plugin en un sitio con tráfico real se recomienda:

* Revisar las reglas de email.
* Revisar las reglas de username.
* Verificar la política de privacidad utilizada.
* Confirmar la configuración de logs.
* Comprobar periódicamente el visor de logs durante los primeros días.

## Siguiente paso

Una vez revisada la configuración inicial, consulta:

```text
configuracion-general.md
```

para conocer en detalle todas las opciones disponibles.
