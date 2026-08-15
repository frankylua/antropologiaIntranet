# AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-003-001

## Análisis de resolución futura del permiso histórico 3

## 1. Identificación y alcance

- **Clasificación:** [ARQ] Resolución modelo identidad/autorización; [GOV] Separación estado académico vs. rol institucional; [DOC] Documento técnico EPIC-003.
- **EPIC asociado:** EPIC-003 — Separación segura entre estados académicos y roles de acceso.
- **Estado:** Pendiente revisión técnica.

Este AT analiza alternativas arquitectónicas para resolver la ambigüedad del permiso histórico 3. No decide una migración, no autoriza implementación y no modifica el modelo vigente.

## 2. Hallazgo confirmado

Modelo actual:

```text
permiso_login
        ↓
id_permiso = 3
        ↓
$_SESSION['aceptado']
```

El mismo identificador representa dos conceptos institucionales distintos:

```text
permiso 3
├── estudiante aceptado
└── docente
```

La equivalencia es ambigua porque un estado académico no es un rol institucional. En consecuencia, el permiso 3 no puede ser fuente confiable para derivar por sí solo el estado Aceptado ni el rol Profesor.

## 3. Dirección arquitectónica objetivo

```text
Estado académico
     +
Rol institucional
     +
Participación
        ↓
Reglas de autorización
        ↓
Permisos funcionales efectivos
```

La transición futura debe avanzar desde permisos y claves históricas hacia una autorización derivada desde contexto institucional. Este contexto distingue identidad, estado, rol y participación; no los infiere desde un permiso histórico ambiguo.

## 4. Alternativas evaluadas sin decisión

### A. Separación de permisos históricos

```text
permiso estudiante aceptado
        ≠
permiso docente
```

**Ventajas:** elimina la ambigüedad técnica en la fuente histórica y hace visible la distinción entre estado y rol.

**Riesgos e impacto de migración:** requiere inventariar usuarios existentes, clasificar correctamente docentes y estudiantes aceptados, preservar accesos acumulativos y actualizar todos los consumidores dependientes. Una clasificación incorrecta puede causar pérdida o ampliación de acceso.

**Compatibilidad:** exige coexistencia, mapeo temporal y validación de sesiones y módulos vigentes. No puede ejecutarse como cambio aislado ni sin una autorización de migración específica.

### B. Mantener temporalmente el permiso 3

```text
permiso 3
        ↓
compatibilidad legado

contexto derivado futuro
```

**Ventajas:** evita cambios inmediatos en usuarios, login, sesiones y módulos; permite incorporar una capa derivada de lectura de forma gradual.

**Riesgos:** mantiene la ambigüedad y duplica temporalmente las fuentes de decisión. El fallback histórico debe ser trazable; no debe ocultar diferencias con el resultado derivado.

**Tiempo de coexistencia y complejidad:** la convivencia debe ser limitada por hitos de validación: definición del contexto, inventario de usuarios afectados, equivalencia funcional por módulo y decisión formal de retiro. Sin estos hitos, la deuda histórica se perpetúa.

### C. Eliminar la dependencia del permiso histórico

```text
Estado / Rol / Participación
        ↓
Autorización derivada
```

**Alineación:** es la alternativa más coherente con ADR-002, al separar estado académico, rol institucional y participación de los permisos funcionales.

**Migración y riesgos:** requiere proveedores de contexto aprobados, catálogo funcional, reglas de composición, trazabilidad de decisiones y migración progresiva de consumidores. El retiro prematuro del permiso 3 puede bloquear a usuarios vigentes o retirar capacidades acumulativas.

**Dependencias previas:** resolución del contrato de contexto, tratamiento de usuarios existentes, validación del rol Profesor y participación Comité, y autorización separada para cualquier cambio en login, sesión o datos.

## 5. Impacto por tipo de actor

### Estudiantes

Para estudiantes, el estado académico debe ser la fuente conceptual de sus permisos. Aceptado y Matriculado son equivalentes en permisos funcionales; ambos conservan Reglamento, entre otros permisos aprobados. Eliminado no posee acceso a intranet.

