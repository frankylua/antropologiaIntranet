# TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001

## Migración de la guardia backend del perfil estudiantil

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Task antecedente:** TASK-EPIC003-CAPACIDAD-PERFIL-VER-001.
- **Clasificación:** [ARQ] [TEC] [SEC] [GOV] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Migrar la guardia backend de `form-doc/info.estudiante.php` desde señales
históricas directas hacia el contrato centralizado de capacidades.

### 3. Archivo implementado

```text
form-doc/info.estudiante.php
```

### 4. Guardia histórica

```text
admin OR comite OR estudiante
```

### 5. Guardia implementada

```text
perfil.ver OR admin OR comite
```

Implementación:

```php
Authorization::hasCapability('perfil.ver')
Authorization::hasAny(['admin', 'comite'])
```

No se mantuvo fallback directo por `estudiante`.

### 6. Bootstrap y autoload

Antes de la guardia se carga:

```php
require_once __DIR__ . '/../src/bootstrap/app.php';
```

También se importa:

```php
use App\Security\Authorization;
```

El bootstrap habilita el autoload, no inicia sesión, no genera salida, no abre
conexión PDO y no modifica headers en el entorno validado. `require_once`
evita su ejecución duplicada cuando posteriormente se incluye `header.php`.

### 7. Orden de ejecución

```text
ob_start
→ sesión
→ bootstrap
→ guardia
→ redirección o contenido
→ primer HTML
```

La autorización ocurre antes de `header.php`, `ficha.estudiante.php`, scripts,
formularios y HTML.

### 8. Redirección

Se conserva:

```php
header('Location:../index.php');
```

No se modificó el destino ni el mecanismo histórico.

### 9. Equivalencia con sesiones actuales

El login vigente produce conjuntamente:

```text
permiso 5
→ estudiante
→ perfil.ver
```

Por ello, los estudiantes autorizados por permiso 5 mantienen acceso. Admin y
comité conservan acceso mediante el fallback histórico.

### 10. Estado académico

`tipo_est` no participa en la guardia.

```text
estado 6 + permiso 5
→ perfil.ver
→ acceso temporal preservado
```

Esta compatibilidad no constituye una política institucional definitiva.

### 11. Identidad derivada

`IdentityResolution` no autoriza la página. Una identidad estudiante sin permiso
5 no obtiene acceso estudiantil nuevo.

Caso funcional validado por el usuario:

```text
identidad estudiante
+ permisos 3 y 4
- permiso 5
→ conserva perfil y capacidades de profesor
→ no recibe perfil.ver
→ no obtiene acceso nuevo al perfil estudiantil
```

### 12. Contenido preservado

No se modificaron:

```text
header.php
ficha.estudiante.php
formularios
scripts
endpoints
navegación
index.php
```

El bloque autorizado permanece intacto.

### 13. Escrituras fuera del alcance

`perfil.ver` protege únicamente el acceso a la página y la lectura piloto ya
migrada. No autoriza:

```text
edición personal
edición del programa
eliminación
ficha académica
grados
publicaciones
congresos
proyectos
pasantías
becas
tesis
```

Estas operaciones requieren capacidades y Tasks posteriores.

### 14. Revisión técnica

Estado:

```text
Aprobada con observaciones no bloqueantes.
```

Observaciones:

```text
- posible ciclo para sesiones antiguas sin perfil.ver;
- ausencia histórica de exit después de header;
- admin/comité sin titular estudiantil propio;
- escrituras aún sin capacidades específicas;
- navegación e index.php todavía usan señales históricas.
```

Estos asuntos no se declaran corregidos.

### 15. Validación funcional

Responsable:

```text
Usuario.
```

Estado:

```text
Aprobada.
```

Se confirmó que estudiantes con permiso 5, estado 6 con permiso 5, admin y
comité conservan su comportamiento; que una identidad con permisos 3 y 4 sin
permiso 5 no obtiene acceso estudiantil; que los actores no autorizados son
redirigidos; y que no cambiaron formularios, datos, navegación ni redirección
normal.

Codex no realizó la validación funcional.

### 16. Sesiones antiguas y riesgo de redirección

Una sesión antigua o malformada con `$_SESSION['estudiante']` presente y
`perfil.ver` ausente puede producir:

```text
info.estudiante.php
→ index.php
→ info.estudiante.php
```

Se registra como observación. No afecta el flujo normal actual porque el login
vigente produce ambas señales y `read_est_perfil` ya exige `perfil.ver`.

### 17. Commit

```text
018a3679b191afbfbe6fe277b3ad60e5184a5964
refactor(auth): migrate student profile guard
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 18. Pendientes

* migrar navegación hacia capacidades;
* migrar redirección de `index.php`;
* tratar sesiones antiguas o malformadas;
* añadir capacidades de edición;
* proteger endpoints de escritura;
* separar perfil propio, datos académicos y administración;
* retirar posteriormente dependencias de sesión histórica;
* retirar el permiso 5 cuando no queden consumidores.

### 19. Estado final

```text
Task:
Cerrada

Implementación:
Completada

Revisión técnica:
Aprobada con observaciones no bloqueantes

Validación funcional:
Aprobada por el usuario

Commit:
018a3679b191afbfbe6fe277b3ad60e5184a5964

Push:
Completado
```
