# TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001

# Resultado — Task integral Pueblo consolidada tras revisión técnica

## 1. Estado Git

Baseline comprobado antes de esta consolidación:

```text
Rama: refactor/fase-0-seguridad
HEAD: a3c75e5b03abb9ba3aa280ea05fd49900cacff82
Staging: vacío
Cambios versionables previos esperados:
?? docs/architecture/AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md
?? docs/tasks/TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md
```

La rama, el staging y el worktree coincidieron con las precondiciones de consolidación. La revisión técnica integral había emitido el dictamen `D. Task requiere addendum del AT.` por dos consumers READ defectuosos. La consolidación mínima posterior aportada resuelve ese alcance directamente sobre esta Task, sin crear un Addendum ni una Task adicional. Esta actualización mantiene el mismo HEAD, no modifica el AT original y no autoriza implementación, VF, staging, commit ni push.

## 2. Fuentes documentales

Fuentes aprobadas y leídas íntegramente:

- [AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001](../architecture/AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md).
- `AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001 — Consolidación mínima posterior a revisión técnica`, aportada como fuente de esta actualización.
- Dictamen fuente: **B. Pueblo puede cerrarse mediante Task integral con observaciones no bloqueantes.**
- Dictamen de consolidación: **A. Pueblo puede cerrarse mediante una única Task integral.**
- Antecedente de revisión técnica: **D. Task requiere addendum del AT.**
- Objeto: Pueblo indígena.
- EPIC funcional: EPIC-001 — Autorización y permisos.
- Marco metodológico: EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.

El contraste con el código en el HEAD indicado confirmó los supuestos de autorización del AT y corrigió su conclusión READ:

- READ sigue disponible sin guard administrativo; endpoint, modelo y contrato son correctos, pero dos scripts consumers son incompatibles con el retorno asociativo.
- CREATE y UPDATE siguen alcanzando persistencia sin autorización backend.
- DELETE conserva el guard Admin/Comité, HTTP 403, validación de dependencias y transacción.
- `Authorization::hasAny()` continúa disponible y expresa la política acumulativa requerida.
- no apareció otra ruta activa de escritura de Pueblo.
- no se requiere modificar modelo, persistencia, base de datos, login, sesión ni capacidades.

La consolidación mínima resuelve documentalmente la contradicción y amplía esta misma Task a los dos callers necesarios. No se crea un Addendum ni una Task adicional.

## 3. SHA de fuentes

```text
Algoritmo: SHA-256
Archivo: docs/architecture/AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md
SHA-256: E608345484B52924C7113AB114B7AD1B289461012EDFE2EC1609C780A04B83B7

Fuente: AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001 — Consolidación mínima posterior a revisión técnica
SHA-256: E65996BBD6B2D9927FC59F06DCD3AE32B06C45142F14BE9B317F57F3948B0E11

Task previa a consolidación:
SHA-256: 51BF07F07F8496EC9A1A607127470ADE283CEE4E8873AC3663816D11BBB16A5C
```

Estas huellas identifican las fuentes exactas utilizadas para definir el alcance, los criterios de aceptación, la VF y las restricciones de esta Task. Si el AT o la consolidación cambia, la revisión técnica deberá recalcular las huellas y comprobar nuevamente que los dictámenes y hallazgos siguen siendo aplicables antes de implementar.

## 4. Documento actualizado

```text
Task: TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001
Documento: docs/tasks/TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md
Objeto: Pueblo indígena
Estado: Consolidada; pendiente de una única revisión técnica final previa a implementación
Tipo: Task integral por objeto
Clasificación: [EPIC-001] [EPIC-009] [TASK-INTEGRAL] [AUTH] [CRUD] [SEC] [VF] [REV] [GOV]
```

Esta Task define un único incremento futuro para READ, CREATE, UPDATE y DELETE. Queda prohibido derivar micro-Tasks separadas para CREATE, UPDATE o DELETE a partir de este documento.

## 5. Objetivo integral

Dejar el objeto Pueblo cerrado desde la perspectiva de autorización de EPIC-001 mediante una sola implementación y una sola VF integral.

El incremento deberá:

- preservar el endpoint y contrato READ y corregir los dos callers incompatibles;
- proteger CREATE y UPDATE en el servidor para Admin o Comité;
- preservar DELETE sin regresión funcional ni de seguridad;
- hacer de `ajax/pueblo.php` la autoridad efectiva sobre las mutaciones;
- devolver HTTP 403 y JSON coherente ante cada mutación no autorizada;
- mostrar de forma comprensible el rechazo de CREATE/UPDATE en el frontend administrativo;
- conservar modelos, persistencia, datos, esquema, login, sesión y capacidades;
- validar conjuntamente CRUD, frontend, respuestas, ausencia de escrituras rechazadas y consumidores READ.

