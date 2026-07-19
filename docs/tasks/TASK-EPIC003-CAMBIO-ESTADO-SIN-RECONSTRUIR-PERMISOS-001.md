# TASK-EPIC003-CAMBIO-ESTADO-SIN-RECONSTRUIR-PERMISOS-001

## Cambio de estado académico sin reconstrucción de permisos

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **AT fuente:** AT-EPIC003-PLAN-SUSTITUCION-CONSUMIDORES-PERMISO-003-001.
- **Clasificación:** [TEC] [ARQ] [GOV] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Modificar la operación:

```text
update-permiso-tipo-est
```

para que el cambio de estado académico actualice únicamente:

```text
estudiante.tipo_est
```

y preserve todos los permisos y roles independientes.

### 3. Archivo implementado

```text
ajax/estudiante.php
```

### 4. Contrato

Parámetro vigente:

```text
id_usu
```

Significado:

```text
usuario.id_usuario
```

Estado académico:

```text
tipo_est
```

Valores válidos:

```text
1, 2, 3, 4, 5, 6, 7
```

### 5. Resultado implementado

La operación:

* valida que `id_usu` sea escalar, numérico entero y mayor que cero;
* valida que `tipo_est` sea escalar, numérico entero y pertenezca a `1–7`;
* rechaza parámetros inválidos antes de cualquier escritura;
* ejecuta como única escritura `Estudiante::editarTipoEst($id_usu, $tipo_est)`;
* conserva el contrato histórico de respuesta;
* no modifica sesiones.

Se retiró de esta operación:

* eliminación de permisos;
* reconstrucción de `permiso_login`;
* asignación automática del permiso `3`;
* asignación automática del permiso `5`.

### 6. Decisión aplicada

```text
estado académico
≠ rol
≠ permiso
≠ capacidad
```

Modificar `estudiante.tipo_est` no debe alterar permisos independientes.

Las capacidades derivadas del estado se recalculan al iniciar una sesión nueva.

### 7. Validación técnica

```text
php -l ajax/estudiante.php:
Correcto.

git diff --check:
Correcto.

git show --check:
Correcto.
```

### 8. Validación funcional

Responsable:

```text
Usuario.
```

Resultados aprobados:

* el estudiante puede cambiar entre estados académicos;
* `estudiante.tipo_est` se actualiza;
* los permisos antes y después permanecen iguales;
* el permiso `3` no se agrega ni elimina;
* el permiso `5` se preserva;
* no existe reconstrucción de permisos;
* las capacidades correspondientes se obtienen en una sesión nueva.

Codex no realizó la validación funcional.

### 9. Permiso 5

El permiso `5` se preserva porque la evidencia lo clasifica provisionalmente como un rol heredado de estudiante independiente del estado académico.

Esta Task no resuelve su semántica definitiva.

El permiso `5`:

* no se asigna durante el cambio de estado;
* no se elimina durante el cambio de estado;
* no debe utilizarse como sustituto del permiso `3`.

### 10. Commit

```text
bc14bcd80170ef1c685c15a717c9323b36bc6b7d
fix(student): preserve permissions when changing academic state
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 11. Pendientes fuera de alcance

* resolución definitiva del permiso `5`;
* congelación del permiso `3`;
* retiro de `$_SESSION['aceptado']`;
* capacidades de Cursos y Calendario;
* redirección y navegación por capacidades;
* actualización inmediata de sesiones;
* atomicidad global del alta;
* reinicialización de datos de prueba.

### 12. Criterios cerrados

```text
[✓] id_usu validado.
[✓] tipo_est validado.
[✓] Única escritura sobre estudiante.tipo_est.
[✓] Permisos preservados.
[✓] Permiso 3 neutral.
[✓] Permiso 5 preservado.
[✓] Validación técnica aprobada.
[✓] Validación funcional aprobada por el usuario.
[✓] Commit publicado.
```

### 13. Estado final

```text
Task:
Cerrada.

Implementación:
Completada.

Validación técnica:
Aprobada.

Validación funcional:
Aprobada por el usuario.

Commit:
bc14bcd80170ef1c685c15a717c9323b36bc6b7d

Push:
Completado.
```
