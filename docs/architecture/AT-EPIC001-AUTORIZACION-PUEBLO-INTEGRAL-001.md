# AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001

# Resultado — AT autorización integral Pueblo

**Análisis integral de autorización del objeto Pueblo indígena**

## Identificación y estado

- **AT:** AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.
- **EPIC funcional:** EPIC-001 — Autorización y permisos.
- **Marco metodológico:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Objeto:** Pueblo indígena.
- **Módulo:** Catálogos / Listas predefinidas.
- **Clasificación:** [EPIC-001] [EPIC-009] [AUTH] [CRUD] [SEC] [VF] [REV] [GOV].
- **Estado:** Analizado — Task integral autorizable.
- **Tipo de ejecución:** análisis estático; no se ejecutaron operaciones HTTP, escrituras, VF ni cambios de datos.

Este AT toma como antecedente la selección realizada por `INSPECCIÓN-EPIC001-REACTIVACION-INTEGRAL-001`, que identificó Pueblo indígena como primer objeto viable. El propósito es determinar si READ, CREATE, UPDATE y DELETE pueden cerrarse mediante una única Task integral de autorización, sin redefinir reglas institucionales ni reabrir innecesariamente decisiones de persistencia.

## 1. Estado Git

La inspección comenzó y terminó sobre el siguiente baseline:

```text
Rama: refactor/fase-0-seguridad
HEAD: a3c75e5b03abb9ba3aa280ea05fd49900cacff82
Worktree versionable: limpio antes de crear este AT
Staging: vacío
```

Últimos commits observados al inicio:

```text
a3c75e5 docs(roadmap): introduce integral object CRUD epic
955f515 chore(project): checkpoint state before next epic
4dc3488 fix(beca): correct scholarship edit identifier
6fe1ca4 refactor(persistence): migrate scholarship list insert contract
ee1194f fix(frontend): adapt scholarship lists to associative response
```

No se detectaron cambios previos que obligaran a detener la ejecución.

## 2. Fuentes

### 3.1 Archivos obligatorios leídos íntegramente

- `admin/act.list.php`.
- `admin/scripts/listas.js`.
- `js/funcAjax.js`.
- `ajax/pueblo.php`.
- `src/Model/Pueblo.php`.
- `src/Security/Authorization.php`.

### 3.2 Fuentes complementarias

- `form-doc/agr.form.dat.pers.php`.
- `form-doc/datos.pers.php`.
- `form-doc/estudiante.php`.
- `form-doc/docente.php`.
- `form-doc/info.estudiante.php`.
- `form-doc/info.docente.php`.
- `form-doc/ficha.estudiante.php`.
- `form-doc/ficha.docente.php`.
- `form-doc/footer.php`.
- `form-doc/scripts/agrFormDatPers.js`.
- `form-doc/scripts/usuario.js`.
- `form-doc/scripts/ficha.estudiante.js`.
- `form-doc/scripts/ficha.docente.js`.
- `admin/ver.estudiante.php`.
- `admin/ver.docente.php`.
- `admin/scripts/ver.estudiante.js`.
- `admin/scripts/ver.docente.js`.
- `src/Model/Usuario.php`.
- `src/Model/Estudiante.php`.
- `src/Model/Docente.php`.
- `src/Config/conexion.php`.
- `src/bootstrap/app.php`.
- `src/bootstrap/session.php`.
- `ajax/validaciones.php`.
- `migrations/TASK-DB-MIGRATION-DELETE-RESTRICT-001.sql`.
- `c1441353_antr_db.sql`.
- `docs/TASKS.md`.
- `archivos/ver.listas.php`.
- Historia Git de los archivos y commits asociados.

## 3. Inventario del objeto

### 4.1 Vista administrativa

`admin/act.list.php` contiene la administración de listas predefinidas. La vista:

- inicia la sesión bajo el bootstrap central;
- permite acceso solamente cuando existe `$_SESSION['admin']` o `$_SESSION['comite']`;
- redirige a `../index.php` a los demás actores;
- ofrece el botón `#btn_pueblos`;
- muestra un formulario único para alta y edición;
- carga botones de edición y eliminación generados por `ajaxListas()`.

Por construcción actual, Admin y Comité ven conjuntamente CREATE, UPDATE y DELETE. No existe diferenciación visual entre ambos roles para Pueblo.

### 4.2 Capas

| Capa | Archivo | Responsabilidad |
| --- | --- | --- |
| Vista | `admin/act.list.php` | Restringe la pantalla a Admin/Comité y contiene formulario/lista. |
| Control UI | `admin/scripts/listas.js` | Selecciona Pueblo, arma CREATE/UPDATE y dispara DELETE. |
| Helpers AJAX | `js/funcAjax.js` | Carga lista/selects, genera botones y ejecuta DELETE. |
| Endpoint | `ajax/pueblo.php` | Despacha READ, CREATE, UPDATE y DELETE según `op` e `id`. |
| Modelo | `src/Model/Pueblo.php` | Implementa los cuatro contratos CRUD. |
| Autorización | `src/Security/Authorization.php` | Permite evaluar acumulativamente claves históricas de sesión. |
| Persistencia | `src/Config/conexion.php` y PDO | Ejecuta lecturas/escrituras y DELETE transaccional. |
| Integridad | migración RESTRICT y modelo | Impide eliminar Pueblo asociado a `usuario`. |

