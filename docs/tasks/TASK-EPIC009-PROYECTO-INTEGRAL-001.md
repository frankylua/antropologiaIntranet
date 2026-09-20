# TASK-EPIC009-PROYECTO-INTEGRAL-001

## 1. Identificación y estado

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Proyecto de Investigación (`proyecto_investigacion`).
- **Nivel:** L3 — Task integral bajo AT aprobado.
- **Fuente normativa:** [AT-EPIC009-PROYECTO-INTEGRAL-001](../architecture/AT-EPIC009-PROYECTO-INTEGRAL-001.md).
- **Estado:** **IMPLEMENTACIÓN TÉCNICA COMPLETADA — VALIDACIÓN FUNCIONAL PASS — CRUD INTEGRAL CERRADO; FASE D DE CASCADAS BLOQUEADA, SEPARADA Y FUERA DEL CIERRE CRUD ACTUAL.**
- **Implementación, revisión técnica, VF y cierre:** completados.

Esta Task implementa una única unidad integral para Proyecto. Debe actualizarse con resultados de fases, bloqueos, VF y cierre; no crea addenda ni micro-Tasks.

## 2. Frontera

Consolidar CREATE, READ, UPDATE y DELETE de `proyecto_investigacion`, con autorización backend, separación actor/subject, CSRF, SQL parametrizado, HTTP/JSON uniforme, validaciones de roles, UI segura y consumers coordinados.

Capas previstas, sujetas a Fase A: `src/Model/Proyecto.php`, `ajax/proyecto.php`, `form-doc/scripts/proyecto.js`, `form-doc/agr.form.dat.acad.php` y únicamente consumers directos identificados y justificados de fichas de estudiante, docente, administración y scripts ficha/info/ver.

Dependencias: `usuario`, `financiamiento`, `institucion`, `Authorization` e infraestructura de persistencia. Están protegidos Authorization, helpers globales, Login/Usuario, Financiamiento, Institución, Publicación, Congreso, Tesis, Cotutela, ADR y Roadmap; si alguno exige cambio, detener y reportar.

Fuera de alcance: Tesis, Cotutela, modernización integral de dependencias, cambios globales de Usuario, N:N de participantes, catálogos nuevos, soft-delete, UNIQUE sobre folio/título/actores, ADR-003 y cambios físicos no autorizados de FK/cascadas.

## 3. Modelo funcional e invariantes

`id_proyecto` es la identidad técnica. `folio` es atributo de negocio y nunca identifica UPDATE ni autoriza operaciones; no se agrega `UNIQUE` sobre folio, título, actores o combinaciones.

| Rol | Identidad interna | Identidad externa |
| --- | --- | --- |
| Investigador | `id_inv` informado, `nom_inv` NULL | `id_inv` NULL, `nom_inv` informado |
| Coinvestigador | `id_coinv` informado, `nom_coinv` NULL | `id_coinv` NULL, `nom_coinv` informado |

Existen exactamente esos dos roles obligatorios. Cada rol cumple XOR: no acepta ID y nombre simultáneos ni ambos ausentes. Si ambos son internos, `id_inv != id_coinv`; la validación inicial es backend y no incorpora constraint físico. Los externos son texto descriptivo, sin usuarios ni catálogos nuevos. Seleccionar un interno conserva su ID; editar su texto lo invalida según patrón consolidado. No se exponen IDs técnicos.

`inst_proy` es exclusivamente la institución del Coinvestigador. Antes de implementar se definirá y validará su limpieza o conservación cuando el Coinvestigador cambie, pase interno↔externo o sea reemplazado; no crear una segunda institución para Investigador.

## 4. CRUD y autorización

| Operación | Estado heredado | Estado objetivo |
| --- | --- | --- |
| CREATE | Existe defectuoso | Consolidado |
| READ | Existe defectuoso | Consolidado |
| UPDATE | Parcial | Integral Admin/Comité |
| DELETE | Falta | Implementado Admin/Comité |

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Académico | Sí | Sólo contextual | No | No |
| Admin | Sí | Contextual o global explícito | Sí, integral | Sí |
| Comité | Sí | Contextual o global explícito | Sí, integral | Sí |

