# TASK-EPIC009-GRADO-INTEGRAL-001

## Grado Académico integral

## 1. Estado

~~~text
Task: CERRADA
AT: APROBADO
Implementación: COMPLETADA
Revisión técnica: APROBADA
Validación funcional: APROBADA POR EL USUARIO
Blockers: NINGUNO
~~~

La implementación, la revisión técnica y la validación funcional del CRUD
integral de Grado Académico están completadas. La VF fue ejecutada y aprobada
por el usuario.

## 2. Trazabilidad de la regularización documental

La Task integral fue autorizada y ejecutada mediante instrucción operativa
aprobada bajo `AT-EPIC009-GRADO-INTEGRAL-001`. Durante el cierre se detectó que
el artefacto Task no había sido materializado como archivo versionable. Este
documento regulariza esa omisión documental sin modificar el alcance, las
decisiones ni la implementación ya validados.

Esta regularización es posterior a la ejecución técnica. No afirma que este
archivo existiera previamente ni reconstruye commits, fechas o validaciones no
realizadas.

## 3. Fuente arquitectónica

La fuente arquitectónica obligatoria y aprobada es:

- [AT-EPIC009-GRADO-INTEGRAL-001](../architecture/AT-EPIC009-GRADO-INTEGRAL-001.md).

La Task pertenece a **EPIC-009 — Consolidación Funcional de Objetos y CRUD
Integral** y no introduce decisiones adicionales a las contenidas en el AT.

## 4. Objetivo funcional

Cerrar integralmente el CRUD de `grado_academico`:

- CREATE;
- READ;
- UPDATE;
- DELETE.

El cierre incluye autenticación, autorización backend, ownership, protección
IDOR, CSRF para CREATE/UPDATE/DELETE, SQL parametrizado, respuestas HTTP/JSON
coherentes, validaciones, comprobación de filas afectadas y DELETE físico.

También preserva la compatibilidad con Ficha Estudiante y Ficha Docente y la
operación global autorizada para Admin y Comité.

## 5. Matriz institucional implementada

### 5.1 Profesor con acceso vigente a su perfil

- READ propio.
- CREATE propio.
- UPDATE propio.
- DELETE propio.

No puede operar Grados ajenos salvo que acumule el rol Admin o Comité.

### 5.2 Estudiante con acceso vigente a su perfil

- READ propio.
- CREATE propio.
- UPDATE propio.
- DELETE propio.

El acceso estudiantil utiliza el contrato vigente `perfil.ver`.

### 5.3 Admin

CRUD global sobre Grados de profesores y estudiantes válidos.

### 5.4 Comité

CRUD global sobre Grados de profesores y estudiantes válidos.

### 5.5 Anónimo

Sin acceso.

## 6. Identidad objetivo

Para una operación global, un usuario objetivo válido debe poseer exactamente
una especialización:

~~~text
Profesor XOR Estudiante
~~~

Se rechaza:

- una identidad que sea simultáneamente Profesor y Estudiante;
- un usuario sin especialización;
- un usuario inexistente.

La revisión técnica detectó como blocker que la implementación aceptaba
`Profesor OR Estudiante`, incluida una identidad con ambas especializaciones.
La regla fue corregida a `Profesor XOR Estudiante`.

## 7. Ownership

La autoridad de ownership es:

~~~text
grado_academico.usuario
~~~

Para Profesor y Estudiante:

- CREATE deriva el propietario desde la sesión;
- READ permite sólo registros propios;
- UPDATE permite sólo registros propios;
- DELETE permite sólo registros propios.

Para Admin y Comité:

- el usuario objetivo es explícito;
- el usuario debe existir;
- el usuario debe satisfacer `Profesor XOR Estudiante`.

El request nunca constituye autoridad de ownership.

## 8. Archivos funcionales

La implementación quedó limitada exactamente a:

