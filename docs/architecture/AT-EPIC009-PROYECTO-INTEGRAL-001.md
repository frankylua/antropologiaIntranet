# AT-EPIC009-PROYECTO-INTEGRAL-001

## 1. Identificación

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto núcleo:** Proyecto de Investigación (`proyecto_investigacion`).
- **Nivel:** L3 — formalización arquitectónica/técnica.
- **Clasificación:** [ARQ] [INST] [DATA] [SEC] [GOV] [TX].

## 2. Estado

**APROBADO PARA GENERACIÓN DE TASK INTEGRAL, SUJETO A PREFLIGHT DE CASCADAS ANTES DE CUALQUIER CAMBIO FÍSICO.**

Este AT formaliza el alcance; no acredita implementación, migración ni validación funcional. No autoriza una modificación física de claves foráneas sin preflight, estrategia exacta y autorización de Dirección Técnica.

## 3. Contexto

Proyecto de Investigación es un objeto funcional autónomo de EPIC-009, consumido por fichas de estudiante y docente, vistas administrativas y scripts asociados de ficha/info/ver. Sus dependencias son `usuario`, `financiamiento`, `institucion` y la infraestructura compartida de conexión/escritura.

Tesis y Cotutela no forman parte de este AT. Financiamiento e Institución son catálogos dependientes: no se modernizan como objetos internos de Proyecto.

## 4. Evidencia actual

El esquema físico confirmado es:

```text
proyecto_investigacion(
  id_proyecto PK AUTO_INCREMENT,
  titulo VARCHAR(300) NOT NULL,
  folio VARCHAR(10) NOT NULL,
  fuente_financiamiento FK NOT NULL,
  anio_adjud INT NOT NULL,
  duracion INT NOT NULL,
  inst_proy FK NULL,
  id_inv FK NULL, id_coinv FK NULL,
  nom_inv VARCHAR(60) NULL, nom_coinv VARCHAR(60) NULL
)
```

`fuente_financiamiento` referencia `financiamiento.id_financ`; `inst_proy`, `id_inv` e `id_coinv` referencian respectivamente Institución y Usuario. Las FK vigentes usan `ON DELETE CASCADE / ON UPDATE CASCADE` y tienen índices individuales. No existe `UNIQUE` sobre `folio`, `titulo` ni combinaciones de actores.

La inspección local registró dos proyectos, con investigador interno en ambos, coinvestigador externo en ambos, `inst_proy` NULL en ambos y sin duplicados de folio/título, misma persona en ambos roles ni FK huérfanas detectables. Estos conteos describen sólo esa base local y no constituyen reglas institucionales.

El CRUD heredado incluye CREATE con `Proyecto::insertar()` y `insert-update` con `id_proy=0`; lecturas por folio, ID, usuario, investigador, coinvestigador e institución; y UPDATE parcial mediante `editarInv()` y `editarCoinv()`. No existe DELETE funcional de Proyecto. El endpoint `ajax/proyecto.php` conserva SQL interpolado, contrato de escritura no explícito, autorización insuficiente, IDs controlados por cliente, CSRF ausente y respuestas/HTTP inconsistentes.

## 5. Modelo funcional e invariantes

La identidad técnica única es `id_proyecto`. `folio` es atributo de negocio, no identidad técnica; no se autoriza `UNIQUE` sobre folio ni título.

Existen exactamente dos roles persistidos: Investigador y Coinvestigador. Cada uno es obligatorio y debe tener identidad interna XOR externa:

| Rol | Interna | Externa |
| --- | --- | --- |
| Investigador | `id_inv` informado y `nom_inv` NULL | `id_inv` NULL y `nom_inv` informado |
| Coinvestigador | `id_coinv` informado y `nom_coinv` NULL | `id_coinv` NULL y `nom_coinv` informado |

No se permite informar ID y nombre externo simultáneamente, ni dejar un rol sin identidad válida. Si ambos roles son internos, `id_inv != id_coinv`. Esta igualdad se valida inicialmente en aplicación/backend; no se incorpora constraint físico en este incremento. Los actores externos siguen siendo descriptivos: no se crean usuarios ni catálogos nuevos.