El permiso 3 no debe decidir esta condición: no distingue Aceptado de Matriculado ni de Eliminado. Cualquier transición debe leer el estado institucional vigente y contrastarlo con el legado sin sincronización automática.

### Profesores y Comité

Profesor es un rol institucional. Comité es una participación que agrega capacidades a las del profesor, sin reemplazarlo. La resolución futura debe componer ambas dimensiones:

```text
Rol = Profesor
Participación = Comité
        ↓
Permisos Profesor + Comité
```

El permiso 3 no puede sustituir esta composición porque no expresa la participación ni diferencia al profesor del estudiante aceptado.

## 6. Impacto en login y sesiones

La creación actual de sesión depende de `permiso_login` y materializa claves históricas. Cambiar ese flujo tendría impacto transversal, riesgo de sesiones inconsistentes y posibles regresiones en módulos aún no migrados.

Este análisis no propone modificar login ni sesiones. Mientras exista compatibilidad, la sesión histórica permanece como mecanismo legado y el contexto derivado debe obtenerse mediante fuentes diferenciadas, con reglas de comparación explícitas.

## 7. Interpretación futura del contexto derivado

Ejemplo estudiante:

```text
Usuario
Estado = Aceptado
Rol = Estudiante
        ↓
Permisos de estudiante
```

Ejemplo profesor integrante de Comité:

```text
Usuario
Rol = Profesor
Participación = Comité
        ↓
Permisos Profesor + Comité
```

La regla consume dimensiones separadas. No convierte un ID de permiso, una clave de sesión ni una participación en sinónimo de otra dimensión.

## 8. Riesgos

- Usuarios existentes asociados al permiso 3 sin clasificación institucional verificable.
- Docentes actuales y estudiantes aceptados que comparten una representación histórica.
- Duplicidad temporal de decisiones entre autorización derivada y legado.
- Inconsistencias históricas entre estado, rol, participación y permiso.
- Migración incorrecta que produzca pérdida de acceso o privilegios no aprobados.

## 9. Recomendación técnica

Se recomienda mantener el permiso 3 exclusivamente como compatibilidad temporal mientras se valida un proveedor independiente de contexto, de solo lectura, para estado, rol y participación. La migración por módulos debe usar decisiones derivadas contrastables con el legado y registrar divergencias.

Esta recomendación no selecciona ni autoriza todavía la separación física de permisos históricos ni el retiro de la dependencia de permiso 3. Esos cambios requieren una decisión posterior, inventario de usuarios afectados, plan de reversión y aprobación específica.

## 10. Decisiones pendientes

- Fuente técnica de estado académico, rol institucional y participación.
- Contrato de contexto y política ante datos ausentes o inconsistentes.
- Inventario y clasificación validada de usuarios asociados al permiso 3.
- Estrategia, alcance y reversión de una eventual separación de permisos históricos.
- Política de coexistencia, trazabilidad y retiro gradual de claves históricas.
- Autorización separada para cualquier modificación de login, sesión, `permiso_login` o datos.

## 11. Exclusiones

Este AT no modifica ni autoriza modificar:

- `login.php`, `Authorization.php`, `permiso_login` ni sesiones;
- SQL, migraciones, tablas o modelo de datos;
- permisos técnicos;
- reglas institucionales, ADR, Roadmap, Manual Maestro o TASK.

No implementa cambios, no realiza migración y no realiza commit.

## 12. Fuentes

- [ADR-002](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md).
- [FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001](../features/FEATURE-EPIC003-MODELO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001](AT-EPIC003-DISENO-AUTORIZACION-DERIVADA-001.md).
- [AT-EPIC003-CONTEXTO-AUTORIZACION-DERIVADA-001](AT-EPIC003-CONTEXTO-AUTORIZACION-DERIVADA-001.md).
- [ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001](../governance/ACTA-VALIDACION-EPIC003-MATRIZ-GLOBAL-ACTORES-001.md).
- [ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001](../governance/ACTA-VALIDACION-EPIC003-SINCRONIZACION-ESTADO-PERMISO-001.md).

## 13. Siguiente paso posterior

```text
Revisión técnica:
AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-003-001
```
