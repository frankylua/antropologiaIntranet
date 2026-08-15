# TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001

## 1. Identificación

- **Nombre:** TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia
- **Feature:** FEATURE-001 — Evolución de los Contratos de Persistencia
- **ADR:** ADR-001 — Contrato explícito para operaciones de escritura
- **AT fuente:** AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001
- **Inspección previa:** INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-002
- **Clasificación:** [PERSIST] [IMPL] [GOV] [DOC] [REV]
- **Estado:** Aprobada para revisión técnica previa a implementación

La creación de este documento no autoriza automáticamente a Codex a modificar
código. La implementación requiere autorización específica y revisión técnica
previa conforme a esta Task.

## 2. Objetivo único

Autorizar para una futura implementación la migración exclusiva de:

```php
Beca::insertarList($nombre,$tipo_int)
```

desde:

```php
return ejecutarConsulta($sql);
```

hacia:

```php
return ejecutarEscritura($sql);
```

La futura implementación deberá preservar firma, SQL, parámetros,
interpolación, categorías, endpoint, frontend, mensajes, caller, métodos
vecinos, helpers globales y comportamiento observable. Esta ejecución es
exclusivamente documental y no implementa la transformación.

## 3. Fuente aprobada

Fuente primaria:

```text
docs/architecture/AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001.md
```

Estado del AT:

```text
Analizado — Migración directa autorizable
```

Dictamen del AT:

```text
A. Migración directa técnicamente autorizable.
```

Evidencia aprobada:

```text
Archivo: src/Model/Beca.php
Método: Beca::insertarList($nombre,$tipo_int)
Operación: INSERT
Helper actual: ejecutarConsulta($sql)
Helper objetivo: ejecutarEscritura($sql)
Caller directo: ajax/beca.php
Cantidad callers directos: 1
Retorno actual: PDOStatement
Uso del retorno: truthiness exclusivamente
ID requerido: No
Transacción: No
Cambios locales en Beca.php: No
Task previa equivalente: No
Riesgo: Bajo con controles
Reversión de código: Una línea
```

También se comprobó la vigencia de ADR-001, FEATURE-001, `src/Model/Beca.php`,
`ajax/beca.php`, `admin/scripts/listas.js`, `admin/act.list.php` y
`src/Config/conexion.php`. Esta Task no reconstruye ni amplía el AT.

## 4. Estado Git de la creación documental

- Rama: `refactor/fase-0-seguridad`.
- HEAD local: `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Referencia local `origin/refactor/fase-0-seguridad`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- HEAD remoto comprobado mediante `git ls-remote`:
  `2cbc7b3289e7e97bda941f0867f4c26bbc1d0085`.
- Staging inicial: vacío.
- `src/Model/Beca.php`: sin cambios locales.
- Blob de trabajo y HEAD de `Beca.php`:
  `f2726ef69af7101aff428a7fcf0bd253c71b27d1`.

Se preservaron `ajax/curso.php`, `form-doc/scripts/curso.js`,
`form-doc/ver.curso.php`, `src/Model/Tesis.php`, `c1441353_antr_db.sql` y todos
los documentos no rastreados preexistentes, incluido el AT fuente.

## 5. Comprobaciones previas

La revisión confirmó:

1. `insertarList()` continúa usando `ejecutarConsulta($sql)`.
2. Existe exactamente un caller directo en `ajax/beca.php`.
3. El retorno se usa exclusivamente como condición booleana.
4. No existe dependencia de `PDOStatement`.
5. No se requiere ID insertado.
6. No existe transacción.
7. SQL y parámetros pueden preservarse íntegramente.
8. No existe implementación parcial.
9. No existe Task previa equivalente.

No se activó una condición de detención durante esta creación documental.

## 6. Alcance futuro autorizado

```text
Archivo potencialmente modificable: src/Model/Beca.php
Método potencialmente modificable: Beca::insertarList($nombre,$tipo_int)
Cantidad esperada de cambios funcionales: 1 invocación
```

No se autoriza modificar otra línea funcional, método o archivo. La futura
implementación no deberá crear archivos. Este documento es la única creación
autorizada en la ejecución documental actual.

## 7. Transformación única

```diff
- return ejecutarConsulta($sql);
+ return ejecutarEscritura($sql);
```

Ubicación exclusiva: `Beca::insertarList($nombre,$tipo_int)`.

La invocación objetivo deberá conservar un solo argumento. No deberá solicitar
el ID insertado.

## 8. SQL y parámetros protegidos

El SQL deberá permanecer exactamente:

```php
$sql="INSERT INTO nombre_beca (id_nom_beca,beca,tipo_beca) VALUES (NULL,'$nombre','$tipo_int')";
```

No se autoriza cambiar tabla, columnas, orden, valores, `NULL`, `$nombre`,
`$tipo_int`, interpolación, espacios, comillas ni contenido de la cadena. No se
autoriza parametrización ni sanitización adicional.

## 9. Contrato actual y objetivo

| Aspecto | Actual | Objetivo |
|---|---|---|
| Helper | `ejecutarConsulta($sql)` | `ejecutarEscritura($sql)` |
| Retorno | `PDOStatement` | `array` |
| Truthiness | Truthy en éxito | Truthy en éxito |
| Filas afectadas | No expuestas | Expuestas, no consumidas |
| ID insertado | No obtenido | `null`, no requerido |

Resultado objetivo equivalente:

```php
[
    'filasAfectadas' => $filasAfectadas,
    'idInsertado' => null,
]
```

No se autoriza cambiar el caller para consumir claves ni convertir el retorno
en `bool`.

## 10. Diferencia residual conocida

```text
ejecutarConsulta():
si execute() no lanza excepción pero retorna false, puede devolver el statement.

