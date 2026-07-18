# TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001

## Centralización incremental de la validación de consulta de Cursos

## 1. Identificación

- **Clasificación:** [TEC] implementación incremental de autorización; [ARQ] evolución de arquitectura de autorización; [GOV] derivada de FEATURE EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- **Estado:** 🟡 Pendiente revisión técnica.

## 2. Antecedentes y dependencias

- ✅ FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.
- ✅ AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.
- ✅ AT-EPIC003-AUTORIZACION-CURSOS-001.
- ✅ ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.

Pendiente: revisión técnica de esta TASK.

## 3. Objetivo

Registrar la implementación incremental para migrar la **validación actual de acceso a consulta/visualización de Cursos** hacia `Authorization`, conservando la fuente actual de permisos y el mismo comportamiento observable.

```text
Módulo Cursos
  ↓
Authorization
  ↓
Fuente actual de permisos
  ↓
Mismo comportamiento observable
```

Esta TASK no crea un nuevo permiso funcional de visualización de Cursos, no redefine reglas institucionales y no modifica el modelo de autorización.

## 4. Principios y restricción principal

```text
Estado académico
  ≠
Rol institucional
  ≠
Permiso funcional
  ≠
Acción permitida
```

La implementación debe reutilizar las condiciones actuales de autorización utilizadas por el módulo Cursos. No se permite:

- Crear nuevas claves de permiso.
- Asignar nuevos accesos.
- Modificar `permiso_login`.
- Redefinir roles.
- Cambiar reglas académicas.
- Modificar reglas institucionales aprobadas.

## 5. Separación de capacidades

```text
Visualización
  ≠
Escritura
  ≠
Actualización
```

Tener acceso de visualización de Cursos no implica crear, modificar, actualizar ni administrar Cursos. La validación centralizada de consulta no habilita permisos de escritura o actualización.

## 6. Alcance incluido

- Análisis del control actual de acceso a consulta/visualización de Cursos.
- Centralización de esa validación mediante `Authorization`.
- Reutilización de permisos y condiciones existentes.
- Conservación del comportamiento actual de consulta.
- Validación funcional de acceso y bloqueo.

## 7. Alcance excluido

- Creación de permisos nuevos.
- Modificación de `permiso_login`.
- Cambios en login o sesiones.
- Cambios de estados académicos.
- CRUD de Cursos.
- Escritura, creación, edición, actualización o eliminación de Cursos.
- Administración de Cursos.
- Migración completa del módulo.
- Cambios al modelo de permisos.

## 8. Archivos candidatos

Los archivos se identificarán y confirmarán durante la revisión técnica. Podrían incluir archivos del módulo Cursos, sus scripts asociados y `src/Security/Authorization.php`; esta referencia no asume nombres definitivos ni autoriza modificar archivos distintos de los que una futura revisión técnica apruebe.

## 9. Criterios de aceptación

### Funcionales

- Un usuario con acceso actual de consulta a Cursos conserva el acceso.
- Un usuario sin acceso actual continúa bloqueado.
- La visualización de Cursos funciona igual que antes.
- No aparecen capacidades adicionales.

### Seguridad

- La visualización no habilita escritura.
- La visualización no habilita actualización.
- La administración permanece separada de la consulta.

### Técnicos

- La validación de consulta pasa por `Authorization`.
- No cambia `permiso_login`.
- No cambia la base de datos, el login ni las sesiones.
- No se afectan otros módulos.

## 10. Validación funcional requerida

| Caso | Resultado esperado |
| --- | --- |
| Usuario con permiso actual de consulta Cursos | Puede visualizar Cursos. |
| Usuario sin permiso actual | No puede visualizar Cursos. |
| Usuario con visualización, sin permisos administrativos | Consulta permitida; escritura y actualización no permitidas. |

## 11. Reversión

Debe ser posible revertir únicamente el cambio de validación de consulta de Cursos. La reversión no debe afectar usuarios, permisos existentes, login ni otros módulos.

## 12. Condiciones de detención

Detener la implementación y solicitar revisión técnica si:

- Se requiere crear un permiso nuevo.
- Consulta y escritura no pueden separarse.
- Se requiere cambiar el modelo de permisos.
- Aparecen reglas académicas no definidas.
- Se requiere modificar más módulos.

## 13. Restricciones documentales

Esta TASK no autoriza modificaciones fuera de su futura implementación aprobada. No modifica ADR-002, ROADMAP, MANUAL_MAESTRO, la Feature existente ni actas existentes. No incluye SQL ni commit.

## 14. Fuentes utilizadas

- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md](../architecture/AT-EPIC003-AUTORIZACION-CENTRALIZACION-001.md)
- [AT-EPIC003-AUTORIZACION-CURSOS-001.md](../architecture/AT-EPIC003-AUTORIZACION-CURSOS-001.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](../governance/ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)

## 15. Siguiente paso

```text
Revisión técnica TASK-EPIC003-AUTORIZACION-CURSOS-CONSULTA-PILOTO-001
```

Solo tras dicha revisión corresponde la implementación supervisada.
