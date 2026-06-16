# Puntuación

## Objetivo

La sección Puntuación permite configurar el umbral final utilizado por el sistema antispam para rechazar solicitudes en función de la puntuación acumulada.

Este sistema trabaja conjuntamente con las señales antispam configuradas en las secciones Email y Username.

## Opciones disponibles

### Umbral de bloqueo (score)

Permite definir la puntuación mínima necesaria para que una solicitud sea rechazada por el sistema antispam.

Valor por defecto identificado:

```text
70
```

## Funcionamiento

Las señales antispam pueden aportar puntos de riesgo durante el análisis de una solicitud.

Cuando la puntuación acumulada alcanza o supera el umbral configurado, el motor antispam puede rechazar la solicitud.

## Relación con otras secciones

La sección Puntuación trabaja conjuntamente con:

* Seguridad
* Email
* Username

Las secciones Email y Username generan puntuaciones.

La sección Seguridad permite activar o desactivar el sistema de score dinámico.

La sección Puntuación define el límite final utilizado para tomar la decisión de bloqueo.

## Consideraciones

Un valor más bajo hará que el sistema sea más restrictivo.

Un valor más alto hará que el sistema sea más permisivo.

La configuración óptima dependerá del nivel de spam recibido y de la agresividad deseada en la protección.
