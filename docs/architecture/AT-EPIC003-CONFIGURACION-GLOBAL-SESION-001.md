# AT-EPIC003-CONFIGURACION-GLOBAL-SESION-001

## Configuración global de sesiones PHP

## 1. Identificación y estado

- **EPIC asociado:** EPIC-003.
- **Task derivada cerrada:** `TASK-EPIC003-CENTRALIZAR-CONFIGURACION-SESION-APP-001`.
- **Clasificación:** [ARQ] [SEC] [APP] [DEPLOY] [DOC] [GOV].
- **Estado:** Aprobado y parcialmente implementado.
- **Dictamen:** Configuración de aplicación resuelta; despliegue pendiente.
- **Alternativa adoptada:** Arquitectura híbrida.

La decisión arquitectónica está aprobada y su componente de aplicación fue
implementado, revisado, validado y publicado. El componente de despliegue no
está implementado porque requiere evidencia externa de infraestructura.

## 2. Arquitectura adoptada

La seguridad de sesiones PHP se divide entre dos responsabilidades:

```text
Aplicación
├── bootstrap dedicado
├── política versionada
├── ejecución anterior a session_start()
├── independencia de base de datos
└── compatibilidad con CLI y HTTP local

Despliegue
├── session.cookie_secure
├── HTTPS obligatorio
├── proxy confiable
└── configuración productiva efectiva
```

Esta separación permite completar el alcance APP sin presentar como resuelta
la configuración que depende del entorno productivo.

## 3. Implementación de aplicación

El bootstrap dedicado se encuentra en:

```text
src/bootstrap/session.php
```

Sus propiedades confirmadas son:

- no genera salida;
- no inicia sesión;
- no depende de base de datos, Composer, `Configuration` ni `app.php`;
- es idempotente;
- detecta carga tardía;
- trata explícitamente `PHP_SESSION_DISABLED`;
- aplica y verifica la política;
- preserva las directivas protegidas;
- publica un marcador sólo después del éxito.

`src/bootstrap/app.php` carga defensivamente el bootstrap de sesión antes de
`Configuration`, Composer, conexión y otros efectos.

## 4. Directivas de aplicación

La política versionada aplica y verifica:

```text
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.use_trans_sid = 0
session.cookie_httponly = 1
session.cookie_samesite = Lax
```

Las siguientes directivas se preservan sin modificación:

```text
session.cookie_secure
session.cookie_path
session.cookie_domain
session.cookie_lifetime
session.name
```

## 5. Cobertura e integración

```text
Callers en HEAD publicado: 33
Callers cubiertos: 33/33
```

Durante la implementación, el working tree alcanzó cobertura `34/34` porque
`ajax/curso.php` contenía un `session_start()` local no publicado. La Task no
dependió de publicar Cursos.

Los callers publicados cargan `session.php` directa o transitivamente antes de
iniciar sesión.

## 6. Corrección de PHP_SESSION_DISABLED

El hallazgo técnico fue que `PHP_SESSION_DISABLED` no tenía tratamiento
explícito. Se añadió una rama anterior a `PHP_SESSION_ACTIVE`, al primer
`ini_get()`, al primer `ini_set()` y a la publicación del marcador.

El resultado es una `RuntimeException` controlada, sin salida, sin modificación
de directivas y sin publicación del marcador.

## 7. Validación y publicación

```text
Implementación: Aprobada
Revisión técnica: Aprobada con observaciones no bloqueantes
Validación funcional: Aprobada por el usuario
Commit: 7bd947661d443228ab0dccdb133cd117c9806104
Mensaje: refactor(session): centralize application session policy
Push: Aprobado y publicado
```

El commit contiene `src/bootstrap/session.php`, `src/bootstrap/app.php`, 30
callers directos y únicamente el hunk autorizado de `form-doc/ver.curso.php`.

Quedaron fuera `ajax/curso.php`, los cambios funcionales de
`form-doc/ver.curso.php`, `form-doc/scripts/curso.js`, `src/Model/Tesis.php`,
SQL y documentos no relacionados.

## 8. Pendiente de despliegue

`session.cookie_secure` no fue implementado. El único pendiente futuro es:

```text
Inspección de despliegue HTTPS productivo para resolver
session.cookie_secure.
```

La inspección requiere confirmar HTTPS obligatorio, redirección HTTP→HTTPS,
terminación TLS, proxy reverso, proxies confiables, SAPI web efectivo,
configuración productiva y responsabilidad institucional de infraestructura.

No se ha creado una Task de despliegue porque falta evidencia productiva. Esta
decisión no autoriza cambios PHP, Apache, detección HTTPS, variables de entorno
ni un runbook productivo definitivo.

## 9. Gobierno y separación de responsabilidades

- **[ARQ]:** bootstrap dedicado y arquitectura híbrida.
- **[SEC]:** seis directivas gestionadas por la aplicación.
- **[APP]:** política versionada implementada y validada.
- **[DEPLOY]:** `Secure` y HTTPS productivo pendientes.
- **[DOC]:** Task de aplicación cerrada.
- **[GOV]:** infraestructura y aplicación conservan responsabilidades separadas.

La AT permanece parcialmente implementada por su componente de despliegue. La
Task derivada queda cerrada porque su alcance APP fue completado.

## 10. Riesgos residuales

- SAPI web productivo no confirmado.
- Manejador de sesiones productivo desconocido.
- Cookies antiguas pueden conservar atributos anteriores.
- `path=/` y el nombre `PHPSESSID` se mantienen.
- Posible carrera alrededor de `session_regenerate_id(true)`.
- Las directivas protegidas no se reverifican en cargas posteriores.
- Entry points heredados continúan con includes faltantes.
- `Secure` continúa pendiente.

## 11. Estado final

```text
Decisión: Aprobada
Arquitectura: Híbrida
Aplicación: Implementada, validada y publicada
Task APP: Cerrada
Despliegue: Pendiente de evidencia externa
Task de despliegue: No creada
Riesgo Secure: Abierto
```