Actor proviene sólo de sesión; subject se valida y permanece separado. IDs cliente son funcionales, nunca autoridad. Académico sólo lee con `id_inv = subject` o `id_coinv = subject`; al crear ocupa exactamente un rol y la contraparte puede ser interna o externa.

UPDATE de Admin/Comité se identifica por `id_proyecto` y permite `titulo`, `folio`, `fuente_financiamiento`, `anio_adjud`, `duracion`, `inst_proy`, Investigador y Coinvestigador. `editarInv()`/`editarCoinv()` no satisfacen solos el contrato. DELETE es físico, explícito, por ID y con confirmación; nunca soft-delete ni edición de participantes.

## 5. Contratos de aplicación

El endpoint exige sesión, Authorization vigente, validación/tipado del payload y CSRF en CREATE/UPDATE/DELETE. Normalizar operaciones y consumers con JSON estructurado (`status`, `data`, `message`, o patrón consolidado equivalente) y HTTP: 2xx éxito, 400 inválido, 401 sin sesión, 403 sin autorización/CSRF, 404 inexistente y 500 inesperado. No conservar rutas o respuestas inseguras por compatibilidad.

Todas las escrituras usan SQL parametrizado y `ejecutarEscritura()`, evaluando `filasAfectadas` e `idInsertado`. CREATE migra desde `ejecutarConsulta()`; UPDATE se consolida; DELETE se implementa con ese contrato. No introducir ORM, DAO, Repository ni capa nueva. Usar transacción sólo si una operación concreta realiza más de una escritura que debe ser atómica, identificándola y definiendo reversión durante implementación.

`fuente_financiamiento` es obligatorio y reutiliza su catálogo. `inst_proy` reutiliza Institución. No modernizar estas dependencias; si bloquean estrictamente Proyecto, detener sólo ese subflujo y reportar.

## 6. UX y flujo heredado

Usar Publicación/Congreso sólo como patrón visual, formularios, autocomplete, acciones, confirmación y refresh; no copiar reglas de dominio. Cards/listados muestran folio, título, año, duración, financiamiento, roles e institución del Coinvestigador cuando aplique, sin IDs técnicos. Admin/Comité ven Editar/Eliminar autorizados; Académico no. Evitar AJAX síncrono, estado global frágil e interpolación insegura; usar render seguro, refresh sin duplicación y formularios CREATE/EDIT diferenciados.

Fase A debe inspeccionar búsqueda por folio y rol vacante: callers y actor real. Folio no identifica recurso; si la búsqueda persiste, selecciona `id_proyecto` y no produce UPDATE accidental. Separar crear Proyecto de completar rol. Si completar rol requiere UPDATE de Académico, detener ese subflujo y solicitar decisión de Dirección Técnica; no reinterpretar CREATE para evadir la política.

## 7. Fases

### Fase A — Preflight y contratos reales

Inspección sólo lectura de FK de `proyecto_investigacion`: nombres, columnas, tablas referenciadas, `ON DELETE`, `ON UPDATE` e índices. Levantar conteos anonimizados de investigador, coinvestigador, financiamiento e institución; analizar impacto de eliminar padres, dependencias inversas, consumers, permisos, respuestas y flujo folio/rol vacante. No ejecutar DELETE, no modificar FK y no crear migración.

Clasificar cada FK: A compatible con conservación institucional; B incompatible y candidata a corrección; C evidencia insuficiente/decisión requerida. Proponer, sin ejecutar, comportamiento objetivo, estrategia física potencial, riesgos, precondiciones y reversión/no reversión.

### Fase B — Modernización de aplicación

Implementar modelo, endpoint, autorización, persistencia, CRUD integral, frontend y consumers directos confirmados, cumpliendo este contrato. Todo consumer adicional se identifica y justifica antes de modificarlo.

### Fase C — Validación técnica

Ejecutar `php -l` para PHP modificado, revisión estática pertinente, `git diff --check`, `git status --short` y `git diff --cached --name-only`. Codex no ejecuta VF ni pruebas funcionales/escrituras de BD.