ejecutarEscritura():
si execute() retorna false, lanza RuntimeException.
```

El AT determinó que esta diferencia no bloquea la migración bajo
`PDO::ERRMODE_EXCEPTION`. No se autorizan cambios adicionales para eliminarla ni
cambios en el manejo de errores.

## 11. Caller protegido

```text
Caller: ajax/beca.php
Branch: op=insert-update, $id_beca == 0
Cantidad: 1 caller directo
Uso: truthiness exclusivamente
```

Código observable protegido:

```php
$respuesta=$beca->insertarList($nombre,$tipo_int);
$respuesta ? $mensaje="Beca registrada" : $mensaje="Error: Beca no ha sido registrada";
```

No se autorizan `fetch()`, `rowCount()`, `lastInsertId()`, métodos de
`PDOStatement`, comparación estricta, serialización del retorno, acceso a
propiedades o claves, ni cambios de mensajes o JSON. Deben conservarse el
mensaje `Beca registrada` y la recarga posterior.

## 12. Categorías protegidas

```text
bec_int → 1
bec_ext → 2
```

No se autoriza redefinir tipos de beca, modificar la normalización ni incorporar
reglas institucionales.

## 13. Métodos protegidos

Dentro de `src/Model/Beca.php` quedan protegidos:

```php
Beca::insertar()
Beca::insertarObtenerId()
Beca::editar()
```

También quedan protegidos constructor, métodos de lectura, propiedades,
namespace, includes, formato, EOL y EOF. No se autoriza reorganizar ni
reformatear el archivo.

`Beca::insertarObtenerId()` mantiene un contrato independiente que retorna
`lastInsertId()` y no puede agruparse en esta Task.

## 14. Defecto preexistente protegido

El branch de edición de `ajax/beca.php` continúa usando `$id_inst`, variable no
definida en el endpoint. El defecto no afecta el branch seleccionado y queda
fuera de alcance. No debe corregirse, analizarse durante la implementación ni
usarse para ampliar esta Task.

## 15. Archivos protegidos

Todos los archivos excepto `src/Model/Beca.php` deberán considerarse protegidos.
Dentro del archivo permitido solo será modificable la invocación autorizada.

Protecciones expresas:

```text
ajax/beca.php
admin/scripts/listas.js
admin/act.list.php
src/Config/conexion.php
docs/architecture/AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001.md
docs/TASKS.md
docs/roadmap/ROADMAP.md
docs/PROJECT_CONTEXT.md
docs/WORKFLOW.md
docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md
docs/features/EPIC-008_FEATURE-001.md
src/Model/Tesis.php
ajax/curso.php
form-doc/scripts/curso.js
form-doc/ver.curso.php
c1441353_antr_db.sql
```

También quedan protegidos todos los demás archivos y documentos no rastreados
preexistentes.

## 16. Criterios de aceptación técnicos

La futura implementación será conforme solo si:

1. modifica exclusivamente `src/Model/Beca.php`;
2. modifica exclusivamente `Beca::insertarList($nombre,$tipo_int)`;
3. el único cambio funcional es `ejecutarConsulta($sql)` →
   `ejecutarEscritura($sql)`;
4. modifica exactamente una invocación;
5. conserva SQL, firma, parámetros e interpolación;
6. conserva `insertar()`, `insertarObtenerId()` y `editar()`;
7. conserva constructor y lecturas;
8. conserva endpoint, frontend, helpers y configuración;
9. conserva `bec_int → 1` y `bec_ext → 2`;
10. no solicita ID insertado;
11. no incorpora transacciones;
12. no crea archivos durante la implementación;
13. no altera EOL ni EOF;
14. preserva los cambios locales ajenos;
15. mantiene staging vacío hasta autorización expresa;
16. no crea commit;
17. no realiza push.

## 17. Validaciones técnicas futuras

```text
php -l src/Model/Beca.php
git diff --check
git diff -- src/Model/Beca.php
git diff -U0 -- src/Model/Beca.php
git status --short
git diff --cached --name-only
```

Además deberá demostrarse una sola invocación modificada; SQL, firma y
parámetros idénticos; métodos vecinos, caller, frontend, helper, categorías y
archivos protegidos intactos; staging vacío.

## 18. Validación funcional futura

La VF corresponde exclusivamente al usuario. Codex no deberá enviar POST ni
crear datos reales o temporales.

Política aprobada:

```text
VF sobre base descartable.
```

Pauta:

1. abrir «Listas predefinidas»;
2. seleccionar «Becas Internas» o «Becas Externas»;
3. crear un nombre de prueba único;
4. confirmar `Beca registrada`;
5. confirmar aparición en la categoría correcta;
6. confirmar exactamente una fila creada;
7. confirmar ausencia de errores PHP, AJAX y JavaScript;
8. confirmar recarga normal de la lista.

## 19. Limpieza de datos de VF

La UI no dispone de eliminación activa para nombres de beca. La VF preferida
debe utilizar una base descartable. Una limpieza manual solo podrá realizarse
con autorización específica posterior.

Esta Task no autoriza crear eliminación, modificar el endpoint, ampliar
`eliminarLista()`, agregar botones ni automatizar limpieza.

## 20. Reversión

Reversión de código:

```diff
- return ejecutarEscritura($sql);
+ return ejecutarConsulta($sql);
```

La reversión se limita a `Beca::insertarList($nombre,$tipo_int)`.

La reversión de datos es independiente y deberá resolverse descartando la base
de prueba o, únicamente con autorización posterior, mediante limpieza manual.
No se autoriza automatizarla.

## 21. Riesgos y controles

| Riesgo | Control |
|---|---|
| R1 — `PDOStatement` → `array` | Caller preservado; truthiness exclusiva |
| R2 — Alterar `insertarObtenerId()` | Método protegido y diff específico |
| R3 — Corregir el branch `$id_inst` | Branch y endpoint fuera de alcance |
| R4 — Modificar SQL | Comparación literal y diff de una invocación |
| R5 — Cambiar categorías | Endpoint y frontend protegidos |
| R6 — Solicitar ID | Helper objetivo con un solo argumento |
| R7 — Cambiar endpoint/frontend | Archivos protegidos |
| R8 — No limpiar la VF | Base descartable |
| R9 — Mezclar cambios ajenos | Estado por ruta y staging vacío |
| R10 — Alterar EOL/EOF | Diff mínimo y comprobación `-U0` |

Riesgo global esperado: bajo, condicionado al cumplimiento de los controles.

## 22. Condiciones de detención de la futura implementación

La implementación deberá detenerse sin modificar archivos si:

1. la rama no es `refactor/fase-0-seguridad`;
2. HEAD local y remoto no coinciden;
3. staging no está vacío;
4. `src/Model/Beca.php` contiene cambios locales;
5. el AT fuente no está disponible;
6. `insertarList()` ya utiliza `ejecutarEscritura()`;
7. el método difiere del AT;
8. el SQL difiere sustancialmente;
9. existen callers adicionales no analizados;
10. el caller depende de `PDOStatement`;
11. se requiere ID insertado;
12. existe una transacción no documentada;
13. se requiere modificar `insertar()`;
14. se requiere modificar `insertarObtenerId()`;
15. se requiere modificar `editar()`;
16. se requiere corregir `$id_inst`;
17. se requiere modificar endpoint;
18. se requiere modificar frontend;
19. se requiere modificar el helper global;
20. se requiere modificar SQL;
21. se requiere modificar parámetros;
22. se requiere cambiar categorías de beca;
23. se requiere modificar más de una invocación;
24. se requiere tocar Cursos o Tesis;
25. se requiere una decisión institucional;
26. se requiere una decisión arquitectónica nueva;
27. la fuente resulta insuficiente o contradictoria;
28. no puede garantizarse reversión de una línea;
29. no pueden preservarse EOL y EOF.

Ante detención no deberá modificarse ningún archivo. El informe deberá registrar
condición, evidencia, rama, HEAD, staging, método, SQL, callers, contrato,
discrepancia, riesgo, alternativa mínima, archivos modificados, commit y push.

## 23. Fuera de alcance

No se autorizan parametrización, sanitización, refactor de Beca, reparación de
`$id_inst`, migración de otros métodos, nuevos helpers, Repository, DAO, ORM,
transacciones, cambios de esquema, eliminación de catálogo, decisiones
institucionales, cambios de endpoint/frontend/mensajes/JSON, datos creados por
Codex, staging, commit ni push.

## 24. Trazabilidad

```text
EPIC-008 — Gobierno del Modelo de Datos y Persistencia
→ FEATURE-001 — Evolución de los Contratos de Persistencia
→ ADR-001 — Contrato explícito para operaciones de escritura
→ INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-002
→ AT-EPIC008-BECA-INSERTARLIST-CONTRATO-ESCRITURA-001
→ TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001
→ futura revisión técnica
→ futura implementación
```

## 25. Auditoría metodológica

- [PERSIST] Un consumidor confirmado de escritura.
- [IMPL] Una transformación futura, no ejecutada.
- [GOV] Secuencia EPIC → FEATURE → ADR → selección → AT → Task.
- [DOC] Esta ejecución crea exclusivamente este documento.
- [REV] Reversión de código separada de datos de VF.
- No se registra la Task como implementada, cerrada, publicada o en ejecución.

## 26. Estado y dictamen

```text
Estado:
Aprobada para revisión técnica previa a implementación
```

**Dictamen: A. Task completa y lista para revisión técnica.**

La Task contiene objetivo único, fuente, alcance, protecciones, transformación,
criterios de aceptación, validaciones, riesgos, reversión y condiciones de
detención. Su creación no autoriza todavía la implementación.

## 27. Resumen de autorización futura

```text
Task creada: TASK-EPIC008-BECA-INSERTARLIST-ESCRITURA-001
Estado: Aprobada para revisión técnica previa a implementación
Archivo futuro modificable: src/Model/Beca.php
Método futuro modificable: Beca::insertarList($nombre,$tipo_int)
Cambio funcional: Una invocación
Helper actual: ejecutarConsulta($sql)
Helper objetivo: ejecutarEscritura($sql)
SQL modificable: No
Parámetros modificables: No
Caller modificable: No
insertar modificable: No
insertarObtenerId modificable: No
editar modificable: No
ID requerido: No
Transacción: No
VF por usuario: Sí
VF sobre base descartable: Sí
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```
