# ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001

## 1. Identificación

- **Clasificación:** [GOV] gobierno y decisión institucional; [ARQ] evolución de identidad y autorización; [DOC] registro oficial EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **ADR relacionado:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **Propósito:** solicitar formalmente la definición institucional sobre la relación entre estado académico del estudiante y permisos efectivos de acceso.

Esta solicitud registra una brecha confirmada entre fuentes heredadas. No resuelve la política institucional, no aprueba una alternativa y no autoriza sincronización ni implementación.

## 2. Situación actual confirmada

Actualmente existen dos fuentes independientes:

### Estado académico

```text
estudiante.tipo_est
  ↓
situación académica del estudiante
```

### Permisos efectivos

```text
permiso_login
  ↓
$_SESSION
  ↓
autorización de acceso
```

La separación es coherente con el principio conceptual de ADR-002: estado académico no equivale a permiso funcional. La relación operativa entre ambas fuentes permanece pendiente de resolución institucional.

## 3. Brecha detectada y evidencia técnica

Un estudiante puede encontrarse en la siguiente situación:

```text
Estado académico:
Aceptado

pero:

Permiso efectivo:
No posee permiso asociado
```

Resultado observable:

```text
No se genera $_SESSION['aceptado']
y la aplicación no aplica los accesos esperados.
```

El login actual genera `$_SESSION['aceptado']` desde `permiso_login.id_permiso = 3`, no desde `estudiante.tipo_est`. Además, el cambio de estado académico actualiza `tipo_est`, pero no modifica automáticamente `permiso_login`.

El permiso histórico 3 posee una asociación técnica compartida entre estudiante aceptado y docente; por tanto, no identifica de forma unívoca un estado académico.

## 4. Decisión institucional requerida

Se solicita definir formalmente qué relación debe existir entre estado académico y permisos efectivos.

### Alternativa A — Estado determina permisos automáticamente

```text
Estado aceptado
  ↓
Permiso de acceso para estudiante aceptado
```

### Alternativa B — Permisos independientes del estado académico

```text
Estado académico
       *
Asignación administrativa de permisos
```

### Alternativa C — Modelo mixto

```text
Estado académico
       *
Rol institucional
       *
Participación
  ↓
Permisos funcionales
```

Las alternativas se presentan exclusivamente para resolución institucional. Esta acta no selecciona, combina ni implementa ninguna.

## 5. Matriz institucional requerida

| Estado académico | Acceso esperado | Observaciones |
| --- | --- | --- |
| Postulante | Pendiente | |
| Aceptado | Pendiente | |
| Matriculado | Pendiente | |
| Graduado | Pendiente | |
| Retirado | Pendiente | |
| Eliminado | Pendiente | |
| Reprobado | Pendiente | |

La matriz debe definir las reglas institucionales aplicables sin confundir estado con permiso funcional y sin inferir valores a partir del comportamiento técnico heredado.

## 6. Responsabilidades institucionales requeridas

Se solicita definir:

- quién asigna permisos;
- quién elimina permisos;
- cuándo ocurre y cuándo se hace efectiva una transición;
- si existen excepciones;
- cómo se auditan los cambios;
- cómo se conservan permisos acumulativos independientes del estado académico.

## 7. Caso del permiso histórico 3

Actualmente existe la siguiente asociación histórica:

```text
permiso 3
├── estudiante aceptado
└── docente
```

Se solicita resolución sobre una de las siguientes posibilidades, sin aprobarlas mediante esta acta:

- mantener la asociación como compatibilidad histórica;
- separar permisos;
- reemplazarla por un modelo funcional.

## 8. Impacto esperado de una resolución

Una definición institucional aprobada puede afectar potencialmente:

- login;
- permisos;
- sesiones;
- módulos académicos;
- usuarios existentes.

El impacto deberá ser analizado y autorizado posteriormente. Esta solicitud no implementa cambios ni obliga una solución técnica.

## 9. Estado actual

```text
EPIC-003

Arquitectura:
✅ Consolidada

Reglas permisos:
✅ Validadas parcialmente

Estado académico vs permisos:
🟡 Pendiente resolución institucional

Implementación:
No corresponde
```

## 10. Restricciones

Esta acta únicamente solicita resolución institucional. No debe interpretarse como autorización para:

- aprobar una alternativa;
- crear reglas;
- definir permisos;
- ordenar sincronización automática;
- modificar arquitectura;
- crear una TASK o implementar cambios.

## 11. Fuentes utilizadas

- [MANUAL_MAESTRO.md](../MANUAL_MAESTRO.md)
- [ROADMAP.md](../roadmap/ROADMAP.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md)
- [AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md](../architecture/AT-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md](ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md)
- [AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md](../architecture/AT-EPIC003-CURSOS-SESION-ACEPTADO-001.md)
- [ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md](ACTA-VALIDACION-EPIC003-REGLAS-PERMISOS-RESOLUCION-001.md)
- [ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md](ACTA-VALIDACION-EPIC003-AUTORIZACION-ESTADOS-001.md)
- [ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md](ACTA-VALIDACION-EPIC003-PERMISOS-FUNCIONALES-001.md)

## 12. Restricciones cumplidas

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
Resolución institucional ACTA-SOLICITUD-RESOLUCION-EPIC003-ESTADO-PERMISO-001
```