### Fase D — Migración física de cascadas

**BLOQUEADA HASTA AUTORIZACIÓN DE DIRECCIÓN TÉCNICA.** Tras Fase A, entregar evidencia; Dirección Técnica decide si no cambia esquema, si se crea migración, qué constraints cambiar y la reversión. Esta Task no autoriza crear ni ejecutar migración.

### Fase E — Validación funcional

Responsable: usuario. Registrar evidencia y aprobación explícita; pendiente hasta entonces.

### Fase F — Cierre técnico / commit

Sólo después de VF aprobada y staging controlado.

## 8. VF mínima y aceptación

| Área | Cobertura mínima |
| --- | --- |
| Académico | READ propio en ambos roles, no ver ajeno, CREATE en ambos roles, contraparte interna/externa, bloqueo UPDATE/DELETE e IDs/subject manipulados sin elevación. |
| Admin/Comité | READ contextual, READ global explícito, CREATE, UPDATE integral, DELETE y recurso inexistente. |
| Invariantes | XOR de ambos roles, misma identidad interna rechazada, financiamiento/institución inválidos e `inst_proy` coherente. |
| Seguridad | Sin sesión, CSRF inválido, payload inválido, textos especiales e IDs manipulados. |
| UI/regresión | Cards, CREATE/EDIT, nombres internos/externos, refresh, confirmación DELETE, sin IDs visibles; fichas estudiante/docente, administración y catálogos consumidores. |

Cierre requiere CRUD integral, matriz autorizada, separación actor/subject, ADR-001 y SQL parametrizado, invariantes backend, UI/consumers migrados, VF aprobada por usuario y ninguna migración física no autorizada.

## 9. Reversión y validaciones

Registrar baseline por archivo. Revertir sólo hunks de esta Task, conservando coherencia endpoint/modelo/frontend; no usar reset global. Cambios físicos requieren estrategia específica previa y nunca DROP destructivo sin análisis.

Al final de cada fase aplicable: `php -l` para PHP modificado, `git diff --check`, `git status --short` y `git diff --cached --name-only`. Staging queda vacío hasta autorización de cierre.

## 10. CONDICIONES PARA DETENER LA EJECUCIÓN

Detener sin modificaciones adicionales y reportar si:

1. el AT no está disponible, es ilegible o contradice decisiones;
2. el esquema real difiere materialmente del discovery;
3. el rol vacante requiere UPDATE Académico;
4. una dependencia exige modificar un objeto protegido o no se puede separar actor/subject;
5. existe dato histórico incompatible con invariantes;
6. cascadas requieren estrategia física no autorizada, migración destructiva o irreversible;
7. hay cambio local ajeno en archivo objetivo, stage previo o no puede preservarse trabajo ajeno.

No resolver estas condiciones por cuenta propia.

## 11. Registro de ejecución

- **Fase A:** cerrada. RV-B confirmado: el flujo heredado de rol vacante era UPDATE académico y se retira. CAS-B confirmado: las cuatro FK salientes mantienen CASCADE/CASCADE; Fase D no está autorizada.
- **Fase B:** implementada técnicamente. Se modernizaron `src/Model/Proyecto.php`, `ajax/proyecto.php` y `form-doc/scripts/proyecto.js`; no fue necesario cambiar consumers directos porque se conserva `cargarProy(usuario, destino)` y el valor se trata sólo como subject validable en backend.
- **Fase C:** revisión técnica final PASS: Modelo, endpoint, frontend y callers/consumidores PASS; `php -l` correcto en Modelo y endpoint, `node --check` correcto en frontend y `git diff --check` correcto.
- **Fase D — gobierno físico de FKs/CASCADE:** BLOQUEADA / SEPARADA / FUERA DEL CIERRE CRUD ACTUAL.
- **VF:** PASS. CREATE académico, READ contextual, UPDATE integral Admin/Comité, DELETE físico Admin/Comité, autorización/CSRF/sesión y UX final aprobados.
- **Cierre:** CRUD integral de Proyecto de Investigación cerrado.
