# Sistema antispam

## Introducción

Acceso Seguro utiliza un sistema antispam multicapa diseñado para proteger los procesos de acceso, registro y recuperación de contraseña frente a abusos y automatizaciones.

A diferencia de otros sistemas basados únicamente en CAPTCHA, Acceso Seguro combina distintos mecanismos de protección que trabajan conjuntamente para analizar el riesgo de cada solicitud.

## Componentes principales

El sistema antispam está formado por varios elementos que actúan de manera coordinada.

### Rate limiting

Controla la frecuencia con la que una misma acción puede ejecutarse.

Su objetivo es dificultar:

* ataques de fuerza bruta
* intentos masivos de registro
* abuso de recuperación de contraseña

Cada acción dispone de límites independientes.

### Sistema de puntuación

El motor antispam puede asignar puntos de riesgo a una solicitud.

Cada señal detectada suma una determinada cantidad de puntos.

Cuando la puntuación total supera los umbrales configurados, la solicitud puede ser rechazada.

### Señales antispam

Las señales son comprobaciones específicas utilizadas para evaluar el riesgo de una solicitud.

Actualmente el plugin incorpora dos grupos principales de señales:

* Email
* Username

### Bloqueo progresivo

Cuando se detectan reincidencias, el sistema puede aplicar bloqueos temporales crecientes.

Esto permite endurecer progresivamente la protección frente a comportamientos repetitivos.

### Sistema de logs

Todas las acciones relevantes pueden registrarse para facilitar el análisis posterior.

Los registros permiten conocer:

* qué ocurrió
* cuándo ocurrió
* por qué se produjo un bloqueo
* qué señales participaron en la decisión

## Funcionamiento general

Cuando un usuario realiza una acción protegida, Acceso Seguro ejecuta una serie de comprobaciones.

Proceso simplificado:

```text
Solicitud
↓
Rate limiting
↓
Señales antispam
↓
Cálculo de puntuación
↓
Bloqueo progresivo (si procede)
↓
Resultado final
↓
Registro en logs
```

## Protección mediante rate limiting

El sistema controla el número de intentos permitidos dentro de una ventana temporal.

Por defecto:

```text
Login:
8 intentos cada 300 segundos

Registro:
4 intentos cada 900 segundos

Recuperación de contraseña:
4 intentos cada 900 segundos
```

Cuando se supera el límite configurado, la acción puede bloquearse temporalmente.

## Protección mediante señales de email

Las direcciones de correo electrónico pueden analizarse utilizando varias comprobaciones.

Entre ellas:

* comprobación de registros MX
* detección de dominios bloqueados
* detección de TLDs bloqueados

Estas comprobaciones permiten detectar correos potencialmente inválidos o utilizados habitualmente por sistemas automatizados.

## Protección mediante señales de username

Los nombres de usuario pueden analizarse mediante distintos criterios.

Entre ellos:

* longitud mínima
* proporción de vocales
* secuencias largas de consonantes
* patrones considerados sospechosos

Estas comprobaciones ayudan a detectar nombres de usuario generados automáticamente.

## Sistema de puntuación

Cada señal puede aportar una cantidad determinada de puntos.

Ejemplo simplificado:

```text
Email sin MX
+45 puntos

Username sospechoso
+30 puntos

Total
75 puntos
```

Si la puntuación acumulada supera los límites configurados, la solicitud puede ser rechazada.

## Bloqueo progresivo

El sistema puede incrementar la duración de los bloqueos cuando detecta reincidencias.

Configuración inicial:

```text
1ª reincidencia:
5 minutos

2ª reincidencia:
30 minutos

3ª reincidencia:
120 minutos

4ª reincidencia:
1440 minutos
```

Este mecanismo dificulta los ataques repetitivos desde una misma fuente.

## Registro de actividad

El sistema puede registrar:

* acción realizada
* motivo del bloqueo
* puntuación obtenida
* señales detectadas
* identificadores técnicos

Estos registros pueden consultarse desde el visor de logs.

## Ventajas del sistema

Entre las principales ventajas de Acceso Seguro destacan:

* protección multicapa
* análisis de riesgo configurable
* reducción del spam automatizado
* bloqueo progresivo por reincidencia
* trazabilidad mediante logs
* integración con WordPress

## Recomendaciones

Para obtener los mejores resultados se recomienda:

* mantener activado el rate limiting
* mantener activado el sistema de puntuación
* revisar periódicamente los logs
* ajustar las reglas de email según las necesidades del sitio
* supervisar posibles falsos positivos durante los primeros días de uso