## 4. Consumers READ

READ no es una operación exclusivamente administrativa. El flujo público de registro enlaza desde `form-doc/login.php` hacia `form-doc/estudiante.php` o `form-doc/docente.php`; esas páginas inician sesión técnica, pero no exigen autenticación ni rol. Ambas cargan `footer.php`, `agrFormDatPers.js` y el selector de Pueblo.

Consumers activos identificados:

| Archivo/superficie | Propósito | Operación | Sesión/rol de la superficie | Dependencia del retorno |
| --- | --- | --- | --- | --- |
| `admin/act.list.php` + `admin/scripts/listas.js` | Administración del catálogo | `op=read` | Admin o Comité | `id_pueblo`, `pueblo`; genera edición/eliminación. |
| `form-doc/estudiante.php` | Registro público de postulante | `op=read` mediante `agrFormDatPers.js` | Sin rol requerido | `id_pueblo`, `pueblo`; llena `#select_pueb`. |
| `form-doc/docente.php` | Registro público de docente | `op=read` mediante `agrFormDatPers.js` | Sin rol requerido | `id_pueblo`, `pueblo`; llena `#select_pueb`. |
| `admin/ver.estudiante.php` + `ficha.estudiante.js` | Edición de estudiante por Admin/Comité | `op=read` | Admin o Comité | `id_pueblo`, `pueblo`; restaura selección. |
| `form-doc/info.estudiante.php` + `ficha.estudiante.js` | Edición de perfil estudiante | `op=read` | `perfil.ver`, Admin o Comité | Mismo contrato asociativo. |
| `admin/ver.docente.php` + `ficha.docente.js` | Edición de docente por Admin/Comité | `op=read` | Admin o Comité | Mismo contrato asociativo. |
| `form-doc/info.docente.php` + `ficha.docente.js` | Edición de perfil docente | `op=read` | Docente, Admin o Comité | Mismo contrato asociativo. |

Consumers indirectos:

- `src/Model/Estudiante.php` y `src/Model/Docente.php` unen `pueblo` para mostrar su etiqueta en fichas y listados.
- `src/Model/Usuario.php` persiste la referencia seleccionada al crear o editar una identidad.
- `admin/scripts/ver.estudiante.js`, `admin/scripts/ver.docente.js`, `form-doc/scripts/info.estudiante.js` y `form-doc/scripts/info.docente.js` muestran la etiqueta obtenida por esos modelos; no llaman directamente a `ajax/pueblo.php`.

Conclusión READ: debe conservarse sin guard administrativo. Restringirlo a Admin/Comité rompería el registro público y perfiles legítimos. Preservar el acceso actual no introduce una regla nueva; evita una reducción de acceso observable.

## 5. Matriz CRUD actual

| Operación | Estado EPIC-009 | Actores observados | Endpoint/modelo | Guard backend | Retorno |
| --- | --- | --- | --- | --- | --- |
| READ | Existe | Público, usuarios autenticados, Admin, Comité | `op=read` → `Pueblo::mostrar()` | No; correcto para consumers actuales | JSON con filas del catálogo. |
| CREATE | Defectuoso | UI: Admin/Comité. Backend: cualquiera | `op=insert-update`, `id=0` → `Pueblo::insertar()` | Falta | JSON string de éxito/error. |
| UPDATE | Defectuoso | UI: Admin/Comité. Backend: cualquiera | `op=insert-update`, `id>0` → `Pueblo::editar()` | Falta | JSON string de éxito/error. |
| DELETE | Existe | Admin/Comité | `op=delete` → `Pueblo::eliminar()` | Directo sobre `$_SESSION`; validado | JSON objeto y HTTP 403/400/500 cuando corresponde. |

No falta ninguna operación CRUD en endpoint o modelo. El defecto integral es de autorización backend en CREATE/UPDATE y de consistencia del mecanismo entre las tres mutaciones.

## 6. CREATE

### 7.1 Flujo actual

```text
admin/act.list.php
→ #btn_pueblos
→ clickListas('pueb')
→ formulario #form_lista
→ id oculto vacío/0
→ payload {nombre, id, op:'insert-update', tipo:'pueb'}
→ POST ajax/pueblo.php
→ cast de id a int = 0
→ Pueblo::insertar($nombre)
→ ejecutarEscritura($sql)
→ JSON string
→ callback insertUpdate()
→ cargarListas('pueb')
```

### 7.2 Estado de autorización

- La UI está contenida en una página exclusiva de Admin/Comité.
- `ajax/pueblo.php` no inicia sesión ni consulta autorización antes de CREATE.
- `src/bootstrap/app.php` aplica la política de sesión, pero no inicia una sesión ni autoriza la operación.
- Sin sesión, con `aceptado`, `docente`, `estudiante` o cualquier rol sin Admin/Comité, una invocación directa alcanza actualmente `Pueblo::insertar()`.

**Resultado:** guard backend ausente; CREATE defectuoso.

