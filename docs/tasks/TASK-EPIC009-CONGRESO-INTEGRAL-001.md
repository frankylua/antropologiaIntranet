# TASK-EPIC009-CONGRESO-INTEGRAL-001

## Estado

**IMPLEMENTADA Y VALIDADA FUNCIONALMENTE — lista para cierre técnico pre-commit.**

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Congreso y Participación.
- **Tipo:** Task integral; no dividir migración, búsqueda, identidad, frontend, endpoint ni CRUD en micro-Tasks.
- **Fuente arquitectónica:** `docs/architecture/AT-EPIC009-CONGRESO-INTEGRAL-001.md`.

## Objetivo y alcance

Implementar CRUD seguro, autorizado y transaccional de Congreso y Participación, respetando `CONGRESO 1:N PARTICIPACIÓN`, dos roles principales obligatorios y los tres tipos vigentes. Incluye migración, preflight histórico, backend, frontend, consumers, seguridad, grid, formularios, validaciones y evidencia técnica. La VF es ejecutada por el usuario.

No crear catálogo de `tipo_part`, `tipo_cong` o mesas; no modificar ADR-003, Roadmap, Publicación, Proyecto, Tesis, Reglamento o Cursos. No modificar Authorization global si la autorización puede expresarse mediante APIs vigentes.

## Capas y archivos candidatos

Inspeccionar según evidencia real: `form-doc/scripts/congreso.js`, formularios, vistas y scripts de ficha consumidores, `ajax/congreso.php`, `src/Model/Congreso.php`, autoridad vigente de escritura, migración y pruebas. Eliminar del frontend, endpoint y modelo el flujo heredado que busca `nombre_mesa`, carga una Participación existente y permite al académico modificar Autor/Coautor. Todo archivo adicional requiere consumer confirmado, cambio mínimo y relación con el alcance. Usar contratos asociativos, no acceso `p[0]/p[1]`.

## Matriz CRUD

| Recurso / acción | Estudiante / Profesor | Admin / Comité |
| --- | --- | --- |
| Congreso CREATE | Nuevo sólo con primera Participación propia | Global, con invariantes |
| Congreso READ | Búsqueda mínima y lectura contextual propia | Global |
| Congreso UPDATE / DELETE | No permitido | Global; DELETE confirmado |
| Participación CREATE | Nueva propia, en Congreso nuevo o existente | Global, con invariantes |
| Participación READ | Sólo propias contextuales | Global |
| Participación UPDATE / DELETE | No permitido | Global; DELETE no elimina Congreso |

Autor/Coautor no concede autoridad. Derivar actor desde sesión, separar actor de subject de ficha y nunca aceptar IDs de cliente como autoridad. Admin/Comité es el único que edita `nombre_mesa`, roles, `otros_org` y campos específicos, o elimina Participación.

## nombre_mesa y cardinalidad

`nombre_mesa` es atributo textual de una fila `participacion`, no una entidad, catálogo, relación compartida, recurso recuperable, propiedad ni autoridad. Después de seleccionar un Congreso, el académico siempre crea una nueva Participación; nunca selecciona una Mesa/Participación existente para editarla.

Un Congreso admite N Participaciones y N filas del mismo `tipo_part`. Se pueden repetir Autor, Coautor, pareja Autor/Coautor y tipos en filas distintas. No crear UNIQUE sobre `nombre_mesa`, `tipo_part` o actores entre filas; se prohíben equivalentes a `(congreso,id_aut)`, `(congreso,id_coaut)`, `(congreso,tipo_part,id_aut)`, `(congreso,tipo_part,id_coaut)` y `(congreso,tipo_part,id_aut,id_coaut)`.

## Migración y preflight ejecutados

Se ejecutó **Fase A**, sin SQL de cambio: inspección de schema, constraints, consumers y datos de `participacion`.

`id_coaut IS NULL` histórico es ambiguo: no prueba coautor externo y puede representar dato ausente, Participación incompleta o semántica antigua. No inventar datos, inferir `nom_coaut` ni hacer backfill. Repeticiones entre filas no son duplicados inválidos; evaluar sólo conflictos dentro de una misma fila.

**Fase B** fue ejecutada manualmente y validada para agregar `nom_coaut VARCHAR(60)` nullable; **Fase C** fue implementada. **Fase D** no fue ejecutada ni aprobada para este cierre: no se agregaron constraints físicos. Tras almacenar datos en `nom_coaut`, no ejecutar DROP COLUMN destructivo como rollback automático.

La aplicación exige para registros nuevos: `id_aut XOR nom_aut`, `id_coaut XOR nom_coaut`, ambos roles obligatorios y `id_aut <> id_coaut` cuando ambos sean internos. Si históricos impiden constraints físicos o requieren reconstrucción inexistente, detener y elevar. No crear tabla de actores ni reemplazar `participacion`; no cambiar FK vigentes salvo bloqueo demostrado.

## Identidad y tipos

Cada Participación nueva válida tiene exactamente dos roles: usuario interno seleccionado explícitamente o externo textual, nunca ambos ni ninguno. El académico de ficha ocupa Rol 1 o Rol 2; editar texto de autocomplete limpia el ID. `otros_org` sigue libre, opcional y separado por comas.

| `tipo_part` | Nombre | Campos específicos |
| --- | --- | --- |
| 1 | Coordinación | Mesa/Simposio; Nombre Mesa/Simposio; Comentarista; Coordinador/a / Coorganizador/a; Coorganizadores/as / Otros Organizadores |
| 2 | Expositor/a | Mesa/Simposio; Nombre Mesa/Simposio; Nombre Ponencia; Autor/a / Coautor/a; Coautores/as / Otros Coautores |
| 3 | Presentación Póster | Mesa/Simposio; Nombre Poster en `nombre_mesa`; sin `coment_ponenc`; Autor/a / Coautor/a; Coautores/as / Otros Coautores |