Proteger únicamente CREATE y UPDATE no bastará para declarar cerrada esta Task. Las cuatro operaciones aplicables y sus consumidores deben superar la VF integral.

## 6. Matriz CRUD

| Operación | Estado actual confirmado | Estado objetivo | Actores objetivo | Cambio previsto |
| --- | --- | --- | --- | --- |
| READ | Defectuoso en consumers: endpoint/modelo/contrato correctos; 2 scripts afectan 4 superficies | Corregido y funcional | Consumidores públicos y autenticados actuales | Adaptar dos callers, sin cambiar contrato/helper |
| CREATE | Existe, pero sin guard backend | Protegido en servidor | Admin OR Comité | Guard antes de escribir + 403 |
| UPDATE | Existe, pero sin guard backend | Protegido en servidor | Admin OR Comité | Guard antes de escribir + 403 |
| DELETE | Protegido y funcional | Preservado sin regresión | Admin OR Comité | Sólo regularización mínima del evaluador |

Regla de cierre:

```text
READ funcional
+ CREATE protegido
+ UPDATE protegido
+ DELETE protegido
+ frontend compatible
+ 403 coherente
+ consumers sin regresión
+ VF integral aprobada
= Pueblo finalizable bajo EPIC-001
```

Si una operación necesaria queda defectuosa, Pueblo permanece **NO FINALIZADO**.

## 7. READ

Estado actual: **DEFECTUOSO EN CONSUMERS**.

El endpoint, el modelo y el contrato READ son correctos: `ajax/pueblo.php` llama a `Pueblo::mostrar()` y devuelve filas asociativas con `id_pueblo` y `pueblo`. El defecto está limitado a dos callers de ficha que invocan `ajaxSelect()` sin declarar esas propiedades y que, por tanto, intentan consumir índices numéricos inexistentes bajo `PDO::FETCH_ASSOC`.

La rama `op=read` de `ajax/pueblo.php` debe continuar llamando a `Pueblo::mostrar()` sin exigir Admin ni Comité. No debe incluirse en un guard general del endpoint, porque la lectura sostiene registro público, selección de Pueblo y edición de perfiles legítimos.

Contratos que deben preservarse:

- petición `op=read`;
- respuesta JSON con un arreglo de filas;
- claves asociativas `id_pueblo` y `pueblo`;
- orden y contenido aportados actualmente por el modelo;
- ausencia de requisito de rol administrativo;
- compatibilidad con `ajaxListas()` y con `ajaxSelect()` cuando el caller declara las propiedades asociativas.

Las cuatro superficies afectadas son `admin/ver.estudiante.php`, `form-doc/info.estudiante.php`, `admin/ver.docente.php` y `form-doc/info.docente.php`. La corrección autorizada se limita a completar, en cada uno de los dos scripts de ficha y sólo en su llamada Pueblo, los argumentos:

```javascript
undefined,
'id_pueblo',
'pueblo'
```

No se autoriza modificar `js/funcAjax.js`, el formato de respuesta, el modelo ni la consulta. La implementación tampoco debe reinterpretar la sesión técnica de los formularios públicos como permiso administrativo.

## 8. CREATE

Flujo real a proteger:

```text
POST ajax/pueblo.php
op=insert-update
id=0 o equivalente actual de alta
→ Pueblo::insertar($nombre)
→ ejecutarEscritura(...)
```

La implementación deberá, dentro de la rama `case 'insert-update'` y antes de invocar `Pueblo::insertar()`:

1. asegurar que la sesión esté iniciada conforme al bootstrap existente;
2. evaluar `Authorization::hasAny(['admin', 'comite'])`;
3. permitir la operación si existe al menos una de esas claves, incluso en un rol acumulativo;
4. si falla la autorización, devolver HTTP 403 y JSON `NO_AUTORIZADO`;
5. terminar la rama sin llamar al modelo ni a ningún helper de escritura.

Resultados obligatorios:

| Actor | Resultado |
| --- | --- |
| Admin | CREATE permitido; comportamiento vigente preservado |
| Comité | CREATE permitido; comportamiento vigente preservado |
| Rol acumulativo con Admin o Comité | CREATE permitido |
| Sin sesión | HTTP 403 y sin escritura |
| Sesión sin Admin/Comité | HTTP 403 y sin escritura |

