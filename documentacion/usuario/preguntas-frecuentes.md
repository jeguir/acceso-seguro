# Preguntas frecuentes

## ¿Necesita CAPTCHA para funcionar?

No.

Acceso Seguro ha sido diseñado para utilizar mecanismos propios de validación y análisis de riesgo.

El plugin puede proteger formularios de acceso y registro sin necesidad de utilizar CAPTCHA tradicionales.

## ¿Puede utilizarse junto con CAPTCHA?

Sí.

Acceso Seguro es compatible con otras medidas de seguridad y puede utilizarse como capa adicional de protección.

## ¿Qué formularios protege?

Actualmente el plugin protege:

* Inicio de sesión.
* Registro de usuarios.
* Recuperación de contraseña.

## ¿Bloquea usuarios legítimos?

La configuración por defecto está diseñada para minimizar los falsos positivos.

No obstante, cualquier sistema de protección puede generar bloqueos legítimos si la configuración es demasiado agresiva.

Por este motivo se recomienda revisar periódicamente los logs.

## ¿Qué ocurre cuando una solicitud es bloqueada?

El usuario recibe un mensaje genérico configurado por el administrador.

Internamente el sistema registra la información necesaria para facilitar el análisis posterior.

## ¿Qué es el sistema de puntuación?

Es un mecanismo que permite evaluar el riesgo de una solicitud.

Cada señal detectada aporta una determinada cantidad de puntos.

Cuando la puntuación acumulada supera los umbrales configurados, la solicitud puede ser rechazada.

## ¿Qué es una señal antispam?

Una señal es una comprobación utilizada para evaluar una solicitud.

Ejemplos:

* Email sin registros MX.
* Dominios bloqueados.
* TLDs bloqueados.
* Username sospechoso.
* Secuencias anómalas de caracteres.

## ¿Qué es el bloqueo progresivo?

Es un mecanismo que aumenta la duración de los bloqueos cuando se detectan reincidencias.

Por ejemplo:

```text
Primer bloqueo:
5 minutos

Segundo bloqueo:
30 minutos

Tercer bloqueo:
120 minutos

Cuarto bloqueo:
1440 minutos
```

## ¿Qué es el rate limiting?

Es una limitación del número de intentos permitidos durante un periodo determinado.

Su objetivo es dificultar:

* ataques de fuerza bruta
* registros masivos
* abusos de recuperación de contraseña

## ¿Dónde puedo ver los bloqueos realizados?

Desde el visor de logs incluido en el plugin.

Allí podrás consultar:

* acciones realizadas
* puntuaciones obtenidas
* motivos de bloqueo
* señales detectadas

## ¿Se almacenan las direcciones IP?

Opcionalmente.

Por defecto el plugin almacena un hash de la IP en lugar de la dirección original.

Esto reduce la cantidad de información personal almacenada.

## ¿Qué significa "IP hasheada"?

Significa que la dirección IP se transforma mediante una función criptográfica antes de almacenarse.

El valor resultante permite identificar reincidencias sin guardar la IP en texto plano.

## ¿Qué ocurre si vacío los logs?

Todos los registros almacenados serán eliminados.

Antes de hacerlo se recomienda exportarlos si deseas conservar una copia.

## ¿Puedo exportar los logs?

Sí.

El visor de logs permite exportar la información en formato CSV.

## ¿Qué son los TLDs bloqueados?

Son extensiones de dominio que el sistema rechazará durante las validaciones.

Ejemplos:

```text
xyz
top
zip
mov
```

Importante:

```text
Correcto:
xyz

Incorrecto:
.xyz
```

Los TLDs deben introducirse sin el punto inicial.

## ¿Qué son los dominios bloqueados?

Son dominios concretos que serán rechazados por el sistema.

Se utilizan habitualmente para bloquear servicios de correo temporal o proveedores no deseados.

## ¿Qué configuración se recomienda?

Para la mayoría de sitios WordPress:

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

## ¿Puede afectar al rendimiento del sitio?

El impacto es muy reducido.

Las comprobaciones se ejecutan únicamente cuando se realizan acciones protegidas por el plugin.

## ¿Es compatible con WordPress?

Sí.

Acceso Seguro utiliza los mecanismos nativos de autenticación y gestión de usuarios de WordPress.

## ¿Necesito conocimientos técnicos para utilizarlo?

No.

La configuración por defecto permite utilizar el plugin sin conocimientos avanzados.

Los usuarios más técnicos pueden ajustar las reglas y umbrales según sus necesidades.

## ¿Qué debo hacer si detecto falsos positivos?

Se recomienda:

1. Revisar los logs.
2. Analizar las señales detectadas.
3. Ajustar las reglas implicadas.
4. Supervisar el comportamiento durante los días siguientes.

## ¿Qué debo hacer después de instalar el plugin?

1. Revisar la configuración inicial.
2. Comprobar login, registro y recuperación de contraseña.
3. Verificar la política de privacidad.
4. Revisar los logs durante los primeros días.
5. Ajustar la configuración si fuese necesario.
