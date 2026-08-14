# TASK-EPIC008-FINANCIAMIENTO-LISTA-ASOCIATIVA-001

## Corrección del contrato asociativo de la lista de Financiamiento

### 1. Identificación

- **TASK:** TASK-EPIC008-FINANCIAMIENTO-LISTA-ASOCIATIVA-001.
- **Tipo:** Corrección técnica independiente detectada durante Validación Funcional.
- **EPIC de contexto:** EPIC-008 — Gobierno del Modelo de Datos y Persistencia.
- **Fuente técnica:** INSPECCIÓN-EPIC008-VF-FINANCIAMIENTO-LISTA-001.
- **Dictamen fuente:** A. Defecto frontend preexistente y corrección mínima identificada.
- **Clasificación:** [IMPL] [FRONT] [CONTRACT] [VF-BLOCKER] [GOV].
- **Nivel de operación:** L2 — creación documental de Task correctiva bloqueante de validación funcional.
- **Estado:** Aprobada para revisión técnica previa a implementación.

Esta Task bloquea temporalmente la Validación Funcional de `TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001`, pero no forma parte de su transformación de persistencia. La creación de este documento no autoriza automáticamente su implementación.

### 2. Estado Git de creación

- **Rama:** `refactor/fase-0-seguridad`.
- **HEAD local:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Referencia local `origin/refactor/fase-0-seguridad`:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Referencia remota verificada mediante `git ls-remote`:** `0d75b7cba5f6ade15614fa8fe98d7829dead689e`.
- **Staging inicial:** vacío.
- **Commit creado:** no.
- **Push realizado:** no.

El árbol de trabajo contenía previamente cambios ajenos y protegidos en:

- `ajax/curso.php`;
- `form-doc/scripts/curso.js`;
- `form-doc/ver.curso.php`;
- `src/Model/Tesis.php`;
- `c1441353_antr_db.sql`;
- documentos no rastreados preexistentes.

También contenía el cambio pendiente autorizado de `TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001` en `src/Model/Financiamiento.php`:

```diff
- return ejecutarConsulta($sql);
+ return ejecutarEscritura($sql);
```

Todos esos cambios deben preservarse íntegramente y no forman parte de esta Task.

### 3. Fuente aprobada

La fuente técnica exclusiva es `INSPECCIÓN-EPIC008-VF-FINANCIAMIENTO-LISTA-001`, cuyo dictamen identifica un defecto frontend preexistente y una corrección mínima.

Cadena funcional aprobada:

```text
admin/act.list.php
→ #btn_financ
→ admin/scripts/listas.js
→ clickListas("financ")
→ cargarListas("financ")
→ ajaxListas("#listas", "../ajax/financiamiento.php", "read")
→ ajax/financiamiento.php, op=read
→ Financiamiento::mostrar()
→ ejecutarConsultaResultados()
→ PDO::FETCH_ASSOC
→ JSON asociativo
```

Evidencia aprobada:

- `ajaxListas()` admite `propiedadId` y `propiedadEtiqueta`;
- el caller de Financiamiento no entrega esas propiedades;
- el fallback intenta acceder a `list[0]` y `list[1]`;
- el backend entrega `id_financ` y `financiamiento`;
- `list[1]` resulta `undefined`;
- `cadenaMay(undefined)` falla en `cadena.split(" ")`;
- el defecto no está relacionado con `ejecutarEscritura()`;
- no deben modificarse `cadenaMay()`, endpoint, modelo ni backend.

Esta Task no reconstruye ni amplía la evidencia aprobada.

### 4. Objetivo único

Corregir exclusivamente el consumidor de Financiamiento que invoca `ajaxListas()` sin indicar las propiedades asociativas devueltas por el backend.

La corrección deberá agregar únicamente:

- propiedad ID: `id_financ`;
- propiedad etiqueta: `financiamiento`.

Esta Task no implementa el cambio durante su creación documental.

### 5. Inspección previa

Se inspeccionaron íntegramente:

- `js/funcAjax.js`;
- `js/funcForm.js`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `ajax/financiamiento.php`;
- `src/Model/Financiamiento.php`;
- los helpers de conexión y lectura llamados directamente por el flujo.

Se confirmó:

1. La invocación objetivo continúa siendo `ajaxListas("#listas", "../ajax/financiamiento.php", "read");`.
2. `ajaxListas()` continúa aceptando `propiedadId` y `propiedadEtiqueta`.
3. El backend continúa entregando `id_financ` y `financiamiento` mediante `PDO::FETCH_ASSOC`.
4. `cadenaMay()` no requiere modificación.
5. No existe una corrección ya aplicada al consumidor de Financiamiento.
6. `js/funcAjax.js` no contiene cambios locales incompatibles.