Los mensajes de éxito `Pueblo Registrado` y de error funcional `Pueblo no ha sido registrado` se conservan. Esta Task no normaliza el retorno exitoso ni el contrato de persistencia.

## 9. UPDATE

Flujo real a proteger:

```text
POST ajax/pueblo.php
op=insert-update
id != 0
→ Pueblo::editar($id_pueblo, $nombre)
→ ejecutarEscritura(...)
```

La implementación deberá aplicar la misma evaluación acumulativa de CREATE antes de invocar `Pueblo::editar()`. Esta Task conserva la semántica vigente `id != 0`; no autoriza ampliar el alcance con una validación nueva de IDs negativos.

Resultados obligatorios:

| Actor | Resultado |
| --- | --- |
| Admin | UPDATE permitido; comportamiento vigente preservado |
| Comité | UPDATE permitido; comportamiento vigente preservado |
| Rol acumulativo con Admin o Comité | UPDATE permitido |
| Sin sesión | HTTP 403 y sin escritura |
| Sesión sin Admin/Comité | HTTP 403 y sin escritura |

Los mensajes de éxito `Pueblo Editado` y de error funcional `Pueblo no ha sido editado` se conservan. El rechazo debe ocurrir antes del UPDATE y no puede quedar delegado al frontend ni al modelo.

## 10. DELETE

DELETE ya está protegido y validado. La futura implementación deberá inspeccionarlo y conservar:

- actores Admin o Comité;
- compatibilidad con roles acumulativos;
- inicio de sesión previo a la autorización;
- HTTP 403 y código `NO_AUTORIZADO` ante rechazo;
- validación de `id_pueblo` y HTTP 400 para `ID_INVALIDO`;
- transacción del modelo;
- comprobación de existencia;
- comprobación de dependencias en `usuario.pueblo`;
- bloqueo `TIENE_DEPENDENCIAS`;
- traducción de restricción SQLSTATE `23000`;
- rollback ante error;
- HTTP 500 para `ERROR_ELIMINACION`;
- mensajes funcionales existentes;
- recarga frontend sólo después de un resultado exitoso.

Se autoriza exclusivamente reemplazar el predicado directo sobre `$_SESSION['admin']` / `$_SESSION['comite']` por `Authorization::hasAny(['admin', 'comite'])` para mantener un evaluador común entre CREATE, UPDATE y DELETE. Esa regularización no autoriza reescribir el branch, mover su lógica al modelo ni alterar sus contratos.

## 11. Authorization

Mecanismo obligatorio reutilizable:

```php
Authorization::hasAny(['admin', 'comite'])
```

La revisión técnica deberá confirmar el `use App\Security\Authorization;` local en `ajax/pueblo.php`. No se modifica `src/Security/Authorization.php` porque `hasAny()` ya:

- devuelve verdadero cuando existe al menos una clave permitida;
- mantiene semántica OR;
- acepta roles acumulativos;
- utiliza las claves históricas actuales de sesión;
- no necesita capacidades nuevas.

Queda prohibido crear `pueblo.crear`, `pueblo.editar`, `pueblo.eliminar` o cualquier otra capacidad. También quedan prohibidos cambios en la derivación de capacidades, login o sesión.

## 12. Backend authority

`ajax/pueblo.php` será la única frontera de autorización efectiva de las mutaciones. La restricción de acceso a `admin/act.list.php`, el ocultamiento de controles y JavaScript no sustituyen el guard del endpoint.

Orden obligatorio por mutación:

```text
petición
→ sesión disponible
→ Authorization::hasAny(['admin', 'comite'])
→ rechazo 403 y fin, o autorización
→ validación funcional existente
→ llamada al modelo
→ respuesta vigente
```

El guard debe ser discriminado por operación: `op=read` queda fuera; `op=insert-update` y `op=delete` quedan dentro. Ningún rechazo puede alcanzar `Pueblo::insertar()`, `Pueblo::editar()` o `Pueblo::eliminar()`.

No se autoriza crear middleware, infraestructura global de errores ni cambios en otros endpoints.

## 13. HTTP 403

Toda mutación no autorizada deberá responder:

```text
HTTP: 403
Content-Type: application/json; charset=utf-8
ok: false
codigo: NO_AUTORIZADO
```