### 7.3 Contrato actual

- Entrada: `nombre`, `id`, `op=insert-update`; `tipo` es enviado por el frontend, pero ignorado por el endpoint.
- `nombre` pasa por `limpiar_datos()`.
- SQL: `INSERT INTO pueblo (id_pueblo,pueblo) VALUES (NULL,'$nombre')`.
- Helper: `ejecutarEscritura()`.
- Retorno del modelo: arreglo con `filasAfectadas` e `idInsertado`.
- Mensajes endpoint: `Pueblo Registrado` o `Pueblo no ha sido registrado` codificados como JSON string.

El frontend actual sólo registra la respuesta en consola y recarga la lista; no presenta un error HTTP de CREATE al usuario.

## 7. READ

### 8.1 Flujo actual

```text
consumer
→ ajaxListas() o ajaxSelect()
→ POST {op:'read', tipo:<opcional>}
→ ajax/pueblo.php
→ Pueblo::mostrar()
→ ejecutarConsultaResultados()
→ JSON de filas ordenadas por pueblo
```

Parámetros efectivos:

- `op=read` es obligatorio para seleccionar el branch.
- `tipo` puede ser enviado por helpers genéricos, pero Pueblo no lo usa.
- No requiere ID.

Contrato consumido: arreglo JSON con claves asociativas `id_pueblo` y `pueblo`. Los helpers actuales fueron adaptados explícitamente a esas claves en `99b36c1` y `0c23b3c`.

### 8.2 Decisión

READ corresponde al caso **A: debe permanecer público/sin autorización administrativa** para registro y selección. No se identificó una segunda ruta activa que requiera política distinta. Iniciar una sesión técnica no debe convertirse en precondición de rol.

**Resultado:** READ completo para el objetivo de autorización; no requiere cambios.

## 8. UPDATE

### 9.1 Flujo actual

```text
fila generada por ajaxListas()
→ botón .editar_lista con id=id_pueblo y name=pueblo
→ editarLista(id, nombre)
→ #oculto recibe id
→ submit #form_lista
→ payload {nombre, id, op:'insert-update', tipo:'pueb'}
→ POST ajax/pueblo.php
→ limpiar_datos(id) + cast int
→ id > 0
→ Pueblo::editar($id, $nombre)
→ ejecutarEscritura($sql)
→ JSON string
→ recarga de lista
```

El ID proviene de `id_pueblo` devuelto por READ. El nombre de parámetro es `id`; el endpoint lo normaliza como texto y luego lo convierte a entero. Un valor vacío, no numérico o equivalente a cero selecciona CREATE, porque CREATE/UPDATE comparten `op=insert-update`.

### 9.2 Estado de autorización

No existe guard antes de `Pueblo::editar()`. Sin sesión o con cualquier sesión no administrativa, una invocación directa puede modificar la tabla.

**Resultado:** guard backend ausente; UPDATE defectuoso.

### 9.3 Contrato actual

- SQL: `UPDATE pueblo SET pueblo='$nombre' where id_pueblo='$id'`.
- Helper: `ejecutarEscritura()`.
- Retorno del modelo: arreglo de escritura.
- Mensajes: `Pueblo Editado` o `Pueblo no ha sido editado` como JSON string.

La interpretación booleana del arreglo devuelto y la interpolación SQL son deuda histórica de persistencia/contrato. No son necesarias para añadir autorización y no deben ampliarse automáticamente dentro de EPIC-001; quedan bajo autoridad de EPIC-008 si se decide revisarlas.

## 9. DELETE

### 10.1 Flujo actual

```text
botón .eliminar_lista
→ eliminarLista(id, 'pueb', descripción)
→ confirmación Bootstrap
→ POST {id, op:'delete', tipo:'pueb'}
→ inicio de sesión si es necesario
→ guard Admin/Comité
→ validación id > 0
→ Pueblo::eliminar($id)
→ transacción + existencia + dependencias + DELETE
→ JSON objeto
→ mensaje CRUD y recarga si ok=true
```

### 10.2 Guard y rechazo

El guard vigente es:

```php
if (!isset($_SESSION['admin']) && !isset($_SESSION['comite']))
```

Ante rechazo:

```text
HTTP: 403
Content-Type: application/json; charset=utf-8
Payload:
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para eliminar"}
```

Un rol acumulativo que incluya Admin o Comité queda autorizado, aunque también posea otras claves.

### 10.3 Integridad y resultados

`Pueblo::eliminar()`:

1. abre una transacción;
2. bloquea/busca el registro mediante `SELECT ... FOR UPDATE`;
3. devuelve `NO_ENCONTRADO` si no existe;
4. busca dependencia en `usuario.pueblo`;
5. devuelve `TIENE_DEPENDENCIAS` y revierte si existe una referencia;
6. elimina y confirma si no hay dependencia;
7. traduce SQLSTATE `23000` a `TIENE_DEPENDENCIAS`;
8. revierte y devuelve `ERROR_ELIMINACION` ante otro error PDO.

Estados HTTP adicionales:

- 400 para `ID_INVALIDO`.
- 500 para `ERROR_ELIMINACION`.
- El resto de resultados funcionales conserva el status normal y comunica el resultado mediante el objeto JSON.

### 10.4 Estado

DELETE fue implementado en `8e36912`, documentado en `1290272` y figura cerrado con validación técnica y VF aprobadas en `TASK-PUEBLO-DELETE-SECURE-001`.

**Resultado:** DELETE completo y reutilizable. El comportamiento transaccional, los códigos y la integridad no deben alterarse en la futura Task.

## 10. Política Admin/Comité

La política observable está suficientemente sustentada:

| Evidencia | Conclusión |
| --- | --- |
| `admin/act.list.php` admite exclusivamente Admin o Comité. | Ambos actores pueden abrir la administración y ver el formulario. |
| El formulario y los botones generados no distinguen entre Admin y Comité. | Ambos ven CREATE, UPDATE y DELETE. |
| `TASK-PUEBLO-DELETE-SECURE-001` declara y valida DELETE para Admin/Comité. | La regla de administración está formalmente confirmada para la mutación más sensible. |
| No existe otro frontend activo de mantenimiento de Pueblo. | No se observa política alternativa. |

Política a preservar:

```text
READ: consumers públicos y autenticados actuales.
CREATE: Admin OR Comité.
UPDATE: Admin OR Comité.
DELETE: Admin OR Comité.
```

No se requieren capacidades nuevas, cambios de login ni reinterpretación de roles. No se identifica bloqueo de EPIC-003.

### Usuarios no autorizados

Comportamiento requerido para las tres mutaciones:

| Contexto | CREATE | UPDATE | DELETE |
| --- | --- | --- | --- |
| Sin sesión | 403, sin escritura | 403, sin escritura | 403, ya implementado |
| `aceptado` sin Admin/Comité | 403 | 403 | 403 |
| `docente` sin Admin/Comité | 403 | 403 | 403 |
| `estudiante`/`perfil.ver` sin Admin/Comité | 403 | 403 | 403 |
| Otra sesión sin rol administrativo | 403 | 403 | 403 |
| Rol acumulativo con `admin` | Permitido | Permitido | Permitido |
| Rol acumulativo con `comite` | Permitido | Permitido | Permitido |

La evaluación debe ser acumulativa: la existencia de otra clave no anula Admin o Comité.

## 11. Authorization.php

`Authorization::hasAny(array $permissions)` comprueba si existe al menos una de las claves solicitadas en `$_SESSION`. Su contrato expresa exactamente la política necesaria:

```php
Authorization::hasAny(['admin', 'comite'])
```

Comparación:

| Criterio | `hasAny()` | Guard directo DELETE |
| --- | --- | --- |
| Resultado funcional | Equivalente | Equivalente |
| Roles acumulativos | Preservados | Preservados |
| Compatibilidad histórica | Sí | Sí |
| Duplicación | Menor | Repite acceso directo a sesión |
| Capacidad nueva | No | No |

Recomendación: la futura Task debe usar `Authorization::hasAny(['admin', 'comite'])` para CREATE, UPDATE y DELETE, preservando los resultados observables del DELETE. No se justifica crear métodos o capacidades ni modificar `Authorization.php`.

La sesión debe iniciarse antes de consultar `Authorization`. READ no debe ser sometido a este guard.

## 12. Backend authority

La frontera efectiva será `ajax/pueblo.php`. La protección de `admin/act.list.php` sólo gobierna la navegación y no impide un POST directo.

La futura Task debe garantizar esta secuencia para cada mutación:

```text
request
→ sesión disponible
→ Authorization::hasAny(['admin','comite'])
→ si falla: HTTP 403 + JSON + fin del branch
→ si pasa: validación/operación existente
```

El guard debe ejecutarse antes de cualquier llamada a `Pueblo::insertar()`, `Pueblo::editar()` o `Pueblo::eliminar()`. Un rechazo no puede alcanzar persistencia.

## 13. Respuesta 403

Se debe conservar el patrón local de DELETE:

- HTTP 403.
- `Content-Type: application/json; charset=utf-8`.
- objeto con `ok`, `codigo` y `mensaje`.
- `codigo=NO_AUTORIZADO`.

Payloads recomendados, sin crear un framework general:

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para crear"}
```

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para editar"}
```

DELETE debe conservar su payload ya validado:

```json
{"ok":false,"codigo":"NO_AUTORIZADO","mensaje":"Usuario sin permisos para eliminar"}
```

La estructura es común y la redacción distingue la operación. Los mensajes de éxito existentes pueden conservarse para minimizar el alcance.

## 14. Frontend

### 16.1 Paridad visual

- Sólo Admin/Comité puede cargar `admin/act.list.php`.
- Ambos actores ven el formulario de alta.
- Los botones Editar y Eliminar se generan genéricamente en `ajaxListas()`.
- CREATE y UPDATE comparten `insertUpdate()`.
- DELETE usa `eliminarLista()` y ya maneja respuestas HTTP de error.

No se requiere ocultar nuevos controles ni cambiar la vista.

### 16.2 Manejo de 403

DELETE ya posee callback `error` y presenta `responseJSON.mensaje` mediante `mostrarMensajeCRUD()`.