### 6. Alcance autorizado

La futura implementación podrá modificar exclusivamente:

```text
Archivo: js/funcAjax.js
Invocación: ajaxListas() dentro del branch n_input == "financ"
Cambios funcionales esperados: una invocación / dos argumentos explícitos
```

No se autoriza ningún otro cambio.

### 7. Transformación futura exacta

Antes:

```javascript
ajaxListas("#listas", "../ajax/financiamiento.php", "read");
```

Después:

```javascript
ajaxListas(
  "#listas",
  "../ajax/financiamiento.php",
  "read",
  undefined,
  "id_financ",
  "financiamiento"
);
```

Si el formato vigente mantiene la invocación en una línea, podrá conservarse en una línea. La autorización semántica cubre exclusivamente la adición de `id_financ` y `financiamiento`; no autoriza reformatear código adicional.

### 8. Contrato frontend/backend

Contrato genérico vigente:

```javascript
let idLista = propiedadId ? list[propiedadId] : list[0];
let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
```

Contrato backend vigente:

```json
[
  {
    "id_financ": "<identificador>",
    "financiamiento": "<texto>"
  }
]
```

Contrato esperado después de la corrección:

```text
propiedadId: id_financ
propiedadEtiqueta: financiamiento
idLista: list["id_financ"]
etiquetaLista: list["financiamiento"]
cadenaMay(): recibe una cadena válida
```

El backend no cambia.

### 9. Archivos y elementos protegidos

Todos los archivos quedan protegidos excepto `js/funcAjax.js`, y este último sólo podrá modificarse en la invocación expresamente autorizada.

Quedan protegidos de forma expresa:

- `js/funcForm.js` y `cadenaMay()`;
- la función genérica `ajaxListas()`;
- `ajaxSelect()`;
- `admin/scripts/listas.js`;
- `admin/act.list.php`;
- `ajax/financiamiento.php`;
- `src/Model/Financiamiento.php`;
- `src/Config/conexion.php`;
- Cursos, Tesis y Proyecto;
- SQL y helpers PHP;
- persistencia y la migración pendiente de `Financiamiento::insertar()`;
- Secure y `session.cookie_secure`;
- documentación, Composer, configuración e infraestructura;
- todos los demás consumidores de listas;
- formato fuera de la expresión objetivo, EOL y EOF.

### 10. Criterios de aceptación técnicos

La futura implementación será aceptable sólo si:

1. modifica exclusivamente `js/funcAjax.js`;
2. modifica exclusivamente la invocación de `ajaxListas()` correspondiente a Financiamiento;
3. agrega exactamente `id_financ` y `financiamiento`;
4. no modifica la función genérica `ajaxListas()`;
5. no modifica `cadenaMay()`;
6. no modifica `ajax/financiamiento.php`;
7. no modifica `Financiamiento::mostrar()`;
8. no modifica `Financiamiento::insertar()` ni su migración pendiente;
9. no modifica otro consumidor de listas;
10. no modifica formato fuera de la expresión objetivo;
11. preserva EOL y EOF;
12. mantiene staging vacío;
13. no crea archivos;
14. no crea commit;
15. no realiza push.

### 11. Validaciones técnicas futuras

Después de implementar se deberá ejecutar:

```text
git diff --check
git diff -- js/funcAjax.js
git diff -U0 -- js/funcAjax.js
git status --short
git diff --cached --name-only
```

Además se deberá verificar:

- una sola invocación modificada;
- presencia exacta de `id_financ` y `financiamiento`;
- función genérica `ajaxListas()` idéntica;
- `cadenaMay()` idéntica;
- endpoint y backend idénticos;
- modelo idéntico respecto de su cambio de persistencia pendiente;
- ningún archivo adicional atribuible a esta Task.

### 12. Validación funcional

La validación funcional corresponde exclusivamente al usuario:

1. Abrir administración de listas.
2. Seleccionar Fuente de Financiamiento.
3. Confirmar que la lista carga.
4. Confirmar ausencia de `Cannot read properties of undefined (reading 'split')`.
5. Confirmar visualización correcta de nombres.
6. Confirmar selección correcta de registros existentes.
7. Confirmar ausencia de nuevos errores JavaScript.

Después de aprobar esta validación se reanudará la VF pendiente de `TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001` y su inserción controlada.

Codex no debe ejecutar ninguna Validación Funcional ni realizar escrituras reales.

### 13. Reversión exacta

Restaurar exclusivamente:

```javascript
ajaxListas("#listas", "../ajax/financiamiento.php", "read");
```

No modificar ningún otro elemento.

**Reversibilidad:** alta, por tratarse de un archivo, una invocación y dos propiedades explícitas, sin cambios backend ni globales.

