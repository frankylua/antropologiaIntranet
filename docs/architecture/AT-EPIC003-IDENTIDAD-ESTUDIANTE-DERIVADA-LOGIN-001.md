# AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001

## Identidad de estudiante y profesor derivada durante login

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
**Tipo:** [ARQ] [TEC] [GOV] [MET].
**Estado:** En ejecución.

## Objetivo

Separar la resolución de identidad personal y de especializaciones de los
permisos históricos. La transición debe conservar el comportamiento vigente
hasta que Tasks posteriores integren el resolver y migren sus consumidores.

## Fuente de identidad

```text
login.id_login
→ usuario.login

usuario.id_usuario
→ estudiante.usuario

usuario.id_usuario
→ profesor.usuario
```

La relación `usuario → estudiante` determina la especialización de estudiante,
`estudiante.tipo_est` determina su estado académico y la relación
`usuario → profesor` determina la especialización de profesor. Los permisos no
son fuente de identidad.

## Resultado implementado

### TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001

Estado:

```text
Completada.
```

Commit:

```text
814ecb2d703d8df1638a53e660a068c5f29e3b06
feat(identity): add read-only login identity resolver
```

Resultado:

* resolver independiente creado;
* resolución de solo lectura;
* cardinalidades explícitas `NONE`, `SINGLE` y `MULTIPLE`;
* identidad estudiante derivada desde la relación;
* identidad profesor derivada desde la relación;
* esa Task no incorporó todavía la integración en `ajax/login.php`, completada posteriormente;
* sin modificación de sesión;
* sin consulta o sincronización de permisos;
* comportamiento observable preservado.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Evidencia técnica

La revisión técnica y estática aprobó el resolver con observaciones no
bloqueantes. La sintaxis de los tres archivos, el autoload PSR-4 y la
compatibilidad con PHP 8.2.12 fueron confirmados. No se detectaron efectos
colaterales, escrituras, consultas de permisos ni ocultación de multiplicidad.

En esa Task no hubo validación funcional productiva porque el componente aún
no tenía un consumidor dentro del flujo de login. La integración y su
validación funcional se completaron posteriormente.

## Observaciones no bloqueantes

* `IdentityResolution` permite la construcción manual de estados
  semánticamente contradictorios, aunque `IdentityResolver` no los produce;
* la versión mínima de PHP no está declarada en Composer; el uso de `readonly`
  requiere PHP 8.1 o superior.

Estas observaciones no constituyen decisiones arquitectónicas nuevas y quedan
pendientes para la integración futura.

## Compatibilidad histórica

* El permiso `5` permanece como compatibilidad transitoria y no ha sido
  retirado.
* `$_SESSION['estudiante']` se mantiene.
* El permiso `4` no es una fuente confiable de identidad docente.
* Un profesor sin permiso `4` no se corrige durante login.
* El permiso `3` continúa pendiente de sustitución.
* La sincronización de permisos durante login está prohibida.

La integración paralela del resolver no sustituye estos mecanismos en el flujo
productivo.

## Integración paralela completada

### TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001

Estado:

```text
Completada.
```

Commit:

```text
2c94d414c72cf3196d46ed766576881742c66e7c
feat(identity): integrate derived identity into login
```

Resultado:

* resolver integrado después de autenticar;
* resolución paralela con `IdentityResolution` local a la solicitud;
* comparación de estudiante con permiso histórico `5`;
* comparación de profesor con permiso histórico `4`;
* observabilidad mediante códigos técnicos seguros;
* capturas específicas de `PDOException` e `InvalidArgumentException`;
* discrepancias `WITHOUT_RELATION` limitadas a usuario `SINGLE`;
* sesiones, permisos y capacidades históricas intactas;
* respuesta, navegación y redirección intactas;
* validación funcional aprobada por el usuario; Codex no la ejecutó.

La relación profesor sin permiso `4` permanece como discrepancia observada y
no fue corregida ni sincronizada durante el login.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Introducción transitoria de `perfil.ver` completada

### TASK-EPIC003-CAPACIDAD-PERFIL-VER-001

Estado:

```text
Completada.
```

Commit:

```text
58d7745684aa1156e1feec0e1c80900cb1dce998
feat(auth): introduce perfil.ver capability
```

Resultado:

* `perfil.ver` introducida como capacidad transitoria;
* productor basado en el permiso histórico `5`;
* consumidor piloto `read_est_perfil` protegido antes de consultar el modelo;
* autorización backend mediante `Authorization`;
* compatibilidad de Administrador y Comité preservada;
* contrato exitoso intacto;
* el estado `6` conserva acceso temporal cuando mantiene permiso `5`;
* la identidad derivada todavía no concede la capacidad;
* validación funcional aprobada por el usuario; Codex no la ejecutó.

Se completaron únicamente la introducción transitoria de `perfil.ver` y la
protección del consumidor piloto `read_est_perfil`. La migración del productor
hacia estado y la exclusión institucional del estado `6` permanecen pendientes.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Migración de la guardia del perfil estudiantil completada

### TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001

Estado:

```text
Completada.
```

Commit:

```text
018a3679b191afbfbe6fe277b3ad60e5184a5964
refactor(auth): migrate student profile guard
```

Resultado:

* la guardia de `form-doc/info.estudiante.php` usa `perfil.ver`, `admin` o `comite`;
* el bootstrap se carga antes de `Authorization`;
* el fallback directo por `estudiante` fue retirado del consumidor;
* la redirección histórica a `../index.php` permanece intacta;
* el contenido, formularios y scripts permanecen intactos;
* el estado `6` conserva acceso temporal cuando mantiene permiso `5`;
* la identidad derivada no concede acceso estudiantil;
* la validación funcional fue aprobada por el usuario; Codex no la ejecutó.

Se completó únicamente la migración de la guardia backend de la página y su
alineación con `read_est_perfil`. Permanecen pendientes la navegación, la
redirección por capacidades, las capacidades de escritura, el perfil ajeno,
el retiro del permiso `5` y el tratamiento de sesiones históricas.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Migración de la selección del destino estudiantil completada

### TASK-EPIC003-MIGRAR-REDIRECCION-PERFIL-ESTUDIANTE-001

Estado:

```text
Completada.
```

Commit:

```text
2c2f4d906e6154e804154bd12a47eab7bd608c74
refactor(auth): migrate student profile redirect
```

Resultado:

* `index.php` utiliza `perfil.ver` para seleccionar el destino estudiantil;
* la condición histórica `estudiante` fue retirada de esa rama, sin fallback directo;
* la prioridad `admin/comite > aceptado > docente > perfil.ver > login` fue preservada;
* todos los destinos permanecen intactos;
* el bootstrap se carga antes de utilizar `Authorization`;
* `IdentityResolution` permanece no autorizativa;
* el estado `6` con permiso `5` mantiene acceso temporal;
* el estado `6` con permiso `3`, sin permiso `5`, prioriza el calendario y no obtiene Mi Perfil;
* se eliminó para esta ruta el ciclo específico cuando `estudiante` está presente y `perfil.ver` ausente;
* la limpieza general de señales residuales durante la reautenticación permanece pendiente;
* la validación funcional fue aprobada por el usuario con observaciones no bloqueantes; Codex no la ejecutó.

Consumidores de `perfil.ver` migrados:

```text
ajax/estudiante.php → read_est_perfil
form-doc/info.estudiante.php → guardia
index.php → selección del destino estudiantil
```

Se completaron únicamente la lectura piloto, la guardia de la página y la
selección del destino estudiantil. Permanecen pendientes la navegación de Mi
Perfil, la redirección integral de otros actores, las capacidades para docente,
administración, comité y calendario, la limpieza de sesión al reautenticar, el
productor basado en reglas institucionales, la resolución institucional del
estado `6`, las capacidades de edición, las escrituras, el perfil ajeno y el
retiro del permiso `5`.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Estado arquitectónico consolidado

```text
Fuente de identidad objetivo: login → usuario → especializaciones
Estrategia actual: derivación paralela
Fuente del comportamiento observable: permisos y sesiones históricas
Productor transitorio de perfil.ver: permiso 5
Usos migrados: read_est_perfil, guardia de info.estudiante.php, selección estudiantil en index.php
Mecanismo de autorización: Authorization::hasCapability()
Estado 6: acceso temporal preservado cuando mantiene permiso 5
Permiso 5: compatibilidad transitoria
Permiso 4: no confiable como fuente de identidad docente
Permiso 3: pendiente de sustitución
Sincronización durante login: prohibida
IdentityResolution: local a la solicitud y no autorizativa
Prioridad de index.php: admin/comite > aceptado > docente > perfil.ver > login
Navegación: todavía histórica
Otros destinos: todavía históricos
Limpieza de sesión: pendiente
```

## Secuencia de progreso

```text
[✓] TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001
[✓] TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001
[✓] TASK-EPIC003-CAPACIDAD-PERFIL-VER-001
[✓] TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001
[✓] TASK-EPIC003-MIGRAR-REDIRECCION-PERFIL-ESTUDIANTE-001
```

La resolución y su integración paralela están completadas. Permanecen
pendientes la sustitución funcional de permisos y sesiones históricas, la
migración del productor de `perfil.ver` hacia la regla institucional de
estados, la resolución institucional del estado `6`, la navegación de Mi
Perfil, la redirección integral de los demás actores, las capacidades para
docente, administración, comité y calendario, la limpieza de sesión al
reautenticar, la edición del perfil, las escrituras, los datos académicos, el
perfil ajeno y el retiro del permiso `5`.

## Próximo incremento

```text
Inspección técnica del enlace “Mi Perfil” y de la navegación en
form-doc/header.php.
```

`form-doc/header.php` contiene cambios locales ajenos que deben aislarse antes
de cualquier implementación. No se crea automáticamente una nueva Task. La
sustitución funcional del permiso `5`, la migración de navegación, la
redirección integral de otros actores, la congelación de productores y el
retiro de sesiones históricas permanecen pendientes para Tasks posteriores.