El alcance conserva los dos roles; no crea tabla N:N ni amplía a N participantes. `inst_proy` representa exclusivamente la institución del Coinvestigador, no una institución genérica del Proyecto. La futura Task debe definir su coherencia cuando el Coinvestigador cambie entre identidad interna/externa o sea reemplazado.

## 6. Actores y autorización

| Actor | CREATE | READ | UPDATE | DELETE |
| --- | --- | --- | --- | --- |
| Académico | Sí | Contextual | No | No |
| Admin | Sí | Sí, según contexto/global explícito | Sí, integral | Sí |
| Comité | Sí | Sí, según contexto/global explícito | Sí, integral | Sí |

La autoridad se deriva de sesión/backend. Actor y subject se mantienen separados; ningún ID enviado por cliente concede permisos. Toda escritura requiere CSRF.

El Académico que crea un Proyecto ocupa exactamente uno de los roles principales. Su READ contextual se limita a proyectos donde `id_inv = subject` o `id_coinv = subject`; no se concede visibilidad mediante un subject arbitrario del cliente. Admin/Comité pueden tener lectura global explícita conforme al patrón vigente, sin convertir automáticamente una lectura de ficha contextual en global.

## 7. CRUD objetivo

| Operación | Contrato objetivo |
| --- | --- |
| CREATE | Requerido. Crea Proyecto y sus dos roles como un mismo objeto; el Académico creador ocupa uno de ellos y la contraparte puede ser interna o externa. |
| READ | Requerido. Académico contextual; Admin/Comité según lectura contextual o global explícita autorizada. |
| UPDATE | Requerido sólo para Admin/Comité, siempre por `id_proyecto`. Edita integralmente `titulo`, `folio`, `fuente_financiamiento`, `anio_adjud`, `duracion`, `inst_proy`, Investigador y Coinvestigador. Nunca actualiza por folio. |
| DELETE | Requerido sólo para Admin/Comité, explícito por `id_proyecto`, con confirmación y separado de la edición de participantes. Es DELETE físico; no se incorpora soft-delete. |

La Task reemplazará la semántica heredada de “editar Proyecto = completar rol vacante” por UPDATE integral autorizado. Completar un rol sólo podrá subsistir, si fuese necesario, dentro del flujo contextual de alta/participación y no como sustituto de la edición administrativa. La UI distingue alta, lectura, edición y eliminación.

## 8. Persistencia / ADR-001

La futura Task aplicará ADR-001: SQL parametrizado, `ejecutarEscritura` para CREATE, UPDATE y DELETE, y evaluación explícita de `filasAfectadas` e `idInsertado` cuando corresponda. No se introduce ORM.

CREATE debe abandonar `ejecutarConsulta()` y el retorno `PDOStatement` heredado. UPDATE integra los métodos parciales actuales al contrato del objeto; DELETE usa `ejecutarEscritura`. Las operaciones compuestas que requieran consistencia entre Proyecto, roles y sus dependencias deben definir transacción y reversión. El endpoint normalizará respuestas y HTTP para distinguir éxito, validación, autorización, no encontrado y error inesperado, actualizando coordinadamente sus consumers.

## 9. Cascadas y conservación

Las cascadas actuales desde Financiamiento, Institución, Usuario investigador y Usuario coinvestigador hacia Proyecto son un riesgo de gobierno y persistencia: pueden producir eliminación histórica indirecta. Se distingue estrictamente entre:

- DELETE explícito de Proyecto por `id_proyecto`, aprobado para Admin/Comité.
- DELETE indirecto provocado por cascadas de dependencias, no aprobado como comportamiento institucional.

Las cascadas no se modificarán silenciosamente dentro del CRUD. Cualquier corrección física deberá realizarse mediante migración separada, sólo después del preflight y con autorización expresa de Dirección Técnica. Este AT no aprueba una transformación concreta de FK ni una migración ejecutable.

## 10. UX y consumers

