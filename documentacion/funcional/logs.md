# Logs

## Objetivo

La sección Logs permite configurar el sistema interno de registro de eventos de Acceso Seguro.

Los registros facilitan el análisis de intentos bloqueados, actividad sospechosa y comportamiento del sistema antispam.

## Opciones disponibles

### Activar logging interno

Permite activar o desactivar el sistema de registros del plugin.

Cuando esta opción está desactivada, Acceso Seguro no almacena eventos en el sistema de logs.

### Guardar IP hasheada

Permite almacenar una versión anonimizada de la dirección IP.

La interfaz identifica esta opción como recomendada.

Cuando está activada, el sistema evita almacenar la IP original en texto plano.

### Retención de registros

Permite definir el número de días durante los que se conservarán los registros.

Valor por defecto identificado:

```text
60 días
```

Los registros que superen el período configurado podrán eliminarse durante las tareas de mantenimiento del sistema.

## Beneficios

El sistema de logs permite:

* Analizar actividad sospechosa.
* Revisar bloqueos realizados por el sistema antispam.
* Detectar patrones de abuso.
* Diagnosticar incidencias de funcionamiento.

## Privacidad

La opción de hash de IP ayuda a reducir el almacenamiento de información identificable.

Esta configuración puede resultar útil para facilitar el cumplimiento de políticas de privacidad y protección de datos.

## Relación con otras secciones

Los logs pueden registrar eventos generados por:

* Login
* Registro
* Recuperación de contraseña
* Rate limiting
* Sistema de puntuación
* Bloqueo progresivo
* Señales antispam

La implementación técnica del sistema de logs se documenta en la documentación técnica del proyecto.
