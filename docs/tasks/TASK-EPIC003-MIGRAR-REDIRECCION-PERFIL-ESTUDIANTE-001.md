# TASK-EPIC003-MIGRAR-REDIRECCION-PERFIL-ESTUDIANTE-001

## Migración del destino estudiantil en la redirección central

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Tasks antecedentes:**
  - TASK-EPIC003-CAPACIDAD-PERFIL-VER-001.
  - TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001.
- **Clasificación:** [ARQ] [TEC] [SEC] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Migrar exclusivamente la selección del destino estudiantil en `index.php`
desde `$_SESSION['estudiante']` hacia la capacidad `perfil.ver`, conservando
prioridades y destinos históricos.

### 3. Archivo implementado

```text
index.php
```

### 4. Condición histórica

```php
isset($_SESSION['estudiante'])
```

### 5. Condición implementada

```php
Authorization::hasCapability('perfil.ver')
```

No se mantuvo fallback directo por `estudiante`.

### 6. Bootstrap y autoload

Antes de la cadena se carga:

```php
require_once __DIR__ . '/src/bootstrap/app.php';
```

También se importa:

```php
use App\Security\Authorization;
```

El bootstrap habilita el autoload, no genera salida, no inicia sesión, no
genera headers, no abre conexión PDO y no modifica destinos.

### 7. Prioridad preservada

```text
admin/comite
> aceptado
> docente
> perfil.ver
> login
```

En esta cadena, Administrador y Comité conservan la prioridad administrativa;
el permiso `3` mantiene prioridad sobre el permiso `4` y `perfil.ver`; el
permiso `4` mantiene prioridad sobre `perfil.ver`; y el permiso `5` continúa
seleccionando el perfil estudiantil mediante `perfil.ver`.

### 8. Destinos preservados

```text
admin OR comite → admin/inicio.php
aceptado → form-doc/calend.acad.php
docente → form-doc/info.docente.php
perfil.ver → form-doc/info.estudiante.php
ninguna señal → form-doc/login.php
```

`index.php` actúa como selección de destino. La autorización efectiva continúa
en `form-doc/info.estudiante.php` y `ajax/estudiante.php`; esta selección no
reemplaza sus guardias.

### 9. Productor transitorio

```text
permiso 5
→ $_SESSION['estudiante']
→ perfil.ver
```

No se modificó el productor, los permisos ni las capacidades ejecutables.
`perfil.ver` no se deriva desde `IdentityResolution`, `tipo_est`, permiso `3` ni
permiso `4`.

### 10. Estado académico

`tipo_est` no participa en la selección del destino.

```text
estado 6 + permiso 3, sin permiso 5
→ calendario académico
→ Mi Perfil restringido

estado 6 + permiso 5
→ perfil.ver
→ Mi Perfil permitido
```

La compatibilidad del estado `6` con permiso `5` continúa siendo transitoria y
no constituye una política institucional definitiva. El estado `6` no concede
ni revoca capacidades por sí mismo.

### 11. Identidad derivada

`IdentityResolution` continúa siendo no autorizativa y no participa en la
redirección. Una identidad estudiante sin permiso `5` no recibe `perfil.ver` ni
obtiene el destino estudiantil.

### 12. Riesgo de ciclo

Antes:

```text
estudiante presente
perfil.ver ausente
→ index.php ↔ info.estudiante.php
```

Después:

```text
perfil.ver ausente
→ index.php no selecciona info.estudiante.php
```

Resultado:

```text
Ciclo resuelto para esta ruta.
Limpieza general de señales residuales pendiente.
```

`ajax/login.php` todavía puede conservar señales históricas residuales al
reautenticar; no se declara resuelta la limpieza general de sesión.

### 13. Navegación

No se modificaron:

```text
form-doc/header.php
$miperfil
menús
enlaces
botones
visibilidad de Cursos
```

La navegación continúa basada parcialmente en señales históricas y pendiente
de migración. `form-doc/header.php` mantiene cambios locales ajenos que deben
aislarse antes de cualquier implementación.

### 14. Revisión técnica

Estado:

```text
Aprobada con observaciones no bloqueantes.
```

Hallazgos:

```text
BLOQUEANTES:
Ninguno.

MAYORES:
Ninguno.

MENORES:
- advertencia Git LF→CRLF sin error de whitespace.

OBSERVACIONES:
- ausencia histórica de exit después de header();
- ajax/login.php puede conservar señales históricas al reautenticar;
- faltan capacidades para docente, administración, comité y calendario;
- perfil.ver continúa dependiendo transitoriamente del permiso 5;
- info.estudiante.php todavía usa $_SESSION['estudiante'] como identificador.
```

Estos asuntos no se declaran resueltos.

### 15. Validación funcional

Responsable:

```text
Usuario.
```

Estado:

```text
Aprobada con observaciones no bloqueantes.
```

Se confirmó:

```text
- Admin y comité conservan acceso administrativo.

- La cuenta con permisos 3 y 4, sin permiso 5, continúa
  llegando inicialmente al calendario académico.

- Esa cuenta conserva acceso directo al perfil docente.

- El estudiante con permiso 5 carga Mi Perfil.

- El estudiante con estado académico 6 y permiso 3,
  sin permiso 5, llega al calendario académico y tiene
  restringido Mi Perfil.

- El estudiante con permiso 5 carga Mi Perfil,
  independientemente de que su estado académico sea 6.

- Sin sesión se mantiene el login.

- No cambiaron menús, destinos ni páginas.

- No aparecieron errores de sesión, autoload o redirección.
```

Codex no ejecutó la validación funcional.

### 16. Commit

```text
2c2f4d906e6154e804154bd12a47eab7bd608c74
refactor(auth): migrate student profile redirect
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 17. Pendientes

* inspeccionar y migrar la navegación de Mi Perfil;
* tratar y aislar cambios locales preexistentes en `form-doc/header.php`;
* limpiar señales históricas al reautenticar;
* crear o evaluar capacidades para perfil docente, administración, comité,
  calendario y postulante/aceptado;
* eliminar posteriormente la dependencia de permiso `5`;
* separar titular del perfil y capacidad;
* crear capacidades de edición y proteger escrituras;
* resolver el acceso a perfil ajeno;
* revisar la ausencia histórica de `exit` después de `header()`.

La migración integral de `index.php` y de los demás destinos no está
completada.

### 18. Estado final

```text
Task:
Cerrada

Implementación:
Completada

Revisión técnica:
Aprobada con observaciones no bloqueantes

Validación funcional:
Aprobada por el usuario con observaciones no bloqueantes

Commit:
2c2f4d906e6154e804154bd12a47eab7bd608c74

Push:
Completado
```