La futura implementación reutilizará estándares visuales consolidados, evitará AJAX síncrono, estado global frágil e interpolación insegura de datos. Publicación y Congreso podrán inspeccionarse como referencias de UX únicamente cuando sean compatibles; sus reglas funcionales no constituyen precedente para Proyecto.

Se coordinarán `ajax/proyecto.php`, `src/Model/Proyecto.php`, `form-doc/scripts/proyecto.js`, `form-doc/agr.form.dat.acad.php` y los consumers de fichas, administración y scripts ficha/info/ver. La compatibilidad de respuestas heredadas es un riesgo que debe resolverse por transición coordinada, no manteniendo códigos HTTP incorrectos.

## 11. Preflight obligatorio

Antes de cualquier cambio físico, la Task deberá realizar y documentar:

1. Inspección de las FK actuales.
2. Conteos de datos y proyectos potencialmente afectados por cascadas.
3. Análisis de dependencias inversas.
4. Estrategia exacta de corrección de `ON DELETE CASCADE`.
5. Reversión o justificación de no reversión.
6. Autorización explícita antes de ejecutar una migración física.

No se ejecutan cambios de esquema en este AT.

## 12. Riesgos

- **Seguridad:** autorización heredada insuficiente, confusión actor/subject, IDs cliente y CSRF ausente.
- **Persistencia:** SQL interpolado, CREATE fuera de ADR-001 y cascadas destructivas.
- **Identidad:** combinación interno/externo, igualdad de roles y falsa autoridad por IDs cliente.
- **UX:** edición parcial heredada, AJAX síncrono, estado global e interpolaciones potencialmente inseguras.
- **Compatibilidad:** consumidores dependientes de respuestas heredadas.
- **Datos:** preflight obligatorio antes de corregir FK.

## 13. Alcance

Este AT cubre la consolidación futura del CRUD integral de `proyecto_investigacion`, sus dos roles, autorización, READ contextual, contrato HTTP, persistencia ADR-001, frontend/consumers y análisis de cascadas. `financiamiento`, `institucion`, `usuario` e infraestructura compartida son dependencias a reutilizar de forma compatible.

## 14. Fuera de alcance

- Tesis, Cotutela y cambios a Usuario.
- Modernización integral de Financiamiento o Institución.
- Tabla N:N de participantes, nuevos catálogos o creación automática de usuarios.
- Soft-delete, UNIQUE sobre folio/título, cambios de identidad global o constraint físico de igualdad de roles.
- ADR-003, Roadmap, Task, migración ejecutable y fase física de cascadas sin preflight/autorización.

## 15. Criterios para generar Task integral

La Task deberá especificar contratos HTTP/JSON, matrices de autorización backend, validaciones XOR e igualdad, flujo de `inst_proy`, transición de consumers, transacciones aplicables, confirmación de DELETE, render seguro y pruebas de CREATE/READ/UPDATE/DELETE, CSRF, IDOR, roles internos/externos y errores. También deberá ejecutar el preflight de cascadas antes de proponer cualquier DDL, rollback o ejecución física.

## 16. Decisiones institucionales incorporadas

- Proyecto es objeto autónomo; `id_proyecto` es su única identidad técnica.
- Folio y título no reciben unicidad artificial.
- Académico tiene CREATE y READ contextual; Admin/Comité tienen CRUD; roles/participación no conceden autoridad.
- UPDATE integral es exclusivo de Admin/Comité y se identifica por `id_proyecto`.
- Investigador y Coinvestigador son exactamente dos roles, cada uno interno XOR externo; no se permite la misma identidad interna en ambos.
- `inst_proy` pertenece semánticamente al Coinvestigador.
- DELETE físico explícito está aprobado sólo para Admin/Comité; DELETE por cascada requiere análisis de gobierno separado.
- El objeto adopta ADR-001, SQL parametrizado, CSRF, autorización backend y contrato HTTP consistente.

## 17. Dictamen

**AT CREADO — LISTO PARA REVISIÓN DE DIRECCIÓN TÉCNICA.** La generación de la Task integral queda habilitada. Cualquier cambio físico sobre cascadas permanece condicionado al preflight, estrategia técnica y autorización explícita indicados en este AT.
