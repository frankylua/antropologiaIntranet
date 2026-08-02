# TASK-EPIC003-CENTRALIZAR-CONFIGURACION-SESION-APP-001

## Centralización de la política de sesión en la aplicación

### 1. Identificación

- **EPIC:** EPIC-003.
- **AT fuente:** [AT-EPIC003-CONFIGURACION-GLOBAL-SESION-001](../architecture/AT-EPIC003-CONFIGURACION-GLOBAL-SESION-001.md).
- **Clasificación:** [ARQ] [SEC] [APP] [DOC] [GOV].
- **Estado:** Cerrada.

### 2. Objetivo

Centralizar la configuración de sesiones PHP que corresponde a la aplicación
mediante un bootstrap dedicado, versionado y ejecutado antes de los
`session_start()` publicados.

### 3. Alcance implementado

Se creó:

```text
src/bootstrap/session.php
```

Se integró defensivamente desde:

```text
src/bootstrap/app.php
30 callers directos
```

El commit incluyó únicamente el hunk de bootstrap autorizado en
`form-doc/ver.curso.php`. La Task no modificó roles, capacidades, reglas de
autorización, base de datos ni configuración de despliegue.

### 4. Bootstrap de sesión

El bootstrap:

- no genera salida;
- no inicia sesión;
- no depende de base de datos, Composer, `Configuration` ni `app.php`;
- es idempotente y detecta carga tardía;
- aplica y verifica la política;
- preserva las directivas protegidas;
- publica su marcador sólo después del éxito;
- trata explícitamente `PHP_SESSION_DISABLED`.

### 5. Directivas aplicadas

```text
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.use_trans_sid = 0
session.cookie_httponly = 1
session.cookie_samesite = Lax
```

### 6. Directivas preservadas

```text
session.cookie_secure
session.cookie_path
session.cookie_domain
session.cookie_lifetime
session.name
```

La Task no implementó `session.cookie_secure`.

### 7. Cobertura

```text
HEAD publicado: 33/33 callers cubiertos
Working tree durante implementación: 34/34 callers cubiertos
```

La diferencia correspondió a un `session_start()` local no publicado en
`ajax/curso.php`. La implementación no dependió de publicar Cursos.

Los callers publicados cargan el bootstrap directa o transitivamente antes de
iniciar sesión.

### 8. Login y logout preservados

Login preserva:

- reemplazo atómico de la sesión;
- `session_regenerate_id(true)`;
- códigos HTTP;
- roles y capacidades.

Logout preserva:

- vaciado de `$_SESSION`;
- expiración de cookie;
- `session_destroy()`;
- redirección y `exit`.

### 9. Corrección PHP_SESSION_DISABLED

La revisión detectó que `PHP_SESSION_DISABLED` no era tratado explícitamente.
Se añadió una rama anterior a ACTIVE, `ini_get()`, `ini_set()` y al marcador.

El estado deshabilitado produce una `RuntimeException` controlada, sin salida,
sin modificar directivas y sin publicar el marcador.

### 10. Validaciones

```text
Implementación técnica: Aprobada
Corrección PHP_SESSION_DISABLED: Aprobada
Revisión técnica: Aprobada con observaciones no bloqueantes
Validación funcional: Aprobada por el usuario
```

La validación funcional confirmó login, navegación, AJAX y logout, mantuvo HTTP
local sin `Secure` y preservó roles, capacidades y autorización.

### 11. Commit y publicación

```text
Commit: 7bd947661d443228ab0dccdb133cd117c9806104
Mensaje: refactor(session): centralize application session policy
Push: Aprobado y publicado
```

### 12. Aislamiento

Quedaron fuera del commit:

```text
ajax/curso.php
cambios funcionales de form-doc/ver.curso.php
form-doc/scripts/curso.js
src/Model/Tesis.php
SQL
documentos no relacionados
```

### 13. Reversión

La unidad técnica de reversión es el commit
`7bd947661d443228ab0dccdb133cd117c9806104`. Una reversión de esta Task debe
limitarse al bootstrap y sus integraciones publicadas, sin incorporar ni
revertir Cursos, Tesis, JavaScript, SQL o documentos no relacionados.

### 14. Pendientes y riesgos residuales

El único pendiente futuro es una inspección de despliegue HTTPS productivo para
resolver `session.cookie_secure`. No se crea todavía una Task de despliegue.

Persisten como riesgos conocidos:

- SAPI web y manejador de sesiones productivos no confirmados;
- cookies antiguas con atributos anteriores;
- `path=/` y nombre `PHPSESSID` preservados;
- posible carrera alrededor de `session_regenerate_id(true)`;
- directivas protegidas no reverificadas en cargas posteriores;
- entry points heredados con includes faltantes;
- `Secure` pendiente de infraestructura.

### 15. Estado final

```text
Task: Cerrada
Implementación: Aprobada
Corrección PHP_SESSION_DISABLED: Aprobada
Revisión técnica: Aprobada con observaciones no bloqueantes
Validación funcional: Aprobada por el usuario
Commit: 7bd947661d443228ab0dccdb133cd117c9806104
Push: Completado
Pendiente DEPLOY: session.cookie_secure y HTTPS productivo
```