CREATE/UPDATE usan `$.post(..., success)` sin callback de error. Cuando el backend responda 403, el frontend no recargará —comportamiento seguro—, pero tampoco mostrará el motivo. La futura Task necesita un cambio mínimo en `admin/scripts/listas.js` para manejar el rechazo de `insertUpdate()` y mostrar el mensaje en `#mnsj_row_listas` mediante el helper ya cargado.

Ese ajuste debe preservar el callback exitoso y evitar cambios conductuales en otros catálogos que usan el helper compartido. No se requiere modificar `admin/act.list.php` ni `js/funcAjax.js` según la evidencia actual.

## 15. Modelo

El CRUD del modelo está completo:

| Operación | Firma | SQL/estrategia | Helper/retorno | Caller activo |
| --- | --- | --- | --- | --- |
| CREATE | `insertar($nombre)` | `INSERT INTO pueblo ...` | `ejecutarEscritura()` → arreglo de escritura | `ajax/pueblo.php` |
| READ | `mostrar()` | `SELECT * FROM pueblo ORDER BY pueblo` | `ejecutarConsultaResultados()` → filas | `ajax/pueblo.php` |
| UPDATE | `editar($id,$nombre)` | `UPDATE pueblo ... WHERE id_pueblo=...` | `ejecutarEscritura()` → arreglo de escritura | `ajax/pueblo.php` |
| DELETE | `eliminar($id)` | PDO preparado, transacción y prevalidación | objeto de resultado funcional | `ajax/pueblo.php` |

No falta una operación funcional necesaria. La autorización pertenece al endpoint y no debe trasladarse al modelo. La futura Task no necesita modificar `src/Model/Pueblo.php`.

## 16. Persistencia

Contratos confirmados:

- CREATE usa `ejecutarEscritura(...)`, migrado en `9ca2dda`.
- UPDATE usa `ejecutarEscritura(...)`, migrado en `8f2b965`.
- READ usa el contrato histórico `ejecutarConsultaResultados(...)`.
- DELETE usa PDO preparado, transacción, validación explícita y traducción de dependencia, implementado en `8e36912`.
- La migración `TASK-DB-MIGRATION-DELETE-RESTRICT-001` cambió `usuario.pueblo → pueblo.id_pueblo` a `ON DELETE RESTRICT` y figura cerrada.

El dump histórico todavía muestra la definición previa `ON DELETE CASCADE`; la migración versionada y el precheck del modelo son las fuentes del comportamiento evolucionado. Esta divergencia histórica no exige cambiar esquema para añadir autorización.

Deuda observada no necesaria para este incremento:

- CREATE/UPDATE interpolan valores en SQL aunque usan el helper explícito de escritura.
- El endpoint interpreta como booleano el arreglo retornado por `ejecutarEscritura()`.
- CREATE/UPDATE no tienen un contrato JSON objeto equivalente a DELETE.

Estas observaciones no bloquean la protección backend y permanecen bajo autoridad de EPIC-008. No se autoriza modificarlas por arrastre.

**Persistencia requerida en la futura Task:** sin cambios.

## 17. Callers/consumers

### 19.1 Callers CRUD directos

| Caller | Operación |
| --- | --- |
| `admin/scripts/listas.js::insertUpdate()` | CREATE/UPDATE mediante `ajax/pueblo.php`. |
| `js/funcAjax.js::cargarListas()` | READ administrativo. |
| `js/funcAjax.js::ajaxSelect()` vía `agrFormDatPers.js` | READ para registro. |
| `form-doc/scripts/ficha.estudiante.js` | READ para edición de estudiante. |
| `form-doc/scripts/ficha.docente.js` | READ para edición de docente. |
| `js/funcAjax.js::eliminarLista()` | DELETE administrativo. |

### 19.2 Consumers de relación/etiqueta

- `src/Model/Usuario.php`: guarda/modifica `usuario.pueblo`.
- `src/Model/Estudiante.php` y `src/Model/Docente.php`: unen la tabla para devolver la etiqueta.
- Scripts de fichas e información: muestran `pueblo` o restauran `id_pueblo`.

Todos dependen de que READ conserve el formato asociativo y de que DELETE mantenga la protección referencial.

## 18. Rutas alternativas

La búsqueda de `INSERT INTO pueblo`, `UPDATE pueblo` y `DELETE FROM pueblo` confirmó una única ruta activa de escritura:

```text
ajax/pueblo.php → src/Model/Pueblo.php
```

Las coincidencias en otros modelos son código comentado y no representan rutas ejecutables.

`archivos/ver.listas.php` contiene una lectura directa histórica. No tiene callers versionados, requiere un `../functions.php` inexistente en el repositorio actual y ordena por una columna `nombre` que no coincide con el modelo vigente (`pueblo`). Se clasifica como superficie heredada/abandonada, no como endpoint activo ni ruta alternativa de mutación. No forma parte de la futura Task.

No se identificó endpoint alternativo activo capaz de crear, editar o eliminar Pueblo.

## 19. Dependencias

La dependencia persistente directa confirmada es:

```text
usuario.pueblo → pueblo.id_pueblo
```

