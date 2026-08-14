# TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001

## Migración de `Financiamiento::insertar()` al contrato explícito de escritura

### 1. Identificación

- **TASK:** TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001.
- **EPIC:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia.
- **FEATURE:** FEATURE-001 — Migración incremental por consumidor.
- **ADR:** ADR-001 — Contrato explícito para operaciones de escritura.
- **AT fuente:** AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001.
- **Commit fuente del AT:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Clasificación:** [PERSIST] [IMPL] [GOV] [DOC].
- **Nivel de operación:** L2 — creación documental de Task implementativa bajo supervisión de Dirección Técnica.
- **Estado:** Cerrada.

Durante su creación, este documento no autorizaba automáticamente a Codex a modificar código. La Task no era ejecutable hasta obtener aprobación de revisión técnica.

### 2. Estado Git de creación

- **Rama:** `refactor/fase-0-seguridad`.
- **HEAD local:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Referencia local `origin/refactor/fase-0-seguridad`:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Referencia remota verificada mediante `git ls-remote`:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Commit del AT:** disponible.
- **Staging inicial:** vacío.
- **Commit creado:** no.
- **Push realizado:** no.

`git fetch origin` no pudo escribir `.git/FETCH_HEAD` porque el entorno devolvió `Permission denied`. La referencia remota se verificó, sin modificar `.git`, mediante:

```text
git ls-remote origin refs/heads/refactor/fase-0-seguridad
```

El árbol de trabajo contenía previamente los siguientes cambios ajenos y protegidos:

- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `src/Model/Tesis.php`;
- `c1441353_antr_db.sql`.

También contenía documentos no rastreados preexistentes. Todos deben preservarse íntegramente y no forman parte de esta Task.

### 3. Fuente aprobada

La fuente exclusiva de esta Task es:

```text
docs/architecture/AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001.md
```

Publicada en el commit:

```text
0d75b7cba5f6ade15614fa8fe98d7829dead689e
```

El AT identifica:

- modelo `Financiamiento`;
- archivo `src/Model/Financiamiento.php`;
- método `insertar($nombre)`;
- operación `INSERT`;
- helper actual `ejecutarConsulta($sql)`;
- helper objetivo `ejecutarEscritura($sql)`;
- caller directo único `ajax/financiamiento.php`;
- retorno actual `PDOStatement`, utilizado únicamente por truthiness;
- retorno objetivo como arreglo explícito de escritura;
- ausencia de dependencia de `PDOStatement`;
- SQL y parámetros preservables;
- ausencia de transacción y de otros callers directos;
- riesgo técnico bajo y reversión de una sola línea;
- dictamen `A. Migración directa autorizable`.

Esta Task no reconstruye ni amplía el AT.

### 4. Objetivo

Autorizar una futura implementación mínima para migrar exclusivamente:

```php
Financiamiento::insertar($nombre)
```

desde `ejecutarConsulta($sql)` hacia `ejecutarEscritura($sql)`, preservando íntegramente:

- firma;
- SQL;
- parámetros e interpolación;
- flujo funcional;
- mensajes;
- callers;
- endpoint y frontend;
- métodos vecinos;
- helpers globales;
- comportamiento observable.

Esta Task no implementaba el cambio durante su creación documental.

### 5. Inspección previa

Se inspeccionaron:

- `docs/architecture/AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001.md`;
- `docs/adr/ADR-001-contrato-explicito-operaciones-escritura.md`;
- `docs/features/EPIC-008_FEATURE-001.md`;
- `src/Model/Financiamiento.php`;
- `ajax/financiamiento.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`.

Se confirmó:

- el método objetivo está intacto y coincide con el blob de `HEAD` `17b397e68b1b68d4e1a46640a793893e99a271ff`;
- el helper actual permanece intacto;
- el caller directo único es `ajax/financiamiento.php`;
- el retorno se evalúa únicamente como condición booleana;
- `insertarObtenerId()` es independiente;
- `editar()` y `eliminar()` quedan fuera de alcance;
- el branch `op=insert` queda fuera de alcance;
- no existe una Task previa equivalente;
- no existe implementación parcial;
- no hay cambios locales en `src/Model/Financiamiento.php`.

