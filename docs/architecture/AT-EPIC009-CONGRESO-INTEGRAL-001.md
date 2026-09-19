# AT-EPIC009-CONGRESO-INTEGRAL-001

## Estado

**IMPLEMENTADO Y VALIDADO FUNCIONALMENTE — listo para cierre técnico pre-commit.**

- **EPIC:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Congreso y Participación.
- **Nivel:** L3 — formalización arquitectónica y operativa.

## Problema y evidencia física

Congreso y Participación son conceptos distintos. El modelo físico confirmado es:

```text
congreso(id_congreso PK, nombre, ciudad, fecha_inicio, fecha_termino)
participacion(id_participacion PK, tipo_part, tipo_cong, otros_org NULL,
              nombre_mesa, coment_ponenc NULL, nom_aut NULL, id_aut NULL,
              id_coaut NULL, congreso FK)
```

`participacion.congreso` referencia `congreso.id_congreso`; `id_aut` e `id_coaut` referencian `usuario.id_usuario`. Las FK vigentes usan CASCADE/CASCADE. Falta la representación textual externa equivalente para el segundo rol.

## Modelo funcional e invariantes

Congreso es un recurso académico compartido y no se duplica por usuario. Participación es una actuación concreta: `CONGRESO 1:N PARTICIPACIÓN`. Un Congreso puede tener N Participaciones, incluso con el mismo `tipo_part`. Entre filas pueden repetirse Autor, Coautor, la pareja Autor/Coautor o un usuario en distintos roles.

`participacion.nombre_mesa` es un atributo textual de una Participación concreta. No es entidad Mesa, catálogo, relación compartida, propiedad ni autoridad. Dos textos iguales no representan necesariamente el mismo recurso lógico. No se crea tabla/catálogo de mesas ni UNIQUE sobre `nombre_mesa`, `tipo_part` o actores entre filas; en particular se prohíben UNIQUE equivalentes a `(congreso,id_aut)`, `(congreso,id_coaut)`, `(congreso,tipo_part,id_aut)`, `(congreso,tipo_part,id_coaut)` o `(congreso,tipo_part,id_aut,id_coaut)`.

Toda Participación nueva válida tiene exactamente dos roles principales:

| Rol | Interno | Externo |
| --- | --- | --- |
| Rol principal 1 | `id_aut` informado y `nom_aut` NULL | `id_aut` NULL y `nom_aut` informado |
| Rol principal 2 | `id_coaut` informado y `nom_coaut` NULL | `id_coaut` NULL y `nom_coaut` informado |

Cada rol es obligatorio y usa identidad interna XOR nombre externo; nunca ambos ni ninguno. Si ambos roles son internos, `id_aut <> id_coaut`. Participación e identidad no conceden autoridad. `otros_org` es opcional, libre, separado por comas y sin identidad, login ni permisos.

## Tipos vigentes

Los códigos están hardcodeados en `form-doc/scripts/congreso.js`; no existe catálogo de BD confirmado y no se creará uno.

| Código | Nombre | Roles visibles | Otros | Campos |
| --- | --- | --- | --- | --- |
| `tipo_part=1` | Coordinación | Coordinador/a / Coorganizador/a | Coorganizadores/as; vista: Otros Organizadores | `tipo_cong`: 1 Mesa, 2 Simposio; Nombre Mesa/Simposio; Comentarista |
| `tipo_part=2` | Expositor/a | Autor/a / Coautor/a | Coautores/as; vista: Otros Coautores | `tipo_cong`: 1 Mesa, 2 Simposio; Nombre Mesa/Simposio; Nombre Ponencia |
| `tipo_part=3` | Presentación Póster | Autor/a / Coautor/a | Coautores/as; vista: Otros Coautores | `tipo_cong`: 1 Mesa, 2 Simposio; Nombre Poster en la misma columna `nombre_mesa`; sin `coment_ponenc` |

Para Póster, `coment_ponenc` no se muestra ni se exige; se persistirá NULL o valor neutro compatible tras inspección. La validación heredada que lo exige para todos los tipos es defectuosa y debe corregirse.

## Alta, lectura y autorización

Estudiante/Profesor puede buscar Congresos existentes con metadatos mínimos, crear Congreso nuevo con primera Participación, agregar una nueva Participación propia a un Congreso existente y consultar su contexto. Al crear una fila debe informar `nombre_mesa` según tipo, ocupar explícitamente Rol 1 o Rol 2 y proporcionar un rol contrario válido. La fila se inserta siempre como nueva `participacion`.