### 14. Riesgos y controles

| Riesgo | Severidad | Control |
|---|---|---|
| R1. Modificar `ajaxListas()` globalmente. | Alta | Prohibido; modificar exclusivamente el caller de Financiamiento. |
| R2. Modificar `cadenaMay()` para aceptar `undefined`. | Alta | Prohibido; corregir el contrato del consumidor. |
| R3. Utilizar nombres incorrectos. | Media | Usar exactamente `id_financ` y `financiamiento`. |
| R4. Modificar otros consumidores. | Media | Revisar el diff `-U0`. |
| R5. Alterar la migración pendiente de persistencia. | Alta | Mantener protegido `src/Model/Financiamiento.php`. |
| R6. Alterar EOL o EOF. | Media | Aplicar un diff mínimo y comprobar formato. |

### 15. Condiciones de detención

La futura implementación se detendrá sin modificar archivos si:

1. la rama no es `refactor/fase-0-seguridad`;
2. el staging no está vacío;
3. la invocación objetivo ya fue corregida;
4. `ajaxListas()` no posee los parámetros documentados;
5. el backend ya no devuelve `id_financ`;
6. el backend ya no devuelve `financiamiento`;
7. se requiere modificar `cadenaMay()`;
8. se requiere modificar `ajaxListas()` globalmente;
9. se requiere modificar endpoint, modelo o SQL;
10. se requiere intervenir la Task de persistencia;
11. se requiere modificar más de una invocación;
12. se requiere modificar otro consumidor;
13. existen cambios locales incompatibles en `js/funcAjax.js`;
14. la evidencia aprobada resulta insuficiente;
15. se requiere una decisión arquitectónica nueva.

Ante una detención se informarán la condición activada, evidencia, expresión actual, estructura backend, archivos involucrados, riesgo, alternativa mínima, archivos modificados, staging, commit y push.

### 16. Trazabilidad

```text
VF de TASK-EPIC008-FINANCIAMIENTO-INSERTAR-ESCRITURA-001
→ defecto preexistente detectado
→ INSPECCIÓN-EPIC008-VF-FINANCIAMIENTO-LISTA-001
→ TASK-EPIC008-FINANCIAMIENTO-LISTA-ASOCIATIVA-001
→ corrección
→ VF de lista
→ reanudación VF de persistencia
```

### 17. Revisión técnica previa

Antes de implementar deberán confirmarse fuente, invocación, contrato asociativo, alcance único, elementos protegidos, validaciones, reversión y condiciones de detención.

La Task no será ejecutable hasta obtener aprobación de revisión técnica. Esa aprobación no ampliará el alcance ni autorizará cambios distintos de la invocación definida.

### 18. Gobierno documental

Esta ejecución crea exclusivamente:

```text
docs/tasks/TASK-EPIC008-FINANCIAMIENTO-LISTA-ASOCIATIVA-001.md
```

No se modifican registros generales, Roadmap, Project Context, Workflow, ADR, AT, Feature, otras Tasks ni código.

### 19. Auditoría metodológica

- **[IMPL]** Corrección única e inequívoca.
- **[FRONT]** El cambio potencial se limita al caller frontend de Financiamiento.
- **[CONTRACT]** El consumidor se adapta al contrato asociativo vigente.
- **[GOV]** La Task permanece independiente del cambio de persistencia.
- **[VF-BLOCKER]** Su corrección es requisito para completar la VF pendiente.
- **[BLOCK]** No mezclar con `cadenaMay()`, backend o persistencia.

### 20. Estado de autorización

```text
CREAR DOCUMENTO TASK: AUTORIZADO
IMPLEMENTAR: PROHIBIDO
MODIFICAR JS: PROHIBIDO
MODIFICAR PHP: PROHIBIDO
MODIFICAR BACKEND: PROHIBIDO
MODIFICAR cadenaMay: PROHIBIDO
MODIFICAR ajaxListas GLOBAL: PROHIBIDO
STAGING: PROHIBIDO
COMMIT: PROHIBIDO
PUSH: PROHIBIDO
```

### 21. Resumen

```text
Task creada: Sí
Estado: Aprobada para revisión técnica previa a implementación
Archivo futuro modificable: js/funcAjax.js
Invocación modificable: ajaxListas() del branch de Financiamiento
Propiedad ID: id_financ
Propiedad etiqueta: financiamiento
cadenaMay modificable: No
ajaxListas global modificable: No
Backend modificable: No
Modelo modificable: No
Persistencia modificada: No
Código modificado: No
Otros documentos modificados: No
Staging modificado: No
Commit creado: No
Push realizado: No
```

### 22. Dictamen

**A. Task completa y lista para revisión técnica.**