Estudiante y Profesor dependen indirectamente a través de `usuario`; no tienen una FK directa a Pueblo.

Comportamiento DELETE:

- Pueblo con al menos un `usuario` asociado: `TIENE_DEPENDENCIAS`, rollback y sin eliminación.
- Pueblo sin dependencias: eliminación y commit.
- Restricción SQLSTATE `23000`: traducida a `TIENE_DEPENDENCIAS`.

No deben cambiarse las reglas referenciales ni las protecciones existentes.

## 20. Mensajes

| Caso | Mensaje/contrato actual |
| --- | --- |
| CREATE éxito | JSON string `Pueblo Registrado`. |
| CREATE error | JSON string `Pueblo no ha sido registrado`. |
| UPDATE éxito | JSON string `Pueblo Editado`. |
| UPDATE error | JSON string `Pueblo no ha sido editado`. |
| DELETE éxito | `{ok:true,codigo:ELIMINADO,mensaje:Registro eliminado correctamente}`. |
| DELETE inexistente | `{ok:false,codigo:NO_ENCONTRADO,mensaje:El pueblo no existe}`. |
| DELETE con dependencia | `{ok:false,codigo:TIENE_DEPENDENCIAS,mensaje:No se puede eliminar porque existen registros asociados}`. |
| DELETE error técnico | `{ok:false,codigo:ERROR_ELIMINACION,mensaje:No fue posible eliminar el registro}` + HTTP 500. |
| DELETE sin autorización | `{ok:false,codigo:NO_AUTORIZADO,mensaje:Usuario sin permisos para eliminar}` + HTTP 403. |

Para la VF integral, CREATE/UPDATE deben añadir un 403 estructurado y el frontend debe mostrarlo. No es necesario normalizar todos los mensajes exitosos ni rediseñar el contrato global del proyecto.

## 21. CRUD faltante

| Operación | Existencia técnica | Autorización | Resultado integral actual |
| --- | --- | --- | --- |
| READ | Completa | Acceso amplio necesario | Completo |
| CREATE | Completa | Falta backend | Defectuoso |
| UPDATE | Completa | Falta backend | Defectuoso |
| DELETE | Completa | Backend Admin/Comité | Completo |

No hay operaciones `No aplica` ni bloqueadas. Pueblo no puede declararse finalizado antes de corregir CREATE/UPDATE y validar las cuatro operaciones en conjunto.

## 22. Alcance Task integral

La futura Task debe ser única para el objeto y contener exactamente:

### `ajax/pueblo.php`

- importar/reutilizar `App\Security\Authorization`;
- disponer de sesión antes de evaluar una mutación;
- proteger CREATE con `Authorization::hasAny(['admin', 'comite'])`;
- proteger UPDATE con la misma regla;
- regularizar el guard DELETE para usar el mismo evaluador sin alterar su autorización, códigos, validaciones, transacción ni mensajes funcionales;
- devolver HTTP 403 y objeto JSON coherente antes de tocar persistencia;
- dejar READ fuera del guard administrativo.

### `admin/scripts/listas.js`

- añadir manejo mínimo del error HTTP de `insertUpdate()`;
- mostrar `responseJSON.mensaje` mediante `mostrarMensajeCRUD()`;
- preservar la recarga y el comportamiento exitoso actual;
- no alterar los demás catálogos salvo la compatibilidad inevitable del helper compartido.

### Sin cambios previstos

- `admin/act.list.php`: la paridad visual ya es correcta.
- `js/funcAjax.js`: DELETE ya maneja 403.
- `src/Model/Pueblo.php`: CRUD completo; autorización no corresponde al modelo.
- `src/Security/Authorization.php`: `hasAny()` es suficiente.
- sesión/login/capacidades: sin cambios.
- base de datos/migraciones: sin cambios.

La Task no debe dividir CREATE, UPDATE y DELETE en unidades independientes.

## 23. VF integral

La VF será única y ejecutada exclusivamente por el usuario.

### 25.1 READ

- Desde registro público de estudiante, seleccionar “Sí” en Pueblo indígena y comprobar carga del catálogo.
- Repetir desde registro público de docente.
- Comprobar carga desde edición de ficha estudiante y docente.
- Comprobar listado administrativo.
- Verificar claves `id_pueblo` y `pueblo` y ausencia de errores PHP/AJAX/JS.

### 25.2 CREATE

| Actor | Resultado esperado |
| --- | --- |
| Admin | Permitido; fila creada y lista recargada. |
| Comité | Permitido; fila creada y lista recargada. |
| Sin sesión | HTTP 403, `NO_AUTORIZADO`, mensaje visible y sin escritura. |
| Aceptado/Docente/Estudiante sin rol acumulativo | HTTP 403 y sin escritura. |
| Rol acumulativo con Admin o Comité | Permitido. |

### 25.3 UPDATE

| Actor | Resultado esperado |
| --- | --- |
| Admin | Permitido; fila editada y lista recargada. |
| Comité | Permitido; fila editada y lista recargada. |
| Sin sesión | HTTP 403, mensaje visible y sin escritura. |
| Actor sin Admin/Comité | HTTP 403 y sin escritura. |
| Rol acumulativo con Admin o Comité | Permitido. |