1. `src/Model/Grado.php`;
2. `ajax/grado.php`;
3. `form-doc/scripts/grado.js`;
4. `form-doc/agr.form.dat.acad.php`.

No forman parte de la Task otros archivos funcionales.

## 9. Persistencia

- El schema no cambió.
- No se creó ninguna migración.
- Las consultas utilizan SQL parametrizado.
- INSERT utiliza `ejecutarEscritura()`.
- UPDATE utiliza `ejecutarEscritura()`.
- DELETE es físico y utiliza `ejecutarEscritura()`.
- READ entrega resultados asociativos.
- Las operaciones de escritura comprueban las filas afectadas.

Institución y Título permanecieron protegidos y no fueron reabiertos.

## 10. Seguridad resuelta dentro de Grado

La implementación resolvió, dentro de la frontera del objeto Grado:

- acceso anónimo al CRUD;
- IDOR;
- ausencia de ownership;
- CREATE para un usuario arbitrario;
- UPDATE y DELETE de registros ajenos;
- SQL interpolado;
- ausencia de CSRF;
- fechas inválidas;
- IDs inválidos;
- respuestas falsas de éxito;
- omisión de la comprobación de filas afectadas.

Este cierre no declara resuelta la seguridad de objetos o superficies ajenos a
Grado Académico.

## 11. Correcciones durante el ciclo

### 11.1 Identidad ambigua

La revisión técnica detectó que se aceptaba `Profesor OR Estudiante`, lo que
incluía identidades que fueran simultáneamente Profesor y Estudiante. Se
corrigió la validación a `Profesor XOR Estudiante`.

La revisión técnica corta posterior quedó **APROBADA CON OBSERVACIONES NO
BLOQUEANTES**.

### 11.2 Error de parseo frontend durante VF

Durante la VF, el botón de alta no desplegaba el formulario. La causa fue que
`mostrarErrorGrado(mensaje)` redeclaraba localmente `const mensaje`.

La corrección mínima renombró la constante local a `mensajeDestino`. El blocker
quedó resuelto y la VF pudo reanudarse.

## 12. Validación funcional

~~~text
VF: APROBADA POR EL USUARIO
~~~

La VF aprobó el comportamiento observable de Grado después de las correcciones
de identidad ambigua y parseo frontend. La aprobación corresponde al usuario,
no a Codex.

## 13. Observaciones no bloqueantes

1. La diferencia entre respuestas 403 y 404 permite una inferencia limitada de
   existencia.
2. `form-doc/scripts/grado.js` conserva un vaciado redundante de
   `#grados_card`.
3. Las altas contextuales de Institución/Título y la operación posterior de
   Grado continúan siendo multi-request y no forman una transacción única.

Estado: **NO BLOQUEANTES**.

No se crean Tasks adicionales por estas observaciones durante la presente
regularización.

## 14. Objetos y superficies protegidos

No se reabrieron:

- Institución;
- Título;
- Login;
- Authorization;
- Ficha Académica como objeto integral;
- BD/schema.

## 15. Criterios de cierre

- [x] Frontera definida.
- [x] CRUD integral implementado.
- [x] Matriz institucional implementada.
- [x] Ownership backend.
- [x] Autorización backend.
- [x] IDOR corregido.
- [x] CSRF aplicado.
- [x] SQL parametrizado.
- [x] DELETE explícito.
- [x] Revisión técnica aprobada.
- [x] Blockers corregidos.
- [x] VF aprobada por el usuario.
- [x] Sin cambios de BD.
- [x] Sin migraciones.

## 16. Gobierno y cierre

~~~text
Estado: CERRADA
AT: APROBADO
Implementación: COMPLETADA
Revisión técnica: APROBADA
VF: APROBADA POR EL USUARIO
Blockers: NINGUNO
~~~

El cierre no requiere:

- ADR;
- addendum;
- nueva decisión institucional.

La omisión del archivo Task queda regularizada sin alterar la implementación ni
las decisiones aprobadas.
