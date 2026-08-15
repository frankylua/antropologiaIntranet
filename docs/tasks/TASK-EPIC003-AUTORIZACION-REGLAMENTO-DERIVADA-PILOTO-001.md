# TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001

## Piloto supervisado de autorización derivada para Reglamento

## 1. Identificación

- **Clasificación:** [TEC] Implementación piloto de autorización derivada; [ARQ] Validación de arquitectura EPIC-003; [DOC] Registro TASK.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Feature asociada:** [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- **AT asociado:** [AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001.md).
- **Estado:** Pendiente implementación supervisada.

## 2. Objetivo

Implementar y validar el primer piloto de resolución derivada de autorización en el módulo Reglamento, sin migración global y manteniendo el comportamiento observable, la compatibilidad con el legado y la reversibilidad.

```text
Contexto usuario
        ↓
Resolución de autorización derivada
        ↓
Authorization
        ↓
Reglamento
```

Temporalmente debe mantenerse la autorización histórica existente como fallback controlado.

## 3. Antecedentes y dependencias

- ✅ ADR-002.
- ✅ [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- ✅ [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- ✅ [AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001.md), aprobado técnicamente.
- ✅ [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).
- ✅ [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).

La dependencia habilitante es `AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001 aprobado`.

## 4. Alcance incluido

- Evaluar el acceso de Reglamento con una resolución derivada temporal para estudiante aceptado, estudiante matriculado, profesor aceptado, profesor integrante de Comité, usuario sin permisos y estado eliminado.
- Integrar incrementalmente la resolución derivada con `Authorization` y `form-doc/reglamento.php`.
- Conservar la autorización histórica como fallback legado controlado.
- Validar equivalencia de acceso, bloqueo y ausencia de cambios observables en Reglamento.
- Registrar cualquier diferencia entre resultado derivado y legado para revisión; no corregirla automáticamente.

## 5. Reglas funcionales esperadas

| Contexto | Resultado esperado en Reglamento |
| --- | --- |
| Estudiante aceptado | Acceso; conserva también Mi perfil. |
| Estudiante matriculado | Acceso equivalente al de estudiante aceptado. |
| Estudiante eliminado | Bloqueado. |
| Profesor aceptado | Acceso. |
| Profesor + Comité | Acceso; conserva permisos de profesor y Comité. |
| Usuario sin permisos | Bloqueado. |

El piloto se limita a la lectura de Reglamento. No habilita capacidades de escritura, administración ni acciones adicionales.

## 6. Estrategia técnica permitida

```text
Authorization existente
        +
Resolución derivada temporal
        +
Fallback legado
```

La integración no reemplaza completamente el mecanismo actual. Debe preservar la fuente histórica mientras se valida la regla derivada. El fallback no debe ocultar divergencias: estas deben quedar identificables para análisis posterior.

## 7. Archivos candidatos

Los siguientes son candidatos y deberán confirmarse durante la implementación supervisada:

- `src/Security/Authorization.php`.
- `form-doc/reglamento.php`.
- Archivos estrictamente necesarios para resolver el piloto.

## 8. Exclusiones y restricciones críticas

Esta TASK no autoriza:

- Modificar reglas institucionales, matriz de permisos, `permiso_login`, estados académicos, login ni base de datos.
- Crear permisos nuevos.
- Resolver el permiso histórico `3`.
- Migrar todos los módulos ni reemplazar globalmente el mecanismo legado.
- Modificar `login.php`, modelos académicos, SQL o `permiso_login` sin nueva aprobación.

## 9. Criterios de aceptación

### Técnicos

- Reglamento evalúa el resultado de autorización derivada mediante `Authorization` dentro del alcance del piloto.
- Se conserva el fallback histórico y no cambia el comportamiento observable aprobado.
- No existen cambios en login, sesión, modelo de datos, SQL, estados académicos, matriz de permisos ni `permiso_login`.
- Se ejecutan `php -l` sobre todos los archivos PHP modificados y `git diff --check` sin errores.

### Funcionales

| Caso | Resultado esperado |
| --- | --- |
| Estudiante aceptado | Acceso |
| Estudiante matriculado | Acceso |
| Estudiante eliminado | Bloqueado |
| Profesor aceptado | Acceso |
| Profesor + Comité | Acceso |
| Sin permisos | Bloqueado |

## 10. Reversión

La reversión debe retirar únicamente la integración temporal de resolución derivada y devolver Reglamento a la validación histórica previa mediante los archivos efectivamente modificados. No debe requerir restauración de datos ni afectar usuarios, permisos, login, sesión u otros módulos, porque el piloto no introduce cambios permanentes en datos.

## 11. Condiciones de detención

Detener la implementación supervisada y solicitar nueva revisión técnica si:

- Cambia el comportamiento existente de Reglamento.
- Se requiere modificar login o `permiso_login`.
- Se requiere modificar el modelo de datos, estados académicos o SQL.
- Aparecen reglas institucionales no definidas.
- El piloto exige permisos nuevos, migración global o cambios fuera de los archivos candidatos estrictamente necesarios.

## 12. Restricciones documentales

Esta TASK registra trabajo futuro y no implementa cambios. No modifica ADR, Roadmap ni Manual Maestro. No realiza SQL ni commit.

## 13. Validaciones de la creación documental

Al registrar esta TASK se deben ejecutar:

```text
git diff --check
git status --short
git diff --name-only
```

## 14. Fuentes

- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).
- [FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001](../features/FEATURE-EPIC003-AUTORIZACION-CENTRALIZADA-001.md).
- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001](../architecture/AT-EPIC003-PILOTO-AUTORIZACION-DERIVADA-001.md).

## 15. Siguiente paso

```text
Revisión técnica:
TASK-EPIC003-AUTORIZACION-REGLAMENTO-DERIVADA-PILOTO-001
```

Solo después de esa revisión corresponde la implementación supervisada.