Preservar `tipo_cong=1` Mesa y `tipo_cong=2` Simposio. Póster no muestra ni exige `coment_ponenc`; persistir NULL o valor neutro compatible tras inspección. Corregir la validación heredada que lo exige para todos los tipos.

## Flujos, endpoint y frontend

### Corrección VF-02

Congreso replica el patrón UX vigente de Autor/Coautor de Publicación, conservando sus labels históricos por `tipo_part`: Coordinador/a/Coorganizador/a para Coordinación y Autor/a/Coautor/a para Expositor/a y Póster. Al seleccionar Congreso se listan Participaciones autorizadas y se ofrece Nueva Participación, sin cargar datos de ninguna fila. Sólo la selección explícita de `id_participacion` habilita su detalle; nunca se usa `nombre_mesa`. La contraparte es híbrida interno/externo y la edición o limpieza de texto borra su ID interno.

La única búsqueda global académica es de Congreso, parametrizada y acotada, con salida exclusiva: `id_congreso`, `nombre`, `ciudad`, `fecha_inicio`, `fecha_termino`. No equivale a READ global ni devuelve identidades o Participaciones ajenas.

- Congreso inexistente: CREATE Congreso + primera Participación en una única transacción; cualquier fallo revierte todo.
- Congreso existente: INSERT sólo nueva Participación con `congreso=id_congreso`; no duplicar Congreso ni imponer UNIQUE(usuario, congreso).
- Durante el alta sobre Congreso existente, Ciudad y ambas fechas quedan bloqueadas como contexto; Nombre permanece habilitado. Editar manualmente Nombre limpia `id_congreso`, los metadatos previos y el estado temporal de Participación, reactiva Ciudad/fechas y permite una nueva búsqueda o crear un Congreso nuevo. UPDATE Congreso sigue siendo una operación administrativa separada.
- Académico: no editar/eliminar después del alta; eliminar selector/carga de Participación por `nombre_mesa`.
- Admin/Comité: acciones separadas para UPDATE/DELETE de Congreso y de cada Participación; DELETE de Participación no elimina Congreso y DELETE global puede usar cascada vigente.

Aplicar ADR-001, autoridad vigente de escritura, SQL parametrizado, resultados explícitos, contratos HTTP/JSON y códigos de estado coherentes, sesión, autorización backend, CSRF en toda escritura, validación backend, protección horizontal y render seguro. Preservar funciones globales necesarias, `cadenaMay` en textos descriptivos, `.text()`/`.val()` o equivalente, X de cancelación, confirmaciones y retorno al grid correcto.

## Criterios de aceptación

- Congreso compartido con N Participaciones; sin duplicación por usuario.
- `nombre_mesa` no identifica, carga ni autoriza edición de Participación para académico.
- Dos roles obligatorios internos XOR externos; misma identidad interna rechazada en ambos roles.
- CREATE nuevo atómico; CREATE en existente inserta sólo nueva Participación.
- Los tres tipos, labels, `otros_org` y campos cumplen esta Task; Póster no exige `coment_ponenc`.
- READ contextual no expone participaciones ajenas ni duplica Congreso.
- El listado agrupa por `id_congreso`: una única card por Congreso contiene N Participaciones autorizadas, aunque existan Congresos distintos con el mismo nombre.
- La lectura resuelve nombres visibles de actores internos; la card reutiliza el estándar visual de Publicación y representa cada Participación como control Bootstrap colapsable, identificado por su ID.
- Académico no logra UPDATE/DELETE; Admin/Comité administra globalmente.
- Editar Congreso abre una vista precargada separada de la edición individual de Participación; cada objeto conserva su propio guardado UPDATE y su propia cancelación al listado contextual.
- La card de lectura mantiene sólo acciones de Congreso; la edición individual de Participación se accede desde la vista Editar Congreso.
- CSRF, autorización, validación backend, SQL parametrizado, render seguro, HTTP/JSON coherente y confirmación de DELETE están cubiertos.

## Validaciones técnicas y VF

La implementación, inspección y validación estática fueron completadas; la VF fue ejecutada y aprobada por el usuario. Se validaron sintaxis PHP, `git diff --check`, staging, migración, transacciones, contratos, autorización y consumers.

VF aprobada: CRUD de Congreso y Participación, búsqueda y alta sobre Congreso existente, agrupación sin duplicado, roles internos/externos, edición y eliminación independientes, permisos Académico/Admin/Comité, actor/subject y CSRF.

## Reversión y detención

Las operaciones runtime revierten transaccionalmente. La migración sólo hace rollback destructivo mientras sea seguro y sin pérdida histórica; después conserva `nom_coaut` y revierte operativamente aplicación/contratos.

Detener sin modificar si schema o decisión oficial contradice el AT; históricos son incompatibles con restricciones físicas; no se distingue interno/externo sin inventar datos; aparece semántica contradictoria de tipos; se requiere nueva decisión institucional; no se garantiza atomicidad; se requiere alterar Authorization global sin decisión; aparecen consumers no contemplados; deben cambiarse FK vigentes sin bloqueo técnico demostrado; o un consumer vigente depende institucionalmente de seleccionar `nombre_mesa` para editar Participación y retirarlo exige una nueva decisión funcional. No preservar esa función heredada automáticamente: elevar el caso.
