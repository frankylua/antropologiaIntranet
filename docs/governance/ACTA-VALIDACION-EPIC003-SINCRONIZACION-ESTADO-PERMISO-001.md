# ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001

## 1. Identificación

- **Clasificación:** [GOV] gobierno y validación institucional; [ARQ] evolución de identidad y autorización; [DOC] registro oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR relacionado:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Análisis técnico base:** AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.
- **Propósito:** registrar la validación institucional pendiente sobre la relación operativa entre estado académico y permisos efectivos de acceso.

Esta acta documenta una brecha técnica confirmada y solicita definiciones institucionales. No crea reglas, permisos ni obligaciones de implementación; tampoco interpreta una regla funcional aprobada como autorización para sincronizar automáticamente el modelo heredado.

## 2. Hallazgo técnico confirmado

Actualmente existen fuentes separadas:

### Estado académico

```text
estudiante.tipo_est
  ↓
situación académica
```

### Permisos efectivos

```text
permiso_login
  ↓
$_SESSION
  ↓
autorización
```

No existe sincronización confirmada entre ambas fuentes. Estado académico y permiso funcional permanecen conceptualmente separados conforme a ADR-002; la brecha registrada corresponde a cómo, cuándo y bajo qué autoridad una regla institucional eventualmente se materializa en el modelo técnico heredado.

## 3. Caso que origina la validación

```text
Estudiante

Estado académico:
Aceptado

Resultado esperado:
Puede visualizar Cursos

Resultado observado:
No posee necesariamente $_SESSION['aceptado']
si no existe permiso_login asociado.
```

El caso no establece una regla nueva. Registra que la condición académica por sí sola no produce la clave de sesión requerida por controles heredados; por ello exige definición institucional antes de modificar permisos, sesiones o autorización.

## 4. Confirmaciones técnicas

1. El login genera `$_SESSION['aceptado']` únicamente cuando `permiso_login.id_permiso = 3`.
2. El flujo `update-permiso-tipo-est` actualiza el estado académico en `estudiante.tipo_est`.
3. Ese cambio de estado no sincroniza automáticamente `permiso_login`.
4. El permiso histórico 3 está asociado técnicamente a estudiante aceptado y docente; por ello no identifica de manera unívoca un estado académico.

## 5. Decisiones que no corresponde resolver técnicamente

No corresponde decidir técnicamente:

- si un estado académico debe generar permisos;
- cuándo debe ocurrir una sincronización;
- quién puede modificar, conservar o remover permisos;
- qué excepciones institucionales aplican;
- la relación definitiva entre estados, roles y participaciones.

Las alternativas técnicas documentadas en el AT son insumos de análisis, no resoluciones institucionales ni autorización de implementación.

## 6. Definiciones institucionales requeridas

### 6.1 Relación entre estado académico y permisos

Se solicita resolución explícita sobre cuál modelo operativo corresponde:

- **A.** El estado académico determina automáticamente permisos.
- **B.** Los permisos se asignan independientemente del estado.
- **C.** Existe un modelo mixto, con condiciones, responsables y excepciones explícitas.

La resolución debe distinguir entre regla institucional de acceso, permiso funcional efectivo y mecanismo técnico de materialización.

### 6.2 Estados involucrados

Se solicita definición aplicable a cada estado confirmado:

- postulante;
- aceptado;
- matriculado;
- graduado;
- retirado;
- eliminado;
- reprobado.

Para cada uno debe precisarse si existe efecto sobre acceso, qué regla lo determina y si es necesaria alguna excepción. No se asigna ninguna regla adicional mediante esta acta.

### 6.3 Responsabilidad administrativa

Se requiere definir:

- quién asigna permisos;
- quién remueve permisos;
- cuándo ocurre una transición y cuándo se hace efectiva para acceso;
- si existen excepciones, revisiones o aprobaciones adicionales;
- cómo se preservan permisos acumulativos independientes del estado.

### 6.4 Caso del permiso histórico 3

Actualmente, el permiso 3 posee asociación histórica con:

```text
* estudiante aceptado
* docente
```

Se solicita resolver si esta asociación debe mantenerse como compatibilidad histórica o separarse en el futuro, y bajo qué reglas. Esta acta no decide ni ejecuta dicha separación.

## 7. Relación con resoluciones vigentes

La resolución institucional de permisos funcionales ya aprobada confirma que el estado académico/institucional determina reglas de acceso y que estado no equivale a permiso funcional. Esta acta no reabre esa definición.

La validación pendiente es más acotada: determinar la política institucional para sincronizar, asignar manualmente o combinar ambas vías en las fuentes heredadas (`permiso_login` y sesión), incluida la responsabilidad administrativa y las excepciones. Hasta contar con esa definición, no se deduce una sincronización automática desde la resolución funcional.

## 8. Estado actual de EPIC-003

```text
EPIC-003

Arquitectura:
✅ Consolidada

Resolución permisos funcionales:
✅ Aprobada

Relación estado/permisos:
🟡 Pendiente validación institucional

Implementación:
No corresponde
```

## 9. Restricciones y cierre

Esta acta no debe interpretarse como autorización para:

- definir reglas por cuenta propia;
- crear permisos;
- aprobar sincronización automática;
- modificar el modelo actual;
- proponer una implementación obligatoria.

No se crean TASK mediante esta acta. Cualquier evolución posterior requerirá la resolución institucional solicitada, análisis técnico específico y autorización de alcance conforme al flujo documental vigente.

## 10. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md](../architecture/AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md)
- [AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md](../architecture/AT-EPIC003-CURSOS-VALIDACION-ACCESO-ACEPTADO-001.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)

## 11. Restricciones cumplidas

```text
Sin cambios de código.
Sin implementación.
Sin cambios ADR.
Sin cambios Roadmap.
Sin cambios Manual Maestro.
Sin commit.
```

Siguiente paso:

```text
Revisión institucional ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001
```
