# TASK-EPIC003-CORREGIR-PUBLICACION-VISIBILIDAD-PROGRAMA-001

## Corrección aditiva de la publicación del menú Programa

### 1. Identificación

- **EPIC:** EPIC-003.
- **Task corregida:** TASK-EPIC003-MIGRAR-VISIBILIDAD-PROGRAMA-ESTUDIANTE-001.
- **Estado:** Cerrada.
- **Clasificación:** [TEC] [GOV] [MET] [DOC].

### 2. Motivo

El commit publicado:

```text
eda073e2f61793a197bd8f0812883c2862144c4f
```

incluyó, además de la migración autorizada, dos cambios locales ajenos:

```text
- condición de Cursos;
- EOF de form-doc/header.php.
```

### 3. Decisión

Se aplicó una corrección aditiva. No se reescribió la historia y no se utilizó
force push.

### 4. Fuente de restauración

```text
eda073e2^:form-doc/header.php
```

### 5. Restauraciones

```text
- Cursos fue restaurado literalmente desde el padre.
- EOF fue restaurado literalmente desde el padre.
```

### 6. Cambio preservado

```text
Programa:
estudiante → perfil.ver
```

### 7. Commit correctivo

```text
3874da2b1e2d20c012f5dd5375c1e15678463afa
fix(auth): remove unrelated changes from program menu migration
```

El commit contiene únicamente `form-doc/header.php`.

### 8. Estado acumulado

El diff acumulado contiene exclusivamente la sustitución autorizada del
wrapper Programa:

```diff
-|| isset($_SESSION['estudiante'])
+|| Authorization::hasCapability('perfil.ver')
```

### 9. Revisión técnica

```text
Aprobada con observaciones no bloqueantes y aislable.
```

### 10. Validación funcional

```text
Responsable: Usuario
Estado: Aprobada
```

Codex no ejecutó la validación funcional.

### 11. Estado final

```text
Corrección: Completada
Commit: 3874da2b1e2d20c012f5dd5375c1e15678463afa
Push: Completado
Historia reescrita: No
Force push: No
```
