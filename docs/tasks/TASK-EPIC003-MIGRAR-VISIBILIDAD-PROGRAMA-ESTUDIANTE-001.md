# TASK-EPIC003-MIGRAR-VISIBILIDAD-PROGRAMA-ESTUDIANTE-001

## Migración de la visibilidad estudiantil del menú Programa

### 1. Identificación

- **EPIC:** EPIC-003.
- **ADR:** ADR-002.
- **Estado:** Cerrada con corrección aditiva posterior.
- **Clasificación:** [ARQ] [TEC] [SEC] [MET].

### 2. Objetivo

Migrar la señal estudiantil del wrapper Programa desde
`$_SESSION['estudiante']` hacia `perfil.ver`.

### 3. Archivo implementado

```text
form-doc/header.php
```

### 4. Condición histórica

```text
admin OR comite OR aceptado OR estudiante OR docente
```

### 5. Condición acumulada final

```text
admin OR comite OR aceptado OR perfil.ver OR docente
```

### 6. Transformación autorizada

```php
isset($_SESSION['estudiante'])
```

fue sustituido por:

```php
Authorization::hasCapability('perfil.ver')
```

únicamente dentro del wrapper Programa.

### 7. Alcance

La Task migró una señal histórica de visibilidad.

No modificó:

- `$miperfil`;
- enlaces hijos;
- guardias backend;
- Calendario Académico;
- Reglamento;
- login;
- capacidades ejecutables.

`perfil.ver` no se convirtió en autorización global para todos los enlaces de
Programa.

### 8. Commit funcional original

```text
eda073e2f61793a197bd8f0812883c2862144c4f
refactor(auth): migrate program menu visibility
```

### 9. Incidencia de publicación

El commit original incluyó accidentalmente:

```text
- una modificación local de la condición de Cursos;
- la adición del newline final del archivo.
```

Estos cambios no formaban parte del alcance aprobado.

### 10. Corrección aditiva

Se creó el commit:

```text
3874da2b1e2d20c012f5dd5375c1e15678463afa
fix(auth): remove unrelated changes from program menu migration
```

Este commit restauró exclusivamente:

```text
- la condición de Cursos desde el padre de eda073e2;
- el estado EOF desde el padre de eda073e2.
```

No revirtió la migración del wrapper, no reescribió la historia y no utilizó
force push.

### 11. Estado acumulado

El diff acumulado entre el padre de `eda073e2` y el commit correctivo contiene
únicamente:

```diff
-|| isset($_SESSION['estudiante'])
+|| Authorization::hasCapability('perfil.ver')
```

### 12. Sesión residual

```text
estudiante presente
perfil.ver ausente
sin admin/comite/aceptado/docente
→ Programa oculto
```

La clave histórica no fue eliminada de sesión.

### 13. Estado 6

```text
estado 6 + permiso 5
→ perfil.ver
→ Programa visible
```

```text
estado 6 + permiso 3 sin permiso 5
→ Programa visible por aceptado
→ sin acceso a Mi Perfil estudiantil
```

### 14. Revisión técnica

```text
Aprobada con observaciones no bloqueantes y aislable.
```

### 15. Validación funcional

```text
Responsable: Usuario
Estado: Aprobada
```

Codex no ejecutó la validación funcional.

### 16. Pendientes

- Programa continúa siendo heterogéneo.
- `perfil.ver` no autoriza todos los enlaces hijos.
- Reglamento continúa sin condición frontend propia.
- Las capacidades de Cursos y Calendario siguen pendientes.
- La limpieza general de sesión sigue pendiente.
- `info.estudiante.php` mantiene la clave histórica como identificador.

### 17. Estado final

```text
Task funcional: Cerrada
Corrección aditiva: Publicada
Revisión técnica: Aprobada
Validación funcional: Aprobada por el usuario
Commit original: eda073e2f61793a197bd8f0812883c2862144c4f
Commit correctivo: 3874da2b1e2d20c012f5dd5375c1e15678463afa
Push: Completado
```
