# SOLICITUD-INSTITUCIONAL-DESPLIEGUE-HTTPS-EPIC003-001

## Solicitud de antecedentes y acceso técnico controlado para resolver `session.cookie_secure`

## 1. Identificación

```text
Proyecto:
Modernización incremental de la Intranet del Doctorado en Antropología
conjunto UCN–UTA

EPIC:
EPIC-003 — Consolidación del modelo de identidad, roles y
participación académica

AT relacionado:
AT-EPIC003-CONFIGURACION-GLOBAL-SESION-001

Inspección previa:
INSPECCIÓN-DESPLIEGUE-HTTPS-EPIC003-001

Asunto:
Obtención de antecedentes productivos necesarios para evaluar y
configurar session.cookie_secure.
```

## 2. Contexto

La aplicación ya posee una política de sesión centralizada y publicada que
establece:

```text
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.use_trans_sid = 0
session.cookie_httponly = 1
session.cookie_samesite = Lax
```

La directiva:

```text
session.cookie_secure
```

permanece pendiente porque su activación depende de la infraestructura
productiva y no puede resolverse responsablemente desde el repositorio o el
entorno local.

No se solicita modificar producción en esta etapa.

## 3. Objetivo de la solicitud

Solicitar antecedentes verificables para determinar:

```text
- dominio y URL productiva;
- obligatoriedad de HTTPS;
- redirección HTTP→HTTPS;
- certificado TLS;
- terminación TLS;
- proxy reverso, balanceador o CDN;
- SAPI PHP;
- configuración productiva;
- scope de session.cookie_secure;
- responsable técnico;
- autoridad de aprobación;
- capacidad de validación y reversión.
```

La información permitirá decidir si corresponde crear una Task de despliegue
controlada.

## 4. Información institucional requerida

### 4.1 Identificación del entorno

Solicitar:

```text
Dominio productivo:
Subdominio:
URL canónica:
Ruta de acceso:
Acceso público o red institucional:
Proveedor de hosting:
Servidor o servicio utilizado:
```

No se requiere entregar credenciales en este documento.

### 4.2 Responsables

Solicitar identificación de:

```text
- propietario funcional de la intranet;
- responsable técnico del servidor;
- administrador del hosting;
- responsable de DNS;
- responsable de certificados;
- responsable de Apache/Nginx/PHP;
- responsable del despliegue;
- autoridad que aprueba cambios;
- contacto para reversión.
```

Para cada uno registrar:

```text
Nombre o unidad:
Cargo:
Institución:
Correo institucional:
Ámbito de responsabilidad:
```

### 4.3 HTTPS y certificado

Solicitar confirmación de:

```text
- HTTPS disponible;
- HTTPS obligatorio;
- accesos HTTP permitidos;
- redirección HTTP→HTTPS;
- código de redirección;
- certificado válido;
- nombres cubiertos;
- fecha de expiración;
- mecanismo de renovación;
- responsable de renovación.
```

No solicitar claves privadas ni certificados privados.

### 4.4 Terminación TLS

Solicitar indicar dónde termina TLS:

```text
[ ] Apache directo
[ ] Nginx
[ ] Proxy reverso
[ ] Balanceador
[ ] CDN
[ ] Servicio del proveedor
[ ] Otro
```

Si existe proxy, solicitar:

```text
- producto o servicio;
- IP o red de proxies confiables;
- protocolo entre proxy y backend;
- headers añadidos;
- headers eliminados;
- mecanismo de confianza;
- responsable de configuración.
```

No basta indicar solamente que existe `X-Forwarded-Proto`.

### 4.5 Configuración PHP productiva

Solicitar:

```text
Versión PHP:
SAPI:
php.ini efectivo:
session.save_handler:
session.save_path, sin exponer información sensible:
manejador personalizado:
```

Valores efectivos requeridos:

```text
session.cookie_secure
session.cookie_httponly
session.cookie_samesite
session.use_strict_mode
session.use_only_cookies
session.use_trans_sid
session.name
session.cookie_path
session.cookie_domain
```

Las evidencias pueden provenir de:

```text
- panel de hosting;
- configuración del servidor;
- salida administrativa existente;
- soporte del proveedor;
- comandos autorizados;
- documentación operacional.
```

No crear un endpoint `phpinfo()`.

### 4.6 Configuración del servidor web

Solicitar evidencia mínima, redactada cuando corresponda, de:

```text
- VirtualHost;
- ServerName;
- ServerAlias;
- reglas HTTP→HTTPS;
- SSLEngine;
- ProxyPass;
- configuración PHP por sitio;
- scope de las directivas;
- aplicaciones compartidas en el mismo servidor o cuenta.
```

No incluir:

```text
- contraseñas;
- claves privadas;
- tokens;
- secretos;
- credenciales;
- contenido de sesiones.
```

### 4.7 Ubicación posible de `session.cookie_secure`

Solicitar identificar cuál mecanismo está disponible:

```text
[ ] php.ini
[ ] VirtualHost
[ ] pool PHP-FPM
[ ] panel de hosting
[ ] .user.ini
[ ] .htaccess
[ ] soporte del proveedor
[ ] otro
```

Para la alternativa disponible, indicar:

```text
- scope;
- aplicaciones afectadas;
- permisos necesarios;
- tiempo de propagación;
- reinicio o recarga requerida;
- responsable;
- mecanismo de reversión.
```

### 4.8 Validación autorizada

Solicitar autorización para verificar posteriormente:

```text
[ ] HTTP redirige a HTTPS
[ ] certificado válido
[ ] login usa HTTPS
[ ] Set-Cookie contiene Secure
[ ] se conservan HttpOnly y SameSite=Lax
[ ] navegación conserva sesión
[ ] AJAX conserva sesión
[ ] logout expira la cookie
[ ] cookie no se envía por HTTP
[ ] no existen warnings PHP
```

Indicar si existe una cuenta de prueba institucional autorizada.

No solicitar credenciales de usuarios reales.

### 4.9 Reversión

Solicitar definir:

```text
- quién ejecuta la reversión;
- qué configuración se restaura;
- si requiere reinicio;
- tiempo estimado operacional;
- validación posterior;
- canal de escalamiento;
- responsable de aprobar la reversión.
```

No autorizar una futura implementación sin reversión disponible.

## 5. Evidencias aceptables

Se consideran válidas:

```text
- capturas del panel sin secretos;
- fragmentos redactados de configuración;
- respuesta escrita del administrador institucional;
- documentación del proveedor;
- salida de comandos autorizados;
- URL productiva confirmada;
- evidencia pública del certificado;
- procedimiento institucional vigente.
```

No se consideran suficientes por sí solas:

```text
- nombres de repositorio;
- remoto Git;
- APP_ENV=prod;
- URL de localhost;
- inferencias desde el código;
- headers no verificados;
- comentarios históricos.
```

## 6. Decisiones que no se solicitan todavía

Esta solicitud no autoriza:

```text
- activar session.cookie_secure;
- modificar PHP;
- modificar Apache/Nginx;
- cambiar proxy;
- cambiar DNS;
- renovar certificados;
- modificar código;
- crear variables de entorno;
- desplegar;
- reiniciar servicios;
- purgar sesiones;
- crear una Task técnica.
```

Su alcance es únicamente obtener evidencia y definir responsables.

## 7. Criterios para considerar suficiente la respuesta

La respuesta institucional será suficiente sólo si permite completar:

```text
[ ] Dominio productivo identificado.

[ ] HTTPS válido confirmado.

[ ] HTTP redirigido o bloqueado.

[ ] Terminación TLS identificada.

[ ] Proxy identificado o descartado.

[ ] SAPI PHP identificado.

[ ] Configuración efectiva identificada.

[ ] Scope de Secure conocido.

[ ] Responsable técnico identificado.

[ ] Autoridad de aprobación identificada.

[ ] Mecanismo de reversión definido.

[ ] Validación productiva autorizable.
```

Si falta un punto crítico, debe registrarse expresamente.

## 8. Respuesta institucional solicitada

Formato recomendado:

```markdown
# Respuesta institucional — Despliegue HTTPS de la intranet

## 1. Institución y unidad responsable

## 2. Dominio y URL productiva

## 3. Hosting o servidor

## 4. HTTPS y certificado

## 5. Redirección HTTP→HTTPS

## 6. Terminación TLS

## 7. Proxy o balanceador

## 8. PHP y SAPI

## 9. Configuración actual de sesión

## 10. Scope de configuración

## 11. Responsable técnico

## 12. Autoridad aprobadora

## 13. Capacidad de modificación

## 14. Reversión

## 15. Validación autorizada

## 16. Evidencias adjuntas

## 17. Observaciones
```

## 9. Condición actual

```text
session.cookie_secure:
BLOQUEADO.

Task de despliegue:
NO AUTORIZADA.

Motivo:
falta evidencia y gobierno institucional del entorno productivo.
```

## 10. Siguiente paso después de recibir respuesta

Una vez recibida la evidencia institucional, Dirección Técnica deberá:

```text
1. validar suficiencia y coherencia;

2. clasificar hechos, hipótesis y contradicciones;

3. determinar la ubicación exacta de Secure;

4. definir scope y reversión;

5. decidir si corresponde crear una Task de despliegue;

6. mantener el bloqueo si la evidencia sigue siendo insuficiente.
```
