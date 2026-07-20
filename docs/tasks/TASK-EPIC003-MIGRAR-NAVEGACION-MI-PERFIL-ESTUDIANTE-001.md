# TASK-EPIC003-MIGRAR-NAVEGACION-MI-PERFIL-ESTUDIANTE-001

## Migración de la navegación estudiantil de “Mi Perfil”

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Tasks antecedentes:**
  - TASK-EPIC003-CAPACIDAD-PERFIL-VER-001.
  - TASK-EPIC003-MIGRAR-GUARDIA-PERFIL-ESTUDIANTE-001.
  - TASK-EPIC003-MIGRAR-REDIRECCION-PERFIL-ESTUDIANTE-001.
- **Clasificación:** [ARQ] [TEC] [SEC] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Migrar exclusivamente la selección del destino estudiantil del enlace
“Mi Perfil” desde `$_SESSION['estudiante']` hacia `perfil.ver`.

### 3. Archivo implementado

```text
form-doc/header.php
```

### 4. Selector histórico

```php
isset($_SESSION['estudiante'])
```

### 5. Selector implementado

```php
Authorization::hasCapability('perfil.ver')
```

No se conservó fallback directo por `estudiante` dentro de `$miperfil`.

### 6. Import y autoload

Se añadió:

```php
use App\Security\Authorization;
```

No se añadió otro bootstrap. Se preservó la cadena existente:

```text
header.php
→ src/Config/global.php
→ src/bootstrap/app.php
→ vendor/autoload.php
```

### 7. Construcción resultante

```php
$miperfil = isset($_SESSION['docente'])
    ? RUTA . 'form-doc/info.docente.php'
    : (
        Authorization::hasCapability('perfil.ver')
            ? RUTA . 'form-doc/info.estudiante.php'
            : RUTA . 'admin/perfil.php'
    );
```

### 8. Prioridad preservada

```text
docente
> perfil.ver
> fallback administrativo
```

Esta prioridad difiere de `index.php` y fue preservada deliberadamente.

### 9. URL preservadas

```text
form-doc/info.docente.php
form-doc/info.estudiante.php
admin/perfil.php
```

### 10. Productor transitorio

```text
permiso 5
→ $_SESSION['estudiante']
→ perfil.ver
```

No se modificó el productor.

### 11. Estado académico

```text
estado 6 + permiso 5
→ perfil.ver
→ Mi Perfil estudiantil

estado 6 + permiso 3 sin permiso 5
→ sin perfil.ver
→ sin acceso a Mi Perfil estudiantil
```

`tipo_est` no participa como fuente de autorización.

### 12. Identidad derivada

`IdentityResolution` continúa siendo observacional, local y no autorizativa; no
participa en el cálculo de `$miperfil`.

### 13. Menú Programa

La condición general del menú no fue modificada y continúa usando señales
históricas. `perfil.ver` no fue añadida al wrapper y `estudiante` no fue
retirada.

Una sesión residual con `estudiante` puede seguir viendo el menú, aunque
`$miperfil` ya no apunte al perfil estudiantil sin `perfil.ver`.

### 14. Cambio local de Cursos

Antes de esta Task, `header.php` contenía:

```text
- un cambio local en la visibilidad de Cursos;
- un salto final de línea local.
```

El commit funcional fue construido mediante staging selectivo por hunks y
excluyó ambos cambios.

### 15. Revisión técnica

Estado:

```text
Aprobada con observaciones no bloqueantes y aislable.
```

Hallazgos:

```text
BLOQUEANTES:
Ninguno.

MAYORES:
Ninguno.

MENORES:
- advertencia Git LF → CRLF no bloqueante.

OBSERVACIONES:
- prioridades distintas entre header.php e index.php;
- Programa continúa usando estudiante;
- aceptado puede recibir fallback administrativo restringido;
- info.estudiante.php aún usa estudiante como identificador;
- faltan capacidades para perfil docente y calendario;
- cambio local de Cursos y EOF permanecen fuera del commit.
```

### 16. Validación funcional

Responsable:

```text
Usuario.
```

Estado:

```text
Aprobada con observaciones no bloqueantes.
```

Confirmaciones:

```text
- docente conserva el perfil docente;
- permisos 3 y 4 sin 5 conservan el enlace docente;
- docente con permiso 5 mantiene prioridad docente;
- permiso 5 abre el perfil estudiantil;
- estado 6 con permiso 5 conserva Mi Perfil;
- estado 6 con permiso 3 sin 5 no accede a Mi Perfil estudiantil;
- admin y comité conservan el destino administrativo;
- Cursos conserva el comportamiento local;
- Calendario Académico no cambia;
- no aparecieron errores de PHP, autoload o navegación.
```

El acceso a Reglamento mediante permiso 3 pertenece a una compatibilidad
histórica distinta y no forma parte de esta Task. Codex no realizó la
validación funcional.

### 17. Sesiones residuales

```text
estudiante presente
perfil.ver ausente
docente ausente
→ $miperfil = admin/perfil.php
```

El selector defectuoso hacia el perfil estudiantil queda eliminado. La
visibilidad completa de Programa y la limpieza general de sesión siguen
pendientes. `ajax/login.php` aún puede conservar señales históricas al
reautenticar.

### 18. Commit

```text
4d3d4c805ef7b00c42ecb0d629c29940266b70f3
refactor(auth): migrate student profile navigation
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 19. Pendientes

* migrar la visibilidad general del menú Programa;
* decidir si se alinean prioridades entre header e index;
* crear capacidades para perfil docente y calendario;
* corregir el fallback histórico de aceptado;
* limpiar señales históricas al reautenticar;
* separar identificador de estudiante y capacidad;
* resolver los cambios locales ajenos de Cursos;
* retirar posteriormente la dependencia del permiso 5.

### 20. Estado final

```text
Task:
Cerrada

Implementación:
Completada

Revisión técnica:
Aprobada con observaciones no bloqueantes y aislable

Validación funcional:
Aprobada por el usuario con observaciones no bloqueantes

Commit:
4d3d4c805ef7b00c42ecb0d629c29940266b70f3

Push:
Completado
```