Estudiante/Profesor no puede UPDATE/DELETE de Congreso o Participación, ni administrar participaciones ajenas. No existe en el contrato objetivo un flujo académico “buscar nombre_mesa → seleccionar Participación existente → cargar Autor/Coautor → modificar”. La funcionalidad heredada equivalente se elimina; después de seleccionar un Congreso se crea una nueva Participación. Si requiere una corrección, solicita intervención de Admin/Comité.

Admin/Comité tiene CRUD global dentro de invariantes y es el único que puede editar `nombre_mesa`, Roles 1/2, `otros_org`, campos específicos o eliminar Participación. Actor de sesión y subject de ficha se separan; IDs de cliente no son autoridad. La ficha muestra un Congreso una vez cuando el sujeto está en `id_aut` o `id_coaut`, y devuelve sólo sus Participaciones; Admin/Comité puede leer el conjunto global.

## Búsqueda y flujos

### Corrección VF-02: selección e identidad

La interfaz de roles de Congreso adopta el comportamiento observable de Publicación: selector del rol propio, subject fijado en ese rol y contraparte híbrida (autocomplete de académico interno o texto externo). La selección explícita conserva el ID interno; editar o vaciar el texto lo limpia. Una coincidencia textual no crea identidad interna. Los labels visibles siguen dependiendo de `tipo_part` conforme a la tabla de tipos.

Seleccionar un Congreso únicamente identifica el recurso y lista las Participaciones visibles/autorizadas. No hidrata campos de una fila. El detalle de una Participación se solicita sólo después de seleccionar inequívocamente `id_participacion`; la creación de una nueva Participación empieza limpia. `nombre_mesa` permanece estrictamente textual y nunca es selector, clave ni mecanismo de carga.

La única búsqueda global disponible para académico es de Congreso: parametrizada, acotada y limitada a `id_congreso`, `nombre`, `ciudad`, `fecha_inicio`, `fecha_termino`. No devuelve Participaciones o identidades de terceros y no equivale a READ administrativo global.

```text
Buscar Congreso existente
        ↓
Seleccionar Congreso
        ↓
Nueva Participación
        ↓
tipo_part + nombre_mesa/Nombre Poster + Rol propio + Rol contrario + otros_org
        ↓
Guardar
```

No imponer UNIQUE artificial por nombre/fecha/ciudad ni UNIQUE(usuario, congreso). Fallar Congreso, Participación o identidad revierte el CREATE compuesto. Eliminar Participación no elimina Congreso; eliminar Congreso es global, exige confirmación y puede activar la cascada vigente.

## Migración y preflight

La evolución es incremental; no se crea tabla de actores ni se reemplaza `participacion`.

- **Fase A:** preflight histórico ejecutado antes del SQL.
- **Fase B:** ejecutada manualmente y validada: `nom_coaut VARCHAR(60)` nullable.
- **Fase C:** implementación completada; exige XOR de ambos roles y la desigualdad interna en registros nuevos.
- **Fase D:** no ejecutada ni aprobada para este cierre; no se agregaron CHECKs/XOR físicos.

`id_coaut IS NULL` histórico es ambiguo: puede significar ausencia, registro incompleto o semántica heredada; no demuestra coautor externo. No se infiere ni realiza backfill de `nom_coaut`. Repetición de actores, parejas o tipos entre filas no es anomalía. Sólo se analizan conflictos dentro de la misma fila: roles ausentes, combinaciones XOR incompatibles o misma identidad interna en ambos roles.

Si históricos impiden constraints físicos o requieren reconstruir información inexistente, se detiene y eleva el caso. El rollback de esquema sólo puede eliminar `nom_coaut` mientras no pierda datos; tras almacenar valores nuevos no se ejecuta DROP COLUMN destructivo automático.

## Seguridad, alcance y decisión

La implementación aplica ADR-001 y la autoridad vigente de escritura: SQL parametrizado, resultados explícitos, HTTP/JSON coherente y transacciones multitabla. Requiere sesión, autorización backend, CSRF, validación backend, protección contra ficha ajena, render seguro y contratos asociativos de lectura.

La Task cubrió CRUD integral, migración, frontend, vistas, JavaScript, endpoint, modelo, consumidores, grid, formularios y VF. Excluye catálogo nuevo, ADR-003, Roadmap y otros objetos.

Congreso se consolidará como recurso compartido con N Participaciones autónomas. La modernización elimina el uso de `nombre_mesa` para reutilizar o editar Participaciones académicas y conserva ese atributo como texto propio de cada fila.