### 6. Alcance autorizado

La futura implementación podrá modificar exclusivamente:

```text
Archivo: src/Model/Financiamiento.php
Método: Financiamiento::insertar($nombre)
Líneas funcionales esperadas: 1
```

No se autoriza ningún otro cambio.

### 7. Transformación única autorizada

Antes:

```php
return ejecutarConsulta($sql);
```

Después:

```php
return ejecutarEscritura($sql);
```

La llamada objetivo debe conservar un único argumento. `idInsertado` no debe solicitarse explícitamente.

### 8. Archivos protegidos

Todos los archivos quedan protegidos excepto `src/Model/Financiamiento.php`, y este último sólo podrá modificarse en el método y la invocación expresamente autorizados.

Quedan protegidos de forma expresa:

- `ajax/financiamiento.php`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `src/Config/conexion.php`;
- `src/Model/Proyecto.php`;
- `src/Model/Tesis.php`;
- `src/Model/Curso.php`;
- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `c1441353_antr_db.sql`;
- `docs/`;
- `.env`;
- Composer;
- configuración PHP;
- infraestructura.

En una futura implementación no se crearán archivos. El presente documento es la única creación autorizada en esta ejecución documental.

### 9. Métodos y estructura protegidos

Dentro de `src/Model/Financiamiento.php` quedan protegidos:

- `Financiamiento::insertarObtenerId()`;
- `Financiamiento::editar()`;
- `Financiamiento::eliminar()`;
- constructor;
- lecturas;
- propiedades;
- includes;
- namespace;
- formato;
- EOL;
- EOF.

La futura implementación no deberá reorganizar el archivo.

### 10. SQL y parámetros protegidos

No se modificarán:

- sentencia `INSERT`;
- tabla;
- columnas;
- valores;
- interpolación existente;
- nombre del parámetro;
- firma del método;
- orden de ejecución.

Esta Task no autoriza parametrizar SQL, corregir defectos de seguridad preexistentes ni aplicar escaping adicional.

### 11. Contrato esperado

Contrato anterior:

```text
PDOStatement truthy en éxito
```

Contrato nuevo:

```php
[
    'filasAfectadas' => $filasAfectadas,
    'idInsertado' => $idInsertado,
]
```

El arreglo explícito de escritura es truthy. Para esta operación `idInsertado` no se solicitará explícitamente y permanecerá en el valor predeterminado del helper.

El caller deberá continuar entrando en el mismo branch de éxito. No se cambiarán mensajes, respuesta JSON, propagación de errores ni comportamiento observable.

### 12. Caller y flujo protegidos

El flujo confirmado es:

```text
admin/act.list.php
→ admin/scripts/listas.js
→ ajax/financiamiento.php
→ op=insert-update, id=0
→ Financiamiento::insertar($nombre)
```

El caller evalúa el retorno mediante una condición booleana. No usa:

- `rowCount()`;
- `lastInsertId()`;
- métodos de `PDOStatement`;
- serialización del retorno;
- comparación estricta de tipo.

El branch `op=insert` y `Financiamiento::insertarObtenerId()` son independientes y quedan excluidos. El caller no se modificará.

### 13. Criterios de aceptación técnicos

La futura implementación será aceptable sólo si:

1. modifica exclusivamente `src/Model/Financiamiento.php`;
2. modifica exclusivamente `Financiamiento::insertar($nombre)`;
3. el único cambio funcional es `ejecutarConsulta($sql)` → `ejecutarEscritura($sql)`;
4. el SQL permanece idéntico;
5. firma y parámetros permanecen idénticos;
6. `insertarObtenerId()` permanece idéntico;
7. `editar()` permanece idéntico;
8. `eliminar()` permanece idéntico;
9. `ajax/financiamiento.php` permanece idéntico;
10. el frontend permanece idéntico;
11. los helpers globales permanecen idénticos;
12. no se incorporan transacciones;
13. no se crean archivos;
14. no se cambia formato, EOL ni EOF;
15. el staging permanece vacío durante la implementación;
16. no se crea commit ni se realiza push.

### 14. Validaciones técnicas obligatorias

La futura implementación deberá ejecutar:

```text
php -l src/Model/Financiamiento.php
git diff --check
git diff -- src/Model/Financiamiento.php
git diff -U0 -- src/Model/Financiamiento.php
git status --short
git diff --cached --name-only
```

Además deberá verificar:

- una única invocación modificada;
- ningún otro método afectado;
- ningún archivo adicional creado o modificado por la implementación;
- caller intacto;
- `insertarObtenerId()` intacto;
- `editar()` intacto;
- `eliminar()` intacto;
- SQL, firma y parámetros intactos;
- ausencia de uso de `PDOStatement` por el caller.

### 15. Validación funcional prevista

La validación funcional será realizada exclusivamente por el usuario:

1. abrir administración de listas;
2. seleccionar Fuente de Financiamiento;
3. crear un registro de prueba controlado;
4. confirmar el mensaje `Fuente de Financiamiento Registrada`;
5. confirmar su aparición en la lista;
6. confirmar una única fila creada;
7. confirmar ausencia de warnings y errores PHP;
8. confirmar que no se alteró el flujo independiente que utiliza `insertarObtenerId()`.

Codex no ejecutará escrituras reales ni creará datos temporales.

### 16. Reversión exacta

Restaurar exclusivamente:

```php
return ejecutarConsulta($sql);
```

en `Financiamiento::insertar($nombre)`.

No modificar SQL, parámetros, otros métodos, callers, helpers, datos ni documentación.

**Reversibilidad:** alta, por tratarse de un cambio de una línea, un método y un archivo, con SQL y caller preservados.

### 17. Riesgos y controles

| Riesgo | Control |
|---|---|
| R1. Cambio de tipo `PDOStatement` → `array`. | El caller sólo usa truthiness. |
| R2. Modificación accidental de `insertarObtenerId()`. | Diff y comparación estática. |
| R3. Interferencia con `op=insert`. | Branch y método excluidos. |
| R4. Corrección accidental de `editar()` o `eliminar()`. | Métodos protegidos. |
| R5. Cambio accidental de SQL. | Diff de una sola invocación. |
| R6. Alteración de EOL o EOF. | Diff mínimo y `git diff --check`. |
| R7. Inclusión accidental de Cursos, Tesis o documentos locales. | Staging vacío y revisión de estado. |
| R8. Escritura funcional sobre datos reales por Codex. | Prohibición expresa. |

### 18. Condiciones de detención

La futura ejecución se detendrá sin modificar archivos si:

1. la rama no es `refactor/fase-0-seguridad`;
2. HEAD local y remoto no coinciden;
3. el staging no está vacío;
4. el AT no está disponible en el commit publicado;
5. `src/Model/Financiamiento.php` cambió respecto de la fuente inspeccionada;
6. `Financiamiento::insertar()` ya utiliza `ejecutarEscritura()`;
7. existen otros callers directos no documentados;
8. el caller depende de `PDOStatement`;
9. la transformación exige cambiar más de una invocación;
10. se requiere modificar SQL;
11. se requiere modificar parámetros;
12. se requiere modificar `insertarObtenerId()`;
13. se requiere modificar `editar()` o `eliminar()`;
14. se requiere modificar `ajax/financiamiento.php`;
15. se requiere modificar frontend;
16. se requiere modificar helpers globales;
17. se requiere tocar Proyecto, Tesis o Cursos;
18. se requiere una decisión institucional;
19. se requiere una decisión arquitectónica nueva;
20. la fuente aprobada resulta insuficiente;
21. existen cambios locales incompatibles en el archivo objetivo;
22. no puede garantizarse reversión de una línea.

Ante una detención se informarán la condición activada, evidencia, discrepancia, estado del método, callers encontrados, archivos involucrados, riesgo, alternativa mínima, archivos modificados, staging, commit y push.

### 19. Trazabilidad

```text
EPIC-008
→ FEATURE-001
→ ADR-001
→ INSPECCIÓN-EPIC008-SELECCION-CONSUMIDOR-PERSISTENCIA-001
→ AT-EPIC008-FINANCIAMIENTO-CONTRATO-ESCRITURA-001
→ TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001
```

Commit fuente del AT:

```text
0d75b7cba5f6ade15614fa8fe98d7829dead689e
```

