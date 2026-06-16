# Rate Limiting

## Objetivo

El componente RateLimiter controla la frecuencia con la que determinadas acciones pueden ejecutarse desde un mismo origen.

Su finalidad es reducir intentos automatizados, ataques de fuerza bruta y abusos sobre los formularios gestionados por Acceso Seguro.

## Acciones protegidas

Actualmente se han identificado límites independientes para:

* Login
* Registro
* Recuperación de contraseña

Cada acción mantiene su propio contador.

Los intentos realizados sobre una acción no afectan a las demás.

## Identificación de origen

El sistema utiliza la dirección IP del cliente como base para el control de frecuencia.

La IP no se almacena en texto plano.

Para generar las claves internas se utiliza un hash derivado de la IP.

## Funcionamiento

El flujo general es:

```text
Solicitud
│
├─ Obtener clave de control
├─ Recuperar contador actual
├─ Verificar ventana temporal
├─ Reiniciar contador si procede
├─ Incrementar contador
├─ Guardar estado
└─ Comprobar límite
```

Si el límite configurado es superado, la solicitud es rechazada.

## Ventanas temporales

Cada acción dispone de:

* Duración de ventana
* Número máximo de intentos

Ambos valores son configurables desde la configuración del plugin.

## Modos de almacenamiento

Se han identificado dos mecanismos de almacenamiento.

### Transients

Modo predeterminado.

Utiliza la API de Transients de WordPress para almacenar los contadores temporales.

### Base de datos

Modo alternativo.

Utiliza una tabla específica para almacenar la información de rate limiting.

Tabla identificada:

```text
wp_as_rate_limit
```

El prefijo real depende de la configuración de WordPress.

## Estructura de almacenamiento

La tabla de rate limiting contiene los siguientes campos:

* rl_key
* start_ts
* count
* expires_ts

## Integración con el sistema antispam

RateLimiter se ejecuta antes de las señales antispam.

Cuando se supera un límite:

* La solicitud es rechazada.
* Se registra una infracción.
* Puede generarse un nuevo strike para el sistema de bloqueo progresivo.

## Consideraciones de privacidad

El sistema no almacena direcciones IP en texto plano dentro de los mecanismos de control identificados durante la auditoría.
