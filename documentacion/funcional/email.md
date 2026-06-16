# Email

## Objetivo

La sección Email permite configurar las reglas aplicadas a las direcciones de correo electrónico dentro del sistema antispam de Acceso Seguro.

Estas reglas pueden aportar puntuación de riesgo o bloquear determinados dominios según la configuración activa.

## Opciones disponibles

### Activar reglas de email

Permite activar o desactivar las comprobaciones específicas sobre direcciones de correo electrónico.

Cuando esta opción está desactivada, las reglas de email no se aplican.

### Comprobar MX

Permite comprobar si el dominio del correo electrónico tiene registros MX.

Si el dominio no tiene registros MX y la comprobación está activa, el sistema puede sumar puntos de riesgo.

### Puntos si falta MX

Permite definir cuántos puntos se añaden cuando un dominio de correo electrónico no tiene registros MX.

Valor por defecto identificado:

```text
45
```

### TLDs bloqueados

Permite definir extensiones de dominio consideradas sospechosas.

Los TLDs pueden introducirse:

* uno por línea
* separados por coma

El sistema espera los TLDs sin punto inicial.

Ejemplo correcto:

```text
xyz
top
website
online
```

Ejemplo incorrecto:

```text
.xyz
```

La interfaz incluye un aviso cuando se detectan TLDs escritos con punto inicial.

### Dominios bloqueados

Permite definir dominios completos que deben bloquearse.

Esta opción está pensada especialmente para dominios asociados a emails temporales o desechables.

Ejemplo:

```text
mailinator.com
tempmail.com
```

## Relación con el sistema de puntuación

Las reglas de email pueden sumar puntos al sistema antispam.

La puntuación final se evalúa junto con el umbral definido en la sección Puntuación.

## Relación con las señales antispam

La sección Email configura el comportamiento de la señal técnica EmailSignal.

La implementación interna se documenta en la documentación técnica del sistema antispam.

## Consideraciones

Conviene introducir los TLDs sin punto inicial para que el bloqueo funcione correctamente.

Los dominios completos deben introducirse sin protocolo y sin rutas.
