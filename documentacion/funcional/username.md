# Username

## Objetivo

La sección Username permite configurar las reglas aplicadas a los nombres de usuario dentro del sistema antispam de Acceso Seguro.

Estas reglas permiten detectar patrones sospechosos y sumar puntuación de riesgo cuando el nombre de usuario cumple determinadas condiciones.

## Opciones disponibles

### Activar reglas de username

Permite activar o desactivar las comprobaciones específicas sobre nombres de usuario.

Cuando esta opción está desactivada, las reglas de username no se aplican.

### Longitud mínima

Permite definir la longitud mínima esperada para un nombre de usuario.

Valor por defecto identificado:

```text
6
```

Si el nombre de usuario es más corto que este valor, puede sumar puntos de riesgo.

### Ratio mínimo de vocales

Permite definir la proporción mínima de vocales respecto al total de letras del nombre de usuario.

Valor por defecto identificado:

```text
0.25
```

El valor debe estar entre `0` y `1`.

### Racha máxima de consonantes

Permite definir el número máximo de consonantes consecutivas aceptado antes de generar una penalización.

Valor por defecto identificado:

```text
6
```

### Puntos patrón nombre.apellido

Permite definir cuántos puntos se añaden cuando el username coincide con un patrón tipo:

```text
nombre.apellido
```

Valor por defecto identificado:

```text
15
```

### Puntos si username corto

Permite definir cuántos puntos se añaden cuando el username no alcanza la longitud mínima configurada.

Valor por defecto identificado:

```text
40
```

### Puntos ratio vocales bajo

Permite definir cuántos puntos se añaden cuando el username tiene una proporción de vocales inferior al mínimo configurado.

Valor por defecto identificado:

```text
30
```

### Puntos racha consonantes larga

Permite definir cuántos puntos se añaden cuando el username contiene una racha de consonantes superior al límite configurado.

Valor por defecto identificado:

```text
30
```

## Relación con el sistema de puntuación

Las reglas de username suman puntos al sistema antispam.

La puntuación acumulada se evalúa posteriormente según los umbrales definidos en las secciones Seguridad y Puntuación.

## Relación con las señales antispam

La sección Username configura el comportamiento de la señal técnica UsernameSignal.

Durante la auditoría técnica se identificó que UsernameSignal no se aplica durante el registro AJAX, porque el username puede derivarse del email y no se considera fiable para este análisis.

## Consideraciones

Estas reglas no bloquean directamente por sí solas.

Su función principal es aportar puntuación de riesgo al sistema antispam.