### 25.4 DELETE

- Admin elimina un Pueblo sin dependencias.
- Comité elimina un Pueblo sin dependencias.
- Pueblo con `usuario` asociado devuelve `TIENE_DEPENDENCIAS` y permanece intacto.
- ID inválido devuelve HTTP 400.
- Sin sesión devuelve HTTP 403.
- Actor sin Admin/Comité devuelve HTTP 403.
- Rol acumulativo con Admin o Comité permanece autorizado.

### 25.5 Regresión

- La lista se recarga después de operaciones exitosas.
- CREATE/UPDATE rechazados muestran el mensaje y no recargan como éxito.
- DELETE conserva confirmación y mensajes actuales.
- Los selects públicos y autenticados siguen funcionando.
- No aparecen errores PHP, AJAX ni JavaScript.
- La verificación de ausencia de escritura debe comparar el estado antes/después; Codex no ejecutará las operaciones.

## 24. Riesgos

| Riesgo | Nivel | Tratamiento |
| --- | --- | --- |
| R1 — cerrar READ requerido por registro | Alto | Excluir explícitamente READ del guard administrativo y probar consumers públicos. |
| R2 — proteger UI y dejar endpoint abierto | Alto | Hacer de `ajax/pueblo.php` la autoridad y probar POST directo mediante VF del usuario. |
| R3 — introducir política institucional nueva | Bajo | Reutilizar Admin/Comité ya observable y validado para DELETE. |
| R4 — romper roles acumulativos | Medio | Usar `Authorization::hasAny()` con semántica OR. |
| R5 — alterar DELETE seguro validado | Alto | Cambiar sólo el evaluador, preservando contrato e integridad. |
| R6 — modificar persistencia sin necesidad | Medio | Excluir modelo, helpers, esquema y migraciones. |
| R7 — olvidar endpoint alternativo | Bajo | Búsqueda global confirmó una única ruta activa de escritura. |
| R8 — 403 incompatible con frontend | Medio | Añadir error handler mínimo en `insertUpdate()`. |
| R9 — normalización EOL/EOF | Medio | Preservar materialización y revisar diff por archivo. |
| R10 — ampliar hacia otros catálogos | Medio | Limitar lógica y VF a Pueblo; no aprovechar el cambio para regularizar Título/Institución/etc. |

**Riesgo global:** Medio. La exposición backend es relevante, pero la regla, el endpoint y la reversión están claramente delimitados.

## 25. Reversibilidad

La futura implementación debe poder revertirse por archivo:

| Archivo | Reversión |
| --- | --- |
| `ajax/pueblo.php` | Retirar guards CREATE/UPDATE y restaurar el guard DELETE literal previo si fuera necesario. |
| `admin/scripts/listas.js` | Retirar exclusivamente el manejo de error añadido a `insertUpdate()`. |

La reversión no requiere:

- migración ni rollback de base de datos;
- limpieza o restauración de datos;
- modificación de sesión/login;
- eliminación de capacidades;
- restauración global del repositorio;
- decisión institucional nueva.

Los datos creados durante una eventual VF deberán gestionarse por el procedimiento de prueba autorizado, no por la reversión técnica del código.

## 26. EOL/EOF

`.gitattributes` declara `* text=auto`. Todos los blobs del índice usan LF; el worktree actual posee materialización mixta que debe preservarse sin normalización masiva.

| Archivo | Worktree | CRLF / LF aislado | BOM | Newline final | Blob HEAD | Blob HEAD^ |
| --- | --- | ---: | --- | --- | --- | --- |
| `admin/act.list.php` | mixed | 67 / 11 | No | Sí, LF | `8927d256908c7626bbdd1c67d88aac256a5cdee2` | igual |
| `admin/scripts/listas.js` | mixed | 227 / 7 | No | Sí, CRLF | `3069e615708d87fb1c37413ef99c905b30823d11` | igual |
| `js/funcAjax.js` | mixed | 116 / 70 | No | Sí, CRLF | `058a3f2e20db55c972e88e7f04ef1c39805e84cd` | igual |
| `ajax/pueblo.php` | mixed | 26 / 22 | No | Sí, LF | `2a8936bbfba55dfd12c7a09ca98c4ca597e8c0a4` | igual |
| `src/Model/Pueblo.php` | mixed | 18 / 42 | No | Sí, LF | `5b03b1d3030e1c041132dbd3b276eb13444807dc` | igual |
| `src/Security/Authorization.php` | mixed | 11 / 13 | No | Sí, LF | `171fe18da9ebe2766bb0e8135f5c8168ca19edbd` | igual |

Archivos previstos para modificación: `ajax/pueblo.php` y `admin/scripts/listas.js`. Los demás se incluyen como baseline de las capas inspeccionadas y no deberían cambiar.

Política para la futura Task:

- preservar el tipo de materialización del worktree;
- mantener newline final;
- no introducir BOM;
- asegurar que el blob versionado no normalice líneas ajenas respecto de su padre;
- revisar diff antes de staging para descartar cambios masivos EOL/EOF.