Mensajes por operación:

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para crear"}
```

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para editar"}
```

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para eliminar"}
```

La implementación podrá resolver el texto de CREATE/UPDATE a partir del valor normalizado de `id_pueblo`, pero deberá responder y terminar antes de la llamada al modelo. DELETE conservará exactamente su mensaje ya validado.

No se exige convertir las respuestas exitosas históricas de CREATE/UPDATE en objetos JSON; esa normalización está fuera de alcance.

## 14. Frontend

Archivo potencialmente modificable: `admin/scripts/listas.js`.

El cambio deberá limitarse al flujo `insertUpdate()` y al rechazo HTTP de Pueblo. El `.fail()` nuevo sólo debe actuar cuando `dato_lista.tipo == 'pueb'` y `xhr.status == 403`:

- conservar íntegramente el callback exitoso actual;
- no recargar la lista ni limpiar el formulario en un 403;
- leer `xhr.responseJSON.mensaje` cuando esté disponible;
- mostrar el rechazo mediante `mostrarMensajeCRUD({ contenedor: '#mnsj_row_listas', mensaje, tipo: 'danger' })`;
- utilizar un texto de respaldo comprensible si el payload esperado no está disponible;
- no interpretar el 403 como éxito;
- no modificar `eliminarLista()`, que ya maneja errores HTTP;
- no cambiar la interfaz ni reorganizar el archivo;
- no introducir comportamiento nuevo para otros catálogos.

Debido a que `insertUpdate()` es compartido, el diff deberá discriminar exactamente `dato_lista.tipo == 'pueb'` y `xhr.status == 403`, y conservar el comportamiento de Institución, Título, Financiamiento y Beca. En ese rechazo no debe recargar, limpiar ni escribir; para un payload incompleto debe usar un texto de respaldo comprensible. No se autoriza un handler global nuevo.

La vista `admin/act.list.php` ya limita la administración a Admin/Comité y carga `js/mensajesCrud.js`; no requiere cambio.

## 15. Modelo

`src/Model/Pueblo.php` queda protegido y no es modificable por esta Task.

El modelo ya contiene CREATE, READ, UPDATE y DELETE. La autorización pertenece al endpoint. Deben preservarse las firmas, los SQL, las comprobaciones DELETE, la transacción y los objetos/arreglos de retorno existentes.

Si la revisión técnica concluyera que debe modificarse el modelo para implementar la autorización, deberá detenerse: esa conclusión contradice la fuente aprobada y requiere un nuevo artefacto documental.

## 16. Persistencia

Persistencia modificable: **No**.

Se preservan sin cambios:

- `ejecutarEscritura()` para CREATE y UPDATE;
- `ejecutarConsultaResultados()` para READ;
- PDO preparado y transacción para DELETE;
- SQL existente;
- IDs y filas afectadas;
- retornos existentes;
- FK y regla RESTRICT;
- tablas, columnas, constraints, datos y migraciones.

La interpolación SQL y la interpretación booleana del arreglo de escritura son deuda observada por el AT, pero no bloquean este incremento y siguen bajo autoridad de EPIC-008. No deben corregirse por arrastre.

## 17. Consumers

Consumers READ directos que la implementación y la VF deberán cubrir:

| Superficie | Flujo | Requisito |
| --- | --- | --- |
| `admin/act.list.php` + `admin/scripts/listas.js` | Lista administrativa | Carga con Admin/Comité |
| `form-doc/estudiante.php` + `agrFormDatPers.js` | Registro público de estudiante | Selector carga sin rol administrativo |
| `form-doc/docente.php` + `agrFormDatPers.js` | Registro público de docente | Selector carga sin rol administrativo |
| `admin/ver.estudiante.php` + `ficha.estudiante.js` | Edición administrativa de estudiante | Defectuoso: corregir la única llamada Pueblo con propiedades explícitas |
| `form-doc/info.estudiante.php` + `ficha.estudiante.js` | Perfil estudiante | Defectuoso: corregir la misma llamada compartida y restaurar selección |
| `admin/ver.docente.php` + `ficha.docente.js` | Edición administrativa de docente | Defectuoso: corregir la única llamada Pueblo con propiedades explícitas |
| `form-doc/info.docente.php` + `ficha.docente.js` | Perfil docente | Defectuoso: corregir la misma llamada compartida y restaurar selección |

Consumers indirectos que dependen de la relación o etiqueta:

- `src/Model/Usuario.php` persiste `usuario.pueblo`;
- `src/Model/Estudiante.php` y `src/Model/Docente.php` unen Pueblo;
- scripts de fichas e información muestran `pueblo` o restauran `id_pueblo`.

Se autoriza modificar únicamente `form-doc/scripts/ficha.estudiante.js` y `form-doc/scripts/ficha.docente.js`, exclusivamente en la llamada Pueblo, para añadir `undefined`, `'id_pueblo'` y `'pueblo'`. Los otros consumers y cualquier otra llamada `ajaxSelect()` permanecen protegidos; su inclusión define pruebas de regresión, no alcance de edición.

## 18. Rutas alternativas

Ruta activa de escritura confirmada:

```text
ajax/pueblo.php → src/Model/Pueblo.php
```

No se identificaron endpoints alternativos activos para INSERT, UPDATE o DELETE. Las coincidencias en otros modelos están comentadas.

`archivos/ver.listas.php` contiene una lectura heredada sin callers versionados, depende de un archivo inexistente y usa una columna histórica incompatible. No es una ruta activa de mutación y queda fuera de alcance.

Criterio obligatorio: antes y después de implementar, repetir la búsqueda global de escrituras y callers. Si aparece una ruta activa de mutación no documentada, detener la implementación; no ampliar esta Task silenciosamente ni aplicar una política institucional no aprobada.

## 19. Mensajes

Mensajes exitosos y funcionales que deben preservarse:

| Caso | Mensaje/contrato |
| --- | --- |
| CREATE exitoso | `Pueblo Registrado` |
| CREATE con error funcional | `Pueblo no ha sido registrado` |
| UPDATE exitoso | `Pueblo Editado` |
| UPDATE con error funcional | `Pueblo no ha sido editado` |
| DELETE exitoso | `Registro eliminado correctamente` |
| DELETE inexistente | `El pueblo no existe` |
| DELETE con dependencias | `No se puede eliminar porque existen registros asociados` |
| DELETE error técnico | `No fue posible eliminar el registro` |

El único contrato nuevo autorizado es el rechazo HTTP 403 de CREATE/UPDATE con `NO_AUTORIZADO` y mensaje por operación. El frontend debe distinguirlo de éxito y de error funcional/persistencia. No se autoriza reformular globalmente los mensajes de Listas predefinidas.

## 20. Archivos futuros

La futura implementación podrá modificar exactamente estos cuatro archivos funcionales y ningún quinto:

| Archivo | Transformación autorizada |
| --- | --- |
| `ajax/pueblo.php` | Reutilizar `Authorization`, iniciar sesión para mutaciones, proteger CREATE/UPDATE, regularizar mínimamente el evaluador DELETE, conservar READ y emitir 403 JSON |
| `admin/scripts/listas.js` | Añadir el manejo mínimo del 403 sólo cuando `dato_lista.tipo == 'pueb'` y `xhr.status == 403` |
| `form-doc/scripts/ficha.estudiante.js` | Completar únicamente la llamada Pueblo con `undefined`, `'id_pueblo'`, `'pueblo'` |
| `form-doc/scripts/ficha.docente.js` | Completar únicamente la llamada Pueblo con `undefined`, `'id_pueblo'`, `'pueblo'` |

Archivos inspeccionables pero no modificables:

- `admin/act.list.php`;
- `js/funcAjax.js`;
- `js/mensajesCrud.js`;
- `src/Model/Pueblo.php`;
- `src/Security/Authorization.php`;
- bootstrap de aplicación y sesión;
- otros consumers;
- migraciones y esquema.

Cualquier necesidad real de editar un quinto archivo funcional obliga a detener la implementación y solicitar revisión documental.

## 21. VF integral

La VF será una sola validación integral del objeto y corresponde exclusivamente al usuario. Codex no debe ejecutarla ni simular escrituras reales sin una autorización posterior expresa.

### READ — siete superficies

1. `admin/act.list.php`: cargar el catálogo administrativo.
2. `form-doc/estudiante.php`: cargar el selector del registro público de estudiante.
3. `form-doc/docente.php`: cargar el selector del registro público de docente.
4. `admin/ver.estudiante.php`: cargar Pueblo y restaurar selección.
5. `form-doc/info.estudiante.php`: cargar Pueblo y restaurar selección.
6. `admin/ver.docente.php`: cargar Pueblo y restaurar selección.
7. `form-doc/info.docente.php`: cargar Pueblo y restaurar selección.

En las siete superficies se confirmarán `id_pueblo`, `pueblo` y la ausencia de errores PHP, AJAX y JavaScript.

### CREATE

| Caso | Resultado esperado |
| --- | --- |
| Admin | Permitido, persistente y visible tras recarga |
| Comité | Permitido, persistente y visible tras recarga |
| Rol acumulativo con Admin/Comité | Permitido |
| Sin sesión | HTTP 403, mensaje visible y sin escritura |
| Actor sin Admin/Comité | HTTP 403, mensaje visible y sin escritura |

### UPDATE

| Caso | Resultado esperado |
| --- | --- |
| Admin | Permitido, persistente y visible tras recarga |
| Comité | Permitido, persistente y visible tras recarga |
| Rol acumulativo con Admin/Comité | Permitido |
| Sin sesión | HTTP 403, mensaje visible y sin escritura |
| Actor sin Admin/Comité | HTTP 403, mensaje visible y sin escritura |

### DELETE

| Caso | Resultado esperado |
| --- | --- |
| Admin, Pueblo sin dependencias | Eliminación permitida |
| Comité, Pueblo sin dependencias | Eliminación permitida |
| Rol acumulativo con Admin/Comité | Permitido |
| Pueblo con dependencia | `TIENE_DEPENDENCIAS`, registro intacto |
| ID inválido | HTTP 400, `ID_INVALIDO` |
| Sin sesión | HTTP 403 y sin eliminación |
| Actor sin Admin/Comité | HTTP 403 y sin eliminación |

### Regresión y evidencia

- obtener estado antes y después de cada operación rechazada para demostrar que no hubo escritura;
- realizar una segunda recarga después de cada operación autorizada para confirmar persistencia;
- comprobar que un 403 de Pueblo no activa el callback exitoso, no recarga, no limpia y no escribe;
- comprobar que el mensaje usa `xhr.responseJSON.mensaje` o el fallback previsto;
- comprobar que DELETE conserva confirmación, dependencias y mensajes;
- registrar status HTTP, payload JSON y ausencia de errores PHP, AJAX y JavaScript.

Una prueba aislada de CREATE no valida ni cierra esta Task.

## 22. Criterios aceptación

### Funcionales y de seguridad

1. READ del endpoint permanece intacto y sin guard administrativo.
2. Los dos callers Pueblo usan `id_pueblo` y `pueblo` explícitamente.
3. Ningún otro `ajaxSelect()` cambia.
4. CREATE tiene guard backend Admin/Comité.
5. UPDATE tiene guard backend Admin/Comité.
6. CREATE/UPDATE no autorizados terminan antes del modelo.
7. DELETE conserva seguridad, dependencias, transacción y mensajes.
8. `Authorization::hasAny()` preserva la política acumulativa.
9. No se crean capacidades.
10. El frontend no interpreta 403 como éxito.
11. El 403 de Pueblo se maneja localmente.
12. El modelo permanece intacto.
13. La persistencia permanece intacta.
14. `js/funcAjax.js` permanece intacto.
15. Login y sesión global permanecen intactos.
16. EOL/EOF se preservan.
17. Se modifican como máximo los cuatro archivos autorizados.
18. La VF integral cubre siete superficies READ y C/U/D.
19. Staging permanece vacío durante la implementación.
20. No se crea commit ni se realiza push durante la implementación.

### Técnicos

La implementación deberá ejecutar y registrar:

```text
php -l ajax/pueblo.php
validación JavaScript estática o herramienta ya existente del proyecto
git diff --check
git diff -- ajax/pueblo.php admin/scripts/listas.js form-doc/scripts/ficha.estudiante.js form-doc/scripts/ficha.docente.js
git diff -U0 -- ajax/pueblo.php admin/scripts/listas.js form-doc/scripts/ficha.estudiante.js form-doc/scripts/ficha.docente.js
git status --short
git diff --cached --name-only
```

Además deberá comprobar que cada rechazo ocurre antes del modelo, que `op=read` no atraviesa el guard, que sólo cambian las transformaciones autorizadas y que no hay staging, commit ni push.

### Condiciones de detención

La futura implementación deberá detenerse sin cambios parciales si:

1. cualquiera de los cuatro SHA-256 baseline difiere;
2. aparece un quinto archivo funcional necesario;
3. existe otro consumer Pueblo defectuoso no documentado;
4. se requiere cambiar el contrato READ;
5. se requiere modificar `js/funcAjax.js`;
6. se requiere modificar modelo;
7. se requiere modificar persistencia;
8. se requiere capacidad nueva;
9. cambia la política Admin/Comité;
10. EOL/EOF no pueden preservarse;
11. el branch UPDATE deja de ser `id != 0`;
12. DELETE difiere de lo documentado;
13. aparece una ruta activa alternativa;
14. la Task deja de poder cerrar Pueblo integralmente.

Ante una condición de detención se deberá informar la operación, el bloqueo, la evidencia, la decisión necesaria y el siguiente artefacto. No se autoriza una implementación parcial.

## 23. EOL/EOF

Baseline obligatorio de los cuatro archivos funcionales modificables:

| Archivo | SHA-256 | Bytes | CRLF / LF aislado | BOM | EOF/final | Blob HEAD | Blob HEAD^ |
| --- | --- | ---: | ---: | --- | --- | --- | --- |
| `ajax/pueblo.php` | `25EF55E48A24EDB5B6CB7E376AF287B5BB7D7FA2BD6AEFA1B6307FED5AF90397` | 2030 | 26 / 22 | No | LF | `2a8936bbfba55dfd12c7a09ca98c4ca597e8c0a4` | igual |
| `admin/scripts/listas.js` | `6727E7A7923830F0AC9AB4722FE3D2470875A60A3CEAE0EFCB2BC60A44F3163C` | 5364 | 227 / 7 | No | CRLF | `3069e615708d87fb1c37413ef99c905b30823d11` | igual |
| `form-doc/scripts/ficha.estudiante.js` | `CC2E34AE0591B6B361CF984C70B386849D7CE444009994F6CFD24E4B0855230E` | 18049 | 443 / 0 | No | Sin newline final | `6ebf532b56b5832d197361dfc759c8acfd4eec1d` | igual |
| `form-doc/scripts/ficha.docente.js` | `73AFF46BB251E6057EAF59EDAE5F53C6652FACA5E38F74496513FC4B69C78355` | 18696 | 484 / 1 | No | CRLF | `a49e845c8dbd5a49aeb80638df38e1b027d54c12` | igual |

La implementación debe detenerse si cualquiera de los cuatro SHA-256 difiere antes de editar. `.gitattributes` declara `* text=auto`; quedan prohibidas la reescritura total y la normalización colateral. Se usarán parches localizados y se comprobarán conteos CRLF/LF, BOM, terminador final, diff normal y diff de cero contexto.

## 24. Reversión

Reversibilidad esperada: **Alta**.

| Archivo | Reversión exacta |
| --- | --- |
| `ajax/pueblo.php` | Retirar import y guards C/U; restaurar el predicado DELETE si fue regularizado |
| `admin/scripts/listas.js` | Retirar exclusivamente el `.fail()` limitado a Pueblo |
| `form-doc/scripts/ficha.estudiante.js` | Restaurar únicamente la llamada previa de `ajaxSelect()` Pueblo |
| `form-doc/scripts/ficha.docente.js` | Restaurar únicamente la llamada previa de `ajaxSelect()` Pueblo |

No se autoriza reset global. La reversión no requiere migración, rollback de BD, cambio de datos, capacidades ni sesión/login.

## 25. Riesgos

| Riesgo | Estado/impacto | Mitigación obligatoria |
| --- | --- | --- |
| R1 — bloquear READ | Alto | Guard sólo en branches mutantes y VF de siete superficies |
| R2 — CREATE abierto | Alto | Guard backend antes del modelo |
| R3 — UPDATE abierto | Alto | Guard backend antes del modelo |
| R4 — regresión DELETE | Alto | Cambiar sólo el predicado de autorización |
| R5 — 403 sin detener ejecución | Crítico | Fin del branch antes de llamar al modelo |
| R6 — frontend interpreta 403 como éxito | Alto | `.fail()` local sin recarga/limpieza |
| R7 — roles acumulativos rechazados | Alto | `hasAny()` y VF acumulativa |
| R8 — capacidad nueva innecesaria | Medio | Prohibición expresa |
| R9 — ruta alternativa omitida | Alto | Búsqueda global antes de implementar |
| R10 — persistencia modificada | Alto | Modelo/SQL/helpers fuera de alcance |
| R11 — EOL/EOF | Medio | Baseline completo y edición localizada |
| R12 — consumers incompatibles con `FETCH_ASSOC` | Bloqueante resuelto documentalmente | Adaptación explícita de dos callers |
| R13 — modificar otros `ajaxSelect()` | Alto | Identificar la única llamada Pueblo por script y revisar diff |
| R14 — normalización de scripts mixtos | Alto | Edición localizada y comparación EOL/EOF |

Riesgo global previo a implementación: **Alto**. La consolidación mínima resuelve el bloqueo documental, pero la seguridad C/U y la reparación READ sólo se cerrarán con implementación y VF.

## 26. Fuera de alcance

Quedan excluidos expresamente:

- otros catálogos: Título, Institución, Beca, Financiamiento, Grado y Cursos;
- cambios de identidad, estados o permiso 3;
- capacidades nuevas o cambios en su derivación;
- cambios de framework, sesión, login o `Authorization.php`;
- cambios en `src/Model/Pueblo.php`, persistencia, esquema, datos o migraciones;
- refactor general de Listas predefinidas o normalización global de JSON;
- corrección de interpolación SQL o rediseño de interfaz;
- modificación de consumers distinta de las dos llamadas Pueblo exactas autorizadas;
- modificación de `js/funcAjax.js`;
- ejecución de VF por Codex.

## 27. Estado finalizable

```text
Task previa finalizable: No
Bloqueador documental READ: Resuelto por la consolidación mínima y esta actualización
Operaciones bloqueadas tras consolidación: Ninguna conocida
Objeto finalizable tras consolidación + Task corregida + implementación + VF: Sí
Estado actual: No implementado; pendiente de una única revisión técnica final
```

Si implementación y VF cumplen todos los criterios, READ, CREATE, UPDATE, DELETE, autorización y consumers podrán cerrarse integralmente bajo EPIC-001. La publicación documental posterior formalizará el cierre.

## 28. Trazabilidad

```text
EPIC-001
→ INSPECCIÓN-EPIC001-REACTIVACION-INTEGRAL-001
→ AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001
→ Consolidación mínima posterior a revisión técnica
→ TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001 consolidada
→ Nueva revisión técnica final
→ Implementación integral
→ VF integral por el usuario
→ Integración, publicación y cierre del objeto Pueblo
```

Marco transversal: **EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral**. EPIC-003 conserva autoridad sobre reglas institucionales y EPIC-008 sobre persistencia; ninguna requiere cambio para esta Task.

## 29. Auditoría

```text
Task por objeto: Sí
CRUD integral: Sí
Micro-Tasks por operación: No
READ incluido: Sí, corregido documentalmente
READ implementado: No
C/U/D protegidos: Sí como objetivo; no implementados en este acto
Consolidación mínima incorporada: Sí
Addendum creado: No
Task dividida: No
EPIC-003 modificado: No
EPIC-008 modificado: No
Código modificado: No
AT original modificado: No
VF ejecutada: No
Staging: Vacío
Commit creado: No
Push realizado: No
```

La consolidación documental no repara aún los callers ni implementa los guards. La transición a implementación exige una única revisión técnica final y una autorización posterior específica.

## 30. Dictamen

**A. Task integral consolidada y lista para una única revisión técnica final.**

```text
Task: TASK-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001
Objeto: Pueblo indígena
Estado: Consolidada; pendiente de una única revisión técnica final previa a implementación
AT original SHA-256: E608345484B52924C7113AB114B7AD1B289461012EDFE2EC1609C780A04B83B7
Consolidación mínima SHA-256: E65996BBD6B2D9927FC59F06DCD3AE32B06C45142F14BE9B317F57F3948B0E11
Task previa SHA-256: 51BF07F07F8496EC9A1A607127470ADE283CEE4E8873AC3663816D11BBB16A5C
READ actual: Endpoint/contrato correctos; dos callers de ficha defectuosos
READ objetivo: Dos callers corregidos con id_pueblo y pueblo explícitos
CREATE objetivo: Protegido en backend para Admin OR Comité
UPDATE objetivo: Protegido en backend para Admin OR Comité con id != 0
DELETE objetivo: Preservado sin regresión para Admin OR Comité
Authorization reutilizable: Authorization::hasAny(['admin', 'comite'])
Capacidad nueva necesaria: No
Archivos funcionales máximos: 4
ajax/pueblo.php modificable: Sí
admin/scripts/listas.js modificable: Sí
form-doc/scripts/ficha.estudiante.js modificable: Sí, sólo llamada Pueblo
form-doc/scripts/ficha.docente.js modificable: Sí, sólo llamada Pueblo
js/funcAjax.js modificable: No
Modelo/persistencia/BD modificables: No
Login/sesión global modificables: No
403 incluido: Sí, local para Pueblo C/U y preservado para DELETE
VF integral: Siete superficies READ y C/U/D, exclusivamente por el usuario
Task divide CRUD: No
Objeto finalizable tras consolidación + Task corregida + implementación + VF: Sí
Código modificado: No
AT original modificado: No
Staging: Vacío
Commit creado: No
Push realizado: No
```