### 20. Revisión técnica previa

Antes de implementar deberán verificarse:

- fuente exacta accesible y AT publicado;
- método intacto;
- alcance único y transformación exacta;
- archivos y métodos protegidos;
- validaciones y reversión;
- condiciones de detención;
- ausencia de ambigüedad y de decisiones nuevas.

La revisión técnica previa es obligatoria. Su aprobación no amplía el alcance ni autoriza cambios distintos de la transformación única.

### 21. Gobierno documental

Esta ejecución crea exclusivamente:

```text
docs/tasks/TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001.md
```

No se modificarán:

- `docs/TASKS.md`;
- `docs/roadmap/ROADMAP.md`;
- `docs/PROJECT_CONTEXT.md`;
- `docs/WORKFLOW.md`;
- ADR-001;
- FEATURE-001;
- el AT publicado;
- ningún otro documento.

La actualización de registros generales será posterior al cierre de la implementación.

### 22. Auditoría metodológica

La Task deja definidos:

- trazabilidad completa;
- fuente publicada;
- objetivo único;
- alcance mínimo;
- transformación inequívoca;
- archivos y métodos protegidos;
- reversión exacta;
- validación funcional por el usuario;
- ausencia de implementación;
- ausencia de cambios documentales adicionales;
- exclusión de Cursos, Tesis y Secure.

**Clasificación de auditoría:** [ARQ] [PERSIST] [IMPL] [GOV] [DOC] [BLOCK].

### 23. Estado de autorización original

```text
CREAR DOCUMENTO TASK: AUTORIZADO
MODIFICAR CÓDIGO: PROHIBIDO
IMPLEMENTAR: PROHIBIDO
MODIFICAR SQL: PROHIBIDO
MODIFICAR PARÁMETROS: PROHIBIDO
MODIFICAR CALLERS: PROHIBIDO
MODIFICAR HELPERS: PROHIBIDO
MODIFICAR OTROS DOCUMENTOS: PROHIBIDO
MODIFICAR CURSOS: PROHIBIDO
MODIFICAR TESIS: PROHIBIDO
REABRIR SECURE: PROHIBIDO
STAGING: PROHIBIDO
COMMIT: PROHIBIDO
PUSH: PROHIBIDO
```

### 24. Resumen de autorización futura

```text
Archivo futuro modificable: src/Model/Financiamiento.php
Método futuro modificable: Financiamiento::insertar($nombre)
Transformación: ejecutarConsulta($sql) → ejecutarEscritura($sql)
SQL modificable: No
Parámetros modificables: No
Callers modificables: No
Helpers modificables: No
Cantidad esperada de líneas funcionales modificadas: 1
```

### 25. Estado documental inicial

```text
Task creada: Sí
Estado de la Task: Aprobada para revisión técnica previa a implementación
Código modificado: No
Otros documentos modificados: No
Cursos modificado: No
Tesis modificada: No
Secure modificado: No
Staging modificado: No
Commit creado: No
Push realizado: No
```

### 26. Dictamen de creación

**A. Task completa y lista para revisión técnica.**

### 27. Cierre oficial

La Task queda cerrada después de completar la implementación, aprobar la
validación funcional y verificar la publicación remota.

`Financiamiento::insertar($nombre)` utiliza ahora
`ejecutarEscritura($sql)`. La implementación preservó el SQL, los parámetros,
la firma y el caller, que continúa consumiendo el retorno mediante truthiness.
`Financiamiento::insertarObtenerId()` permaneció intacto.

La incidencia de carga de la lista detectada durante la validación funcional
fue independiente de esta migración de persistencia. Después de cerrar la
corrección frontend, la validación funcional de persistencia fue reanudada y
aprobada por el usuario.

### 28. Evidencia de cierre

```text
Estado: Cerrada
Implementación: Completada
Validación funcional: Aprobada
Publicación: Completada
Commit: 2032cc33b9d7b90ea508ed0622968a488eb5c5f1
Mensaje: refactor(persistence): migrate financing insert write contract
Estado remoto: Publicado y verificado
```

### 29. Dictamen de cierre

**A. Task cerrada con implementación, validación funcional y publicación completadas.**