## 27. Historial

| Evidencia | Estado | Reutilización |
| --- | --- | --- |
| `9ca2dda` — migrate Pueblo insert to write contract | Implementado | Confirma contrato CREATE de EPIC-008. |
| `8f2b965` — migrate Pueblo edit to explicit write contract | Implementado | Confirma contrato UPDATE de EPIC-008. |
| `4ae45a4` — enforce restrict delete on catalog foreign keys | Cerrado/validado en `TASKS.md` | Confirma RESTRICT de `usuario.pueblo`. |
| `8e36912` — secure pueblo deletion with dependency validation | Implementado | Patrón funcional y de seguridad para DELETE. |
| `1290272` — documentación DELETE Pueblo | Cerrado con VF | Evidencia de actores y resultados. |
| `0b8e298` — confirmación/mensajes CRUD | Cerrado con VF | Componente frontend reutilizable. |
| `7d6fd40` — integración final DELETE en listas | Implementado | Confirma recarga sólo tras éxito. |
| `99b36c1` / `0c23b3c` | Implementados | Confirman contrato asociativo de READ. |

No se encontraron AT/Task específicos de autorización integral para CREATE/UPDATE de Pueblo. Este documento no duplica el cierre DELETE ni las migraciones de persistencia; las incorpora como evidencia ya cerrada.

## 28. Estado finalizable

Después de una única Task integral y su VF:

| Dimensión | Resultado esperado |
| --- | --- |
| READ | Cerrado, preservado para consumers legítimos. |
| CREATE | Cerrado con Admin/Comité y rechazo backend. |
| UPDATE | Cerrado con Admin/Comité y rechazo backend. |
| DELETE | Cerrado, reutilizando protección validada. |
| Frontend/backend | Coherente para actores y respuestas 403. |
| Consumers | Sin regresión de formato ni acceso READ. |
| Persistencia | Conforme a contratos actuales, sin cambios. |
| VF | Integral y viable. |

No existen operaciones bloqueadas, dependencia de EPIC-003 ni decisión nueva de EPIC-008. Pueblo puede quedar **FINALIZADO en autorización integral** después de una Task y VF únicas.

Observaciones no bloqueantes:

- deuda histórica de interpolación/contratos CREATE/UPDATE queda fuera de EPIC-001;
- la lectura heredada `archivos/ver.listas.php` no es activa;
- el helper frontend de CREATE/UPDATE es compartido y exige un diff especialmente acotado;
- debe vigilarse la materialización EOL mixta.

## 29. Auditoría metodológica

- EPIC funcional: EPIC-001.
- EPIC-009: marco de inspección y cierre CRUD integral.
- EPIC-003: conserva autoridad sobre reglas institucionales; no fue necesario redefinirlas.
- EPIC-008: conserva autoridad sobre persistencia; no requiere cambios para esta autorización.
- No se diseñaron capacidades nuevas.
- No se modifica login ni construcción de sesión.
- No se ejecutó VF ni operación real.
- Este AT es el único documento autorizado por la ejecución.

### Resumen obligatorio

```text
Objeto: Pueblo indígena
READ: Completo; preservar acceso de consumers públicos y autenticados
CREATE: Defectuoso; operación existe y falta guard backend
UPDATE: Defectuoso; operación existe y falta guard backend
DELETE: Completo; Admin/Comité, transaccional y validado
CRUD completo en modelo: Sí
READ requiere guard administrativo: No
CREATE guard actual: Ausente
UPDATE guard actual: Ausente
DELETE guard actual: Directo sobre admin/comite
Actores C/U/D: Admin OR Comité
Sin sesión C/U/D: Actualmente C/U permitidos indebidamente; D devuelve 403
Actor sin rol C/U/D: Actualmente C/U permitidos indebidamente; D devuelve 403
Authorization reutilizable: Sí, Authorization::hasAny(['admin','comite'])
Capacidad nueva necesaria: No
Frontend requiere cambios: Sí, manejo mínimo de 403 en CREATE/UPDATE
Endpoint requiere cambios: Sí
Modelo requiere cambios: No
Persistencia requiere cambios: No
Endpoint alternativo: No activo; existe lectura heredada abandonada sin callers
Consumers READ: 7 superficies activas más consumers indirectos de etiqueta/relación
Dependencia EPIC-003: No
Dependencia EPIC-008: Coordinación por autoridad; sin cambio requerido
Operaciones bloqueadas: Ninguna
Objeto finalizable en una Task: Sí
Task integral viable: Sí
VF integral viable: Sí, ejecutada exclusivamente por el usuario
Código modificado: No
Documentación creada: docs/architecture/AT-EPIC001-AUTORIZACION-PUEBLO-INTEGRAL-001.md
Otros documentos modificados: No
Staging: Vacío
Commit creado: No
Push realizado: No
```

## 30. Dictamen

**B. Pueblo puede cerrarse mediante Task integral con observaciones no bloqueantes.**

## 31. Siguiente artefacto

Una vez revisado y aprobado este AT, el siguiente artefacto autorizable es una única Task integral de autorización de Pueblo. Este AT no crea ni autoriza automáticamente esa Task.
