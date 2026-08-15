# Roadmap Maestro de Modernización

Este roadmap organiza la modernización incremental de la intranet a partir de problemas confirmados. Cada épica se descompondrá posteriormente en Features y Tasks mediante el flujo oficial de modernización. No define soluciones técnicas ni arquitectura futura.

## Definición de prioridades

* **Crítica:** protege seguridad, integridad o continuidad funcional.
* **Alta:** elimina riesgos estructurales que bloquean la evolución segura.
* **Media:** mejora mantenibilidad o experiencia, pero requiere bases previas.
* **Baja:** optimización conveniente sin riesgo confirmado inmediato.

## Épicas

### EPIC-001 — Modernización del modelo de autorización y permisos

#### Objetivo

Evolucionar el control de acceso para que los permisos de cada cuenta sean coherentes, preservables y verificables durante todos los procesos del sistema.

#### Problema confirmado

La autorización está distribuida. Las cuentas pueden tener múltiples permisos, existen permisos mezclados con estados académicos y se ha identificado una posible pérdida de roles durante cambios de estado.

El sistema diferencia actores principales —estudiante, profesor, comité académico y administrador— de participaciones académicas contextuales que no necesariamente constituyen roles de seguridad. Esta distinción debe conservarse durante la modernización.

#### Evidencia

* Asignación de permisos mediante `permiso_login`.
* Autorización distribuida.
* Múltiples permisos por cuenta.
* Permisos mezclados con estados académicos.
* Reconstrucción de permisos como proceso existente.
* Posible pérdida de roles durante cambios de estado.
* Existencia de actores de seguridad y participaciones académicas contextuales diferentes.

#### Riesgo de no realizarla

* Acceso indebido a funciones.
* Pérdida accidental de permisos legítimos.
* Bloqueo de usuarios después de cambios académicos.
* Interpretación incorrecta de una participación académica como rol de seguridad.
* Mayor dificultad para modificar cualquier módulo protegido.

#### Dependencias

No depende de otra épica para comenzar a nivel estratégico.

Debe coordinarse estrechamente con:

* EPIC-002, gestión de identidades y cuentas.
* EPIC-003, estados académicos y preservación de roles.
* EPIC-004, integridad transaccional.

#### Beneficio esperado

* Mayor seguridad funcional.
* Comportamiento de acceso predecible.
* Menor riesgo al modernizar módulos protegidos.
* Base común para la evolución posterior de toda la intranet.

#### Impacto para usuarios

**Alto.**

Afecta directamente qué información y operaciones puede utilizar cada estudiante, profesor, integrante del comité académico o administrador.

#### Prioridad

**Crítica**

#### Esfuerzo relativo

**XL**

La autorización atraviesa cuentas, permisos, sesiones, estados académicos y múltiples módulos.

#### Criterios de éxito

* Los permisos efectivos de una cuenta pueden determinarse de manera consistente.
* Los cambios de estado no eliminan permisos independientes del estado académico.
* Las participaciones académicas no se interpretan automáticamente como roles de seguridad.
* Los flujos existentes de inicio de sesión y construcción de sesión conservan compatibilidad funcional.
* No se producen ampliaciones ni reducciones involuntarias de acceso.
* Los módulos protegidos mantienen el comportamiento autorizado previamente validado.

### EPIC-002 — Modernización de la gestión de identidades y ciclo de vida de cuentas

#### Objetivo

Fortalecer la gestión de cuentas e identidades personales durante su creación, especialización académica, modificación y retiro del sistema.

#### Problema confirmado

El sistema separa la cuenta de acceso (`login`), la identidad personal (`usuario`) y las especializaciones académicas (`profesor` y `estudiante`). También existe eliminación física de cuentas, identificada explícitamente como riesgo.

#### Evidencia

* Existencia de cuentas de acceso.
* Existencia de identidades personales.
* Perfiles diferenciados de profesor y estudiante.
* Registro de estudiantes y profesores.
* Inicio de sesión y construcción de sesión sobre cuentas.
* Eliminación física de cuentas como proceso implementado y riesgo conocido.

#### Riesgo de no realizarla

* Pérdida irreversible de información vinculada a una persona.
* Inconsistencias entre cuenta, identidad y perfiles académicos.
* Eliminación de referencias necesarias para antecedentes o fichas académicas.
* Dificultad para rastrear la trayectoria de una cuenta.
* Riesgo de afectar permisos durante modificaciones de identidad o perfil.

#### Dependencias

* Coordinación obligatoria con EPIC-001.
* EPIC-004 debe acompañar los procesos que modifiquen varias entidades.
* EPIC-005 depende de una gestión de identidad estable para modularizar el backend sin alterar el comportamiento.

#### Beneficio esperado

* Mayor protección de la información académica.
* Menor riesgo de pérdida de relaciones históricas.
* Gestión coherente de una persona con uno o más perfiles.
* Base segura para evolucionar estudiantes, profesores y acceso.

#### Impacto para usuarios

**Alto.**

Afecta el acceso al sistema, la continuidad de las cuentas y la conservación de antecedentes personales y académicos.

#### Prioridad

**Crítica**

#### Esfuerzo relativo

**L**

#### Criterios de éxito

* La relación entre cuenta, persona y perfiles académicos se mantiene consistente.
* Los procesos de alta, modificación y retiro no dejan relaciones incompletas.
* La gestión del ciclo de vida de la cuenta evita pérdida accidental de información asociada.
* La modernización mantiene el acceso de cuentas válidas.
* Las operaciones sobre una identidad no eliminan perfiles o permisos no comprendidos en el cambio.
* Los antecedentes académicos permanecen vinculados correctamente.

### EPIC-003 — Separación segura entre estados académicos y roles de acceso

#### Objetivo

Evitar que la evolución del estado académico de un estudiante modifique de forma accidental sus roles, permisos u otras participaciones en el sistema.

#### Problema confirmado

Los permisos están mezclados con estados académicos y existe una posible pérdida de roles durante cambios de estado. El sistema implementa cambios de estado académico y reconstrucción de permisos.

Los estados confirmados son postulante, aceptado, matriculado, graduado, retirado, eliminado y reprobado. No se asumen en esta épica transiciones institucionales que no estén confirmadas.

#### Evidencia

* Entidad `tipo_estudiante`.
* Proceso existente de cambio de estado académico.
* Proceso existente de reconstrucción de permisos.
* Permisos mezclados con estados académicos.
* Posible pérdida de roles durante cambios de estado.
* Una persona puede participar académicamente de distintas formas que no son necesariamente roles de seguridad.

#### Riesgo de no realizarla

* Pérdida de acceso legítimo después de graduación, retiro u otro cambio.
* Conservación de accesos que ya no correspondan.
* Desaparición de roles independientes del perfil de estudiante.
* Efectos secundarios sobre profesores, comité académico o administradores que también posean otro perfil.
* Inconsistencias entre estado académico, sesión y permisos reconstruidos.

#### Dependencias

* EPIC-001 habilita la definición coherente de permisos.
* EPIC-002 habilita la preservación correcta de las identidades y perfiles.
* EPIC-004 protege la consistencia del cambio cuando afecta varias entidades.

#### Beneficio esperado

* Evolución académica sin pérdida accidental de acceso.
* Menor acoplamiento funcional entre situación académica y seguridad.
* Mayor previsibilidad en procesos de cambio de estado.
* Protección de usuarios que poseen más de una condición o participación.

#### Impacto para usuarios

**Alto.**

Impacta especialmente a estudiantes cuyo estado cambia y a personas que simultáneamente tienen otros permisos o perfiles.

#### Prioridad

**Crítica**

#### Esfuerzo relativo

**L**

#### Criterios de éxito

* Un cambio de estado modifica únicamente los efectos explícitamente asociados a dicho cambio.
* Los permisos independientes del estado académico permanecen intactos.
* Ninguna participación académica se elimina por ser confundida con un rol de seguridad.
* El inicio de una nueva sesión refleja correctamente el resultado del cambio.
* Los estados existentes continúan siendo reconocidos.
* No se introducen transiciones de estado no confirmadas.

### EPIC-004 — Integridad transaccional de las operaciones compuestas

#### Objetivo

Garantizar que los procesos que modifican varias entidades finalicen de manera íntegra o no produzcan cambios parciales.

#### Problema confirmado

Existen operaciones compuestas sin transacciones. Entre los procesos confirmados se encuentran registro de estudiantes y profesores, cambios de estado, reconstrucción de permisos, gestión de antecedentes y eliminación de cuentas.

#### Evidencia

* MySQL como persistencia.
* PDO como mecanismo de acceso a datos.
* Operaciones compuestas sin transacciones como riesgo conocido.
* Existencia de procesos que afectan cuentas, personas, perfiles, permisos y antecedentes.

#### Riesgo de no realizarla

* Creación parcial de personas o perfiles.
* Permisos reconstruidos de forma incompleta.
* Estados modificados sin completar sus operaciones asociadas.
* Cuentas eliminadas mientras permanecen relaciones inconsistentes.
* Datos académicos incompletos o difíciles de recuperar.

#### Dependencias

Puede comenzar en paralelo con EPIC-001 y EPIC-002, pero la aplicación sobre procesos de autorización y estados debe coordinarse con EPIC-003.

Habilita:

* EPIC-005, modernización del backend.
* EPIC-006, robustecimiento de la ficha académica.
* EPIC-007, evolución del frontend con operaciones más confiables.

#### Beneficio esperado

* Mayor consistencia de datos.
* Recuperación segura ante errores.
* Menor probabilidad de registros parciales.
* Base confiable para modernizar procesos críticos.

#### Impacto para usuarios

**Alto, aunque principalmente indirecto.**

Los usuarios experimentarán menos inconsistencias, pérdidas parciales y resultados contradictorios después de una operación.

#### Prioridad

**Crítica**

#### Esfuerzo relativo

**L**

#### Criterios de éxito

* Las operaciones compuestas identificadas tienen límites de consistencia definidos.
* Un error intermedio no deja cambios parciales persistidos.
* Las operaciones exitosas conservan exactamente su comportamiento funcional.
* La consistencia se verifica en cuentas, personas, perfiles, permisos y antecedentes afectados.
* La reversión técnica de una operación incompleta no destruye información válida preexistente.
* No se requieren migraciones destructivas para completar la épica.

## EPIC-008 — Gobierno del Modelo de Datos y Persistencia

### Objetivo

Establecer la capacidad de gobernar la evolución del modelo de datos compartido de la intranet de manera incremental, compatible, verificable y reversible, preservando la integridad y el significado de la información utilizada por autorización, identidad, módulos académicos, ficha académica y reportabilidad.

La EPIC no define un nuevo modelo de datos ni una solución técnica de persistencia. Su propósito estratégico es establecer que la persistencia compartida constituye una línea propia de modernización.

### Problema

El modelo de datos es transversal a todo el sistema y presenta dependencias directas con:

- cuentas e identidades;
- permisos y estados académicos;
- operaciones compuestas;
- lógica PHP;
- ficha académica;
- reportabilidad;
- módulos académicos.

La evaluación arquitectónica confirmó que estos problemas no pueden gobernarse completamente desde una EPIC consumidora, porque ninguna de las EPIC existentes tiene como responsabilidad principal el gobierno y la evolución integral de la persistencia compartida.

Además, se encuentran confirmados los siguientes riesgos relacionados:

- operaciones compuestas sin transacciones;
- posible pérdida de roles;
- dependencia de la ficha académica respecto de múltiples entidades;
- acoplamiento entre procesos PHP, PDO y MySQL;
- necesidad de mantener compatibilidad funcional durante la modernización incremental.

### Alcance

La EPIC incluye, a nivel estratégico:

- gobierno de la evolución del modelo de datos compartido;
- preservación de relaciones e integridad entre entidades;
- compatibilidad entre estructuras persistentes heredadas y módulos modernizados;
- tratamiento incremental de dependencias entre módulos y persistencia;
- capacidad de modificar el esquema de manera incremental sin exigir una sustitución completa;
- coordinación de los datos utilizados por autorización, identidades, estados, antecedentes, ficha académica y reportabilidad;
- criterios de reversión para cambios estructurales;
- coexistencia temporal entre estructuras heredadas y evolucionadas cuando resulte necesaria;
- consistencia semántica de las entidades y relaciones confirmadas;
- reducción progresiva del acoplamiento directo entre procesos PHP y estructuras concretas de persistencia;
- gobernar la evolución del modelo físico sin modificar el modelo conceptual validado del dominio, salvo decisión arquitectónica posterior expresamente aprobada.

### Exclusiones

La EPIC no incluye:

- modernización general del código PHP, responsabilidad de EPIC-005;
- definición de permisos, responsabilidad de EPIC-001;
- gestión del ciclo de vida de cuentas, responsabilidad de EPIC-002;
- definición de estados académicos y su relación con roles, responsabilidad de EPIC-003;
- atomicidad de operaciones compuestas como objetivo principal, responsabilidad de EPIC-004;
- construcción funcional de la ficha académica, responsabilidad de EPIC-006;
- modernización de la interfaz, responsabilidad de EPIC-007;
- definición institucional de reglas de negocio;
- creación anticipada de un modelo de datos futuro;
- selección de ORM, framework o patrón de acceso;
- migraciones destructivas;
- optimizaciones de rendimiento no sustentadas por evidencia;
- diseño funcional de reportes concretos;
- redefinir reglas de negocio;
- redefinir el modelo conceptual validado del dominio;
- redefinir los actores del dominio;
- redefinir la ficha académica.

### Dependencias

**Relación con EPIC-001**

Debe coordinar la evolución de las estructuras que soportan cuentas y permisos, pero no redefine el significado de los permisos.

EPIC-001 entrega los criterios funcionales de autorización que la persistencia debe representar correctamente.

**Relación con EPIC-002**

Debe preservar las relaciones entre `login`, `usuario`, `profesor` y `estudiante`.

EPIC-002 define el comportamiento funcional del ciclo de vida de identidades; EPIC-008 garantiza que la persistencia pueda soportarlo sin pérdida de relaciones.

**Relación con EPIC-003**

Debe soportar la separación persistente entre estados académicos y roles de acceso.

EPIC-003 determina qué conceptos deben permanecer funcionalmente separados.

**Relación con EPIC-004**

Ambas son complementarias:

- EPIC-004 protege la atomicidad y consistencia de una operación.
- EPIC-008 protege la estructura y evolución del modelo persistente.

EPIC-004 no debe absorber EPIC-008, ni EPIC-008 reemplazar la responsabilidad transaccional.

**Relación con EPIC-005**

EPIC-008 habilita parcialmente a EPIC-005.

La modernización del backend debe consumir una persistencia cuya evolución esté gobernada y no crear modelos paralelos incompatibles.

**Relación con EPIC-006**

EPIC-008 habilita directamente el robustecimiento de la ficha académica, porque esta se construye desde múltiples entidades.

EPIC-006 define la confiabilidad de la proyección; EPIC-008 protege la coherencia de sus fuentes persistentes.

**Relación con EPIC-007**

La dependencia es indirecta.

La interfaz debe consumir procesos y datos estables, pero EPIC-007 no condiciona el diseño de la persistencia.

### Beneficios

- Evolución del modelo de datos sin reescritura completa.
- Menor riesgo de inconsistencias entre módulos.
- Reducción progresiva del acoplamiento entre PHP y estructuras concretas de MySQL.
- Mayor seguridad para modernizar autorización e identidades.
- Fuentes más confiables para la ficha académica.
- Mejor base para reportabilidad futura.
- Capacidad de aplicar cambios pequeños y reversibles.
- Menor riesgo de que cada módulo adopte una interpretación distinta de las mismas entidades.
- Posibilidad de separar la evolución del backend de la evolución estructural de la persistencia.
- Mayor trazabilidad del impacto de los cambios sobre datos compartidos.

### Riesgos

- Alterar relaciones utilizadas por procesos heredados no identificados.
- Introducir incompatibilidades entre módulos modernizados y heredados.
- Provocar pérdida o reinterpretación de datos históricos.
- Duplicar temporalmente estructuras sin controlar su sincronización.
- Convertir la EPIC en una reestructuración completa del esquema.
- Mezclar cambios estructurales con redefiniciones no autorizadas de reglas de negocio.
- Diseñar el modelo únicamente desde las necesidades de un consumidor, como autorización o ficha académica.
- Confundir modernización del modelo con optimización prematura.
- Ejecutar cambios destructivos sin mecanismos de reversión.
- Expandir excesivamente el alcance debido al carácter transversal de la persistencia.

### Prioridad

**Alta.**

Debe situarse como la primera EPIC del grupo de prioridad alta, después de las cuatro EPIC críticas existentes y antes de EPIC-005.

### Esfuerzo

**XL.**

El esfuerzo deriva de su alcance transversal y de la coordinación entre múltiples dominios funcionales, no de una intención de reestructurar toda la base de datos en un único ciclo.

El esfuerzo XL corresponde a múltiples incrementos pequeños y reversibles; no implica una implementación monolítica.

### Criterios de éxito

La EPIC podrá considerarse finalizada cuando:

- el modelo de datos pueda evolucionar mediante incrementos pequeños y reversibles;
- los cambios estructurales preserven compatibilidad con los módulos que todavía permanezcan heredados;
- las entidades centrales mantengan un significado coherente entre los distintos módulos;
- las relaciones entre cuenta, identidad, perfiles, permisos y estados no se pierdan durante la evolución;
- las fuentes de la ficha académica puedan modificarse sin producir pérdida o contradicción de antecedentes;
- los cambios de persistencia dispongan de validaciones previas y posteriores;
- exista una estrategia comprobable de reversión para cada incremento estructural;
- no se requieran migraciones destructivas no autorizadas;
- la lógica PHP pueda evolucionar sin depender de duplicar indefinidamente reglas de persistencia;
- la reportabilidad pueda consumir datos coherentes sin redefinir el modelo transaccional desde cada reporte;
- los módulos modernizados y heredados puedan coexistir durante la transición;
- los cambios no introduzcan reglas de negocio no confirmadas;
- la evolución de la persistencia pueda validarse independientemente de la interfaz y del backend que la consumen.

## EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral

### Identificación y estado

- **EPIC:** EPIC-009.
- **Nombre:** Consolidación Funcional de Objetos y CRUD Integral.
- **Clasificación:** [EPIC] [ARQ] [GOV] [CRUD] [VF] [REV] [PERSIST].
- **Estado:** APROBADA.
- **Implementación:** No iniciada.
- **Primer objeto:** Pendiente de selección.
- **Próximo paso:** Inspección dirigida para seleccionar el primer objeto funcional integral.

Esta creación no selecciona un objeto, no crea Feature, AT o Task y no modifica el orden de prioridad de las épicas existentes. EPIC-009 actúa como marco metodológico transversal para los incrementos que comiencen después de su checkpoint de origen.

### Baseline de origen

El punto de separación metodológica previo a EPIC-009 es:

```text
Commit: 955f515ae48da2cfbb3774074ef2edcbfc4beb9b
Mensaje: chore(project): checkpoint state before next epic
```

El checkpoint consolida el estado Git existente antes de adoptar la nueva metodología. No constituye cierre funcional automático de Cursos, Tesis, EPIC-003, EPIC-008 ni de los documentos históricos incluidos en él.

### Hechos confirmados

- La modernización incremental anterior utilizó unidades delimitadas por métodos, llamadas u operaciones aisladas.
- Cada micro-unidad puede requerir inspección, análisis técnico, Task, revisión, implementación, VF, commit y publicación independientes.
- La fragmentación de un mismo objeto incrementa la sobrecarga documental y de integración.
- Validar una sola operación no demuestra que el objeto completo sea funcionalmente coherente.
- Un objeto puede conservar simultáneamente operaciones correctas, defectuosas, ausentes o desconectadas.
- El repositorio contiene objetos que atraviesan interfaz, JavaScript, transporte, endpoints, modelos, persistencia, permisos y consumidores.
- EPIC-008 continúa gobernando contratos de datos y persistencia.
- EPIC-003 continúa gobernando identidad, roles, estados, permisos, capacidades y decisiones institucionales relacionadas.
- ADR-001 continúa gobernando los contratos explícitos de escritura.

### Problema

La división artificial de una entidad funcional en múltiples micro-unidades puede producir una secuencia repetida:

```text
inspección
→ AT
→ Task
→ revisión técnica
→ implementación
→ VF
→ commit
→ publicación
```

por cada método o llamada aislada. Esta granularidad aumenta la carga de coordinación y puede dejar el mismo objeto en un estado parcial, por ejemplo:

```text
CREATE correcto
READ correcto
UPDATE defectuoso
DELETE inexistente
```

Sin una visión integral, una operación validada puede ocultar incompatibilidades en las demás operaciones, en sus contratos o en sus interacciones.

### Objetivo estratégico

Establecer un proceso incremental en el que cada objeto seleccionado sea inspeccionado y modernizado integralmente a través de sus operaciones aplicables e interacciones relevantes, manteniendo alcance controlado, trazabilidad, reversibilidad y compatibilidad funcional.

Antes de declarar finalizado un objeto deberán revisarse, según su existencia:

```text
interfaz
→ JavaScript
→ transporte AJAX/HTTP
→ endpoint
→ modelo
→ persistencia
→ base de datos
→ callers/consumers
→ permisos
→ mensajes
→ comportamiento observable
```

### Unidad funcional preferente

EPIC-009 define el **objeto funcional completo** como unidad preferente de trabajo.

Un objeto representa un concepto funcional coherente cuyas operaciones e interacciones pueden analizarse y evolucionar conjuntamente. Beca, Financiamiento, Institución, Título, Grado y Postdoctorado son ejemplos ilustrativos; esta lista no selecciona ni prioriza el primer objeto.

La unidad preferente reemplaza la selección automática de un método individual o una operación CRUD aislada cuando todas las operaciones pertenecen al mismo objeto y pueden evolucionar de manera segura y reversible.

### Inspección integral del objeto

Toda inspección previa deberá revisar, según existencia y evidencia:

#### Frontend

- vistas, formularios, listas, botones y modales;
- selectores, JavaScript y mensajes visibles;
- identificación y transporte de IDs.

#### Transporte

- AJAX o HTTP;
- parámetros, payload y JSON;
- métodos GET/POST y contratos de identificación.

#### Endpoint

- operaciones y branches;
- validaciones, mensajes y forma de retorno.

#### Modelo

- métodos, firmas, SQL, retornos y helpers.

#### Persistencia

- lecturas, inserciones, actualizaciones y eliminaciones;
- IDs generados, filas afectadas y transacciones.

#### Integraciones

- callers, consumers y módulos dependientes;
- permisos y capacidades relacionadas.

#### Estado funcional

- operaciones correctas o defectuosas;
- operaciones ausentes o desconectadas;
- inconsistencias y comportamiento histórico observable.

### Matriz CRUD obligatoria

Todo objeto seleccionado deberá contar con una matriz sustentada por evidencia del repositorio:

| Operación | Estados permitidos |
| --- | --- |
| CREATE | Existe / Defectuoso / Falta / No aplica / Bloqueado |
| READ | Existe / Defectuoso / Falta / No aplica / Bloqueado |
| UPDATE | Existe / Defectuoso / Falta / No aplica / Bloqueado |
| DELETE | Existe / Defectuoso / Falta / No aplica / Bloqueado |

La pertenencia conceptual de una operación a CRUD no demuestra por sí sola que deba existir. Cada clasificación deberá indicar evidencia y comportamiento observable.

### Regla de CRUD incompleto

Cuando una operación necesaria falte, sea parcial, esté defectuosa o desconectada, utilice contratos incompatibles o no complete su flujo observable, la Task integral del objeto deberá incluir su resolución.

No se podrá declarar terminado un objeto dejando silenciosamente una operación necesaria incompleta.

### Operaciones no aplicables

Una operación podrá clasificarse como **No aplica** únicamente cuando exista evidencia de una regla funcional, política institucional, requisito de seguridad, conservación histórica, decisión arquitectónica o naturaleza del objeto que justifique su ausencia.

EPIC-009 no obliga a implementar artificialmente las cuatro operaciones CRUD. Cuando no exista evidencia suficiente para declarar que una operación no aplica, deberá registrarse como pendiente de definición o bloqueada.

### Operaciones bloqueadas

Una operación necesaria que no pueda resolverse dentro de la Task integral deberá registrarse con esta información mínima:

```text
Objeto:
Operación:
Estado: Bloqueada
Causa:
Evidencia:
Dependencia:
Decisión requerida:
Siguiente artefacto:
```

El objeto permanecerá **No finalizado** hasta resolver el bloqueo o determinar formalmente que la operación no aplica.

### Task integral por objeto

El estándar preferente es:

```text
1 objeto
→ 1 Task integral
```

La Task podrá incluir múltiples métodos, archivos, endpoints, cambios frontend, cambios de modelo y ajustes de persistencia cuando formen una sola unidad funcional coherente.

CREATE, READ, UPDATE y DELETE no se dividirán automáticamente en cuatro Tasks. La Task integral seguirá teniendo un objetivo único: completar coherentemente el objeto dentro del alcance aprobado.

### Límites de agrupación

La agrupación no autoriza mezclar decisiones incompatibles. La ejecución deberá separarse cuando exista:

- una decisión arquitectónica independiente;
- una decisión institucional pendiente;
- un riesgo de seguridad autónomo;
- una migración de esquema de alto riesgo;
- una dependencia externa;
- un bloqueo funcional;
- un objeto distinto;
- necesidad de un ADR propio.

En esos casos se conservará la visión integral del objeto, con división de ejecución y trazabilidad explícitas.

### Excepciones a la Task integral

Una Task puntual será admisible únicamente con justificación registrada, por ejemplo:

- seguridad o producción;
- bug urgente;
- bloqueo externo;
- decisión arquitectónica independiente;
- operación aislada surgida después del cierre integral;
- riesgo excesivo de agrupación.

### Relación con EPIC-008

EPIC-009 no sustituye ni cierra EPIC-008.

- EPIC-009 gobierna la granularidad funcional de la modernización por objeto.
- EPIC-008 gobierna los contratos de datos y la evolución de la persistencia.

Cuando un objeto requiera cambios de persistencia, deberá cumplir las decisiones vigentes de EPIC-008.

### Relación con ADR-001

ADR-001 permanece vigente y sin modificaciones. Cada objeto deberá distinguir entre:

- escritura que solo requiere éxito o fallo;
- escritura que requiere el ID insertado;
- escritura que requiere filas afectadas;
- operación que pueda requerir una transacción.

No se sustituirán mecánicamente todas las escrituras por un mismo contrato.

### Relación con EPIC-003

EPIC-009 no redefine identidad, roles, estados, permisos, capacidades ni reglas institucionales.

Cuando un objeto dependa de estas materias deberá registrar el bloqueo o la dependencia hacia EPIC-003. Una Task CRUD no resolverá silenciosamente esas decisiones.

### Validación funcional integral

La VF se diseñará por objeto y cubrirá todas sus operaciones aplicables:

- CREATE: crear y confirmar persistencia;
- READ: listar o cargar y confirmar datos;
- UPDATE: editar y confirmar persistencia;
- DELETE: eliminar y confirmar el resultado, cuando corresponda;
- recarga, mensajes, errores, integración visual y permisos aplicables.

La VF corresponde exclusivamente al usuario. Codex no ejecutará operaciones funcionales reales sin una autorización posterior explícita.

### Criterio de cierre del objeto

Un objeto podrá declararse **Finalizado** únicamente cuando:

1. su matriz CRUD esté completa;
2. todas las operaciones aplicables funcionen;
3. los defectos conocidos incluidos en el alcance estén resueltos;
4. las operaciones requeridas ausentes estén implementadas;
5. las operaciones No aplica estén justificadas;
6. los bloqueos estén resueltos;
7. frontend y backend sean coherentes;
8. los contratos técnicos estén conformes;
9. callers y consumers relevantes estén revisados;
10. la VF integral esté aprobada;
11. la integración Git esté publicada;
12. la documentación de cierre esté actualizada.

### Prohibición de deuda oculta

No podrá registrarse un objeto como terminado cuando una operación necesaria continúe defectuosa o ausente, o cuando frontend y backend permanezcan incompatibles.

Toda deuda dentro del objeto deberá quedar resuelta o formalmente registrada como bloqueada o no aplicable.

### Priorización de objetos

La selección posterior deberá ponderar:

1. beneficio funcional;
2. riesgo;
3. tamaño del CRUD;
4. claridad institucional;
5. dependencias;
6. posibilidad de VF;
7. acoplamiento;
8. contratos de persistencia;
9. defectos existentes;
10. cantidad de interacciones.

La cantidad de llamadas a `ejecutarConsulta()` o de métodos heredados no será por sí sola un criterio de prioridad.

Esta creación no altera el orden por prioridad de las épicas existentes. La priorización inicial de objetos se realizará mediante una inspección posterior específica.

### Reversibilidad y alcance

Aunque una Task integral abarque varias capas, deberá seguir siendo acotada, trazable, reversible y revisable.

EPIC-009 no autoriza refactorizaciones generales, reescrituras masivas, reformateo, reorganización oportunista, cambio de framework ni cambios no relacionados.

Para EOL y EOF se preservará la representación material del worktree antes y después. El blob Git no introducirá normalización adicional respecto de su padre cuando intervengan `text=auto` o `core.autocrlf=true`.

### Seguridad y datos locales

Los dumps de base de datos que contengan datos personales, credenciales o información sensible no se incorporarán automáticamente al repositorio.

No se documentarán valores sensibles. El dump local excluido antes de EPIC-009 no forma parte de esta épica ni de sus artefactos.

### Flujo metodológico

EPIC-009 formaliza el flujo:

```text
EPIC
→ selección del objeto
→ inspección integral
→ matriz CRUD
→ análisis técnico integral
→ Task integral
→ revisión técnica
→ implementación supervisada
→ VF integral del objeto
→ commit
→ publicación
→ cierre documental del objeto
```

Las micro-Tasks se reducirán cuando la agrupación no aumente indebidamente el riesgo.

### Métrica de avance

La métrica principal será **Objetos funcionales finalizados**.

Cada objeto deberá registrar:

| Dimensión | Estados |
| --- | --- |
| CRUD | Completo / Parcial / Bloqueado |
| VF | Pendiente / Aprobada |
| Estado | No iniciado / En análisis / En implementación / En VF / Finalizado / Bloqueado |

La cantidad de métodos migrados no será el indicador principal de progreso.

### Fuera de alcance

EPIC-009 no autoriza por sí sola:

- reemplazar PHP o cambiar de framework;
- adoptar una nueva base de datos;
- realizar una reescritura global;
- crear una API general nueva;
- reemplazar totalmente jQuery;
- ejecutar un rediseño visual general;
- definir nuevas reglas institucionales;
- eliminar masivamente código heredado;
- ejecutar cambios generales de esquema;
- modificar ADR-001, EPIC-003 o EPIC-008;
- incorporar dumps sensibles.

### Bloqueos iniciales

No existe un bloqueo para la creación documental de EPIC-009. La selección del primer objeto permanece pendiente y no se resolverá sin una inspección dirigida.

Una dependencia o decisión institucional detectada durante la inspección de un objeto deberá registrarse como bloqueo de ese objeto, no resolverse como supuesto.

### Decisiones adoptadas

- La unidad funcional preferente será el objeto completo.
- Cada objeto contará con una matriz CRUD basada en evidencia.
- Una operación necesaria incompleta deberá resolverse en la Task integral.
- Una operación No aplica requerirá evidencia.
- Una operación bloqueada mantendrá abierto el objeto.
- La Task integral por objeto será el estándar preferente.
- La VF será integral y ejecutada por el usuario.
- El cierre exigirá integración Git publicada y documentación actualizada.

### Reglas de EPIC-009

- La matriz CRUD deberá basarse en evidencia del repositorio.
- Toda operación necesaria incompleta deberá resolverse dentro de la Task integral o registrarse como bloqueada.
- La clasificación No aplica exigirá justificación verificable.
- Un bloqueo mantendrá el objeto abierto.
- La agrupación por objeto será preferente, pero no absorberá decisiones autónomas ni objetos diferentes.
- Toda excepción que requiera una Task puntual deberá quedar justificada y trazada.
- La VF integral será ejecutada por el usuario.
- Ningún objeto se cerrará antes de publicar su integración Git y actualizar su documentación.

### Dependencias

- EPIC-008 para contratos de datos y persistencia.
- ADR-001 para distinguir contratos de escritura.
- EPIC-003 cuando intervengan identidad, roles, estados, permisos, capacidades o decisiones institucionales.
- Otras épicas vigentes según las capas e interacciones concretas del objeto seleccionado.

### Próximo paso

Realizar una inspección dirigida para seleccionar el primer objeto funcional integral. Esa inspección deberá comparar candidatos con los criterios definidos, sin anticipar la selección en este documento.

### Auditoría metodológica

- EPIC-009 no sustituye EPIC-008.
- EPIC-009 no sustituye EPIC-003.
- EPIC-009 no modifica ADR-001.
- EPIC-009 introduce la unidad funcional por objeto y el cierre CRUD integral.
- No se seleccionó el primer objeto.
- No se creó Feature, AT o Task.
- No se modificó código, base de datos ni configuración.

### EPIC-005 — Modernización incremental del backend PHP

#### Objetivo

Evolucionar progresivamente el backend heredado para que sus responsabilidades sean más controlables, verificables y modificables, manteniendo el comportamiento funcional existente.

#### Problema confirmado

La aplicación PHP heredada posee modelos bajo `src/Model` y puntos de entrada distribuidos en directorios como `admin`, `ajax`, `fetchapi` y `form-doc`. Los procesos de acceso, cuentas, personas y gestión académica se encuentran desplegados sobre esta estructura.

#### Evidencia

* Aplicación PHP heredada.
* Autoload PSR-4 para `App\`.
* Modelos bajo `src/Model`.
* Puntos de entrada distribuidos.
* Persistencia MySQL mediante PDO.
* Diversos procesos funcionales que comparten entidades centrales.

#### Riesgo de no realizarla

* Mayor dificultad para modificar procesos sin afectar otros flujos.
* Crecimiento de la dispersión de responsabilidades.
* Repetición de comportamientos críticos en distintos puntos de entrada.
* Modernización desigual entre módulos.
* Aumento del costo y riesgo de cada cambio futuro.

#### Dependencias

La ejecución sobre procesos críticos debe realizarse después de establecer bases suficientes en:

* EPIC-001: autorización.
* EPIC-002: identidades.
* EPIC-004: integridad transaccional.

Puede avanzar primero sobre un módulo piloto de bajo riesgo, una vez que dicho módulo pueda seleccionarse con evidencia.

Habilita:

* EPIC-006.
* EPIC-007.
* Modernizaciones funcionales posteriores de los módulos académicos.

#### Beneficio esperado

* Cambios más localizados y controlables.
* Mayor facilidad para verificar regresiones.
* Evolución gradual sin reescritura completa.
* Reducción progresiva del riesgo técnico del sistema heredado.
* Mejor capacidad para reutilizar comportamientos comunes.

#### Impacto para usuarios

**Bajo durante la ejecución y alto a largo plazo.**

La modernización debe preservar el comportamiento visible, pero permitirá entregar mejoras posteriores con menor riesgo.

#### Prioridad

**Alta**

#### Esfuerzo relativo

**XL**

#### Criterios de éxito

* La evolución se realiza por módulos y con compatibilidad funcional.
* Los procesos modernizados mantienen sus entradas, resultados y reglas confirmadas.
* Cada incremento puede desplegarse y revertirse de forma independiente.
* Las responsabilidades críticas no se duplican al modernizar nuevos módulos.
* Los puntos de entrada existentes continúan operando mientras corresponda.
* No se requiere una reescritura total del sistema.

### EPIC-006 — Robustecimiento de la ficha académica y sus fuentes de información

#### Objetivo

Aumentar la confiabilidad de la construcción de la ficha académica, preservando su condición de proyección integrada desde múltiples entidades.

#### Problema confirmado

La ficha académica depende de múltiples entidades, situación identificada como riesgo. El sistema gestiona antecedentes como publicaciones, congresos, proyectos, tesis, grados académicos, postdoctorados, becas, pasantías y líneas de investigación.

#### Evidencia

* La ficha académica es una proyección, no una entidad aislada.
* Su construcción es un proceso implementado.
* Depende de múltiples entidades.
* Existen módulos confirmados que aportan antecedentes académicos.
* La dependencia múltiple está registrada expresamente como riesgo.

#### Riesgo de no realizarla

* Fichas incompletas o contradictorias.
* Dificultad para identificar la fuente de un dato incorrecto.
* Regresiones al modificar cualquiera de los módulos que la alimentan.
* Resultados inconsistentes entre antecedentes registrados y ficha mostrada.
* Mayor riesgo en futuras mejoras de reportabilidad.

#### Dependencias

* EPIC-004, para asegurar consistencia en la actualización de antecedentes.
* EPIC-005, para permitir una evolución controlada del proceso de construcción.
* Coordinación con EPIC-001 cuando la ficha muestre información condicionada por permisos.

#### Beneficio esperado

* Mayor confiabilidad de la información académica consolidada.
* Menor riesgo al modernizar módulos de antecedentes.
* Base más estable para reportes relacionados con trayectoria académica.
* Mejor trazabilidad funcional entre dato de origen y proyección.

#### Impacto para usuarios

**Alto.**

La ficha académica concentra información relevante para estudiantes, profesores, comité académico y administración.

#### Prioridad

**Alta**

#### Esfuerzo relativo

**XL**

#### Criterios de éxito

* La ficha conserva todos los antecedentes confirmados que correspondan.
* Las modificaciones en una fuente no eliminan datos provenientes de otras.
* Los datos mostrados pueden asociarse consistentemente con sus entidades de origen.
* Los resultados permanecen funcionalmente compatibles con la ficha actual validada.
* Los errores parciales de una fuente no producen modificaciones inconsistentes en otras.
* Los módulos que alimentan la ficha pueden evolucionar incrementalmente.

### EPIC-007 — Modernización progresiva de la interfaz web

#### Objetivo

Evolucionar la experiencia de uso de la intranet de forma incremental, por módulos y sin modificar las reglas de negocio ni exigir una sustitución completa de la interfaz.

#### Problema confirmado

El sistema es una intranet web heredada con múltiples módulos funcionales y puntos de entrada distribuidos. Existe una interfaz web, pero el contexto recibido no confirma tecnologías frontend específicas; por tanto, esta épica no selecciona framework, biblioteca ni arquitectura de interfaz.

#### Evidencia

* Existencia de una interfaz web.
* Sistema PHP heredado.
* Módulos funcionales numerosos y diferenciados.
* Principio explícito de modernización incremental.
* Exigencia de compatibilidad funcional, cambios pequeños, reversibles y verificables.

#### Riesgo de no realizarla

* Experiencia de uso desigual entre módulos modernizados y heredados.
* Mayor dificultad para incorporar mejoras de interacción.
* Acumulación de comportamiento de interfaz difícil de mantener.
* Incremento del costo de futuros cambios visibles para usuarios.

No se afirma que exista actualmente un problema específico de accesibilidad, rendimiento o compatibilidad de navegadores, porque esos problemas no están confirmados en el contexto.

#### Dependencias

* EPIC-005 debe proporcionar procesos backend suficientemente estables para el módulo que se modernice.
* EPIC-001 debe asegurar que la interfaz no exponga operaciones fuera de los permisos correspondientes.
* EPIC-004 debe proteger las operaciones compuestas ejecutadas desde la interfaz.

Puede ejecutarse gradualmente en paralelo con EPIC-006 después de disponer de un módulo piloto validado.

#### Beneficio esperado

* Interacciones más consistentes.
* Evolución visual y funcional controlada.
* Menor dependencia de una sustitución completa.
* Capacidad de modernizar un módulo sin interrumpir los restantes.

#### Impacto para usuarios

**Alto y directo.**

Es la épica con mayor visibilidad, aunque no debe ser la primera debido a sus dependencias.

#### Prioridad

**Media**

#### Esfuerzo relativo

**XL**

#### Criterios de éxito

* La interfaz se moderniza por módulos, no mediante una reescritura total.
* Cada incremento conserva las reglas y resultados funcionales existentes.
* Los usuarios pueden seguir utilizando los módulos no modernizados durante la transición.
* Los controles visibles respetan los mismos permisos que el backend.
* Cada módulo modernizado puede revertirse sin afectar los demás.
* La tecnología objetivo, cuando se decida, se basa en evidencia técnica específica y no en una selección anticipada.

# Orden por prioridad

| Orden | Épica                                        | Prioridad | Esfuerzo |
| ----: | -------------------------------------------- | --------- | -------- |
|     1 | EPIC-001 — Modelo de autorización y permisos | Crítica   | XL       |
|     2 | EPIC-002 — Gestión de identidades y cuentas  | Crítica   | L        |
|     3 | EPIC-003 — Estados académicos y roles        | Crítica   | L        |
|     4 | EPIC-004 — Integridad transaccional          | Crítica   | L        |
|     5 | EPIC-008 — Gobierno de datos y persistencia  | Alta      | XL       |
|     6 | EPIC-005 — Backend PHP                       | Alta      | XL       |
|     7 | EPIC-006 — Ficha académica                   | Alta      | XL       |
|     8 | EPIC-007 — Interfaz web                      | Media     | XL       |

El orden dentro del grupo crítico expresa la secuencia de reducción de riesgo, pero no obliga a completar íntegramente una épica antes de iniciar cualquier trabajo de la siguiente.

# Justificación del orden

## 1. Primero se protege el acceso

La autorización es transversal: cualquier modernización de backend, interfaz, ficha académica o módulos funcionales debe mantener quién puede acceder a cada operación. Modernizar primero la presentación o los procesos internos sin controlar este riesgo podría conservar o ampliar errores de acceso.

## 2. Después se estabiliza la identidad

Los permisos se aplican sobre cuentas asociadas a personas y perfiles académicos. Por ello, la gestión de identidad debe evolucionar junto con la autorización para evitar que una mejora en permisos se apoye sobre cuentas o perfiles inconsistentes.

## 3. Se desacopla el estado académico del acceso

El cambio de estado es un riesgo explícito porque puede provocar pérdida de roles. Esta línea debe resolverse antes de modernizar ampliamente los módulos de estudiantes o sus flujos asociados.

## 4. Se protege la consistencia de las operaciones

Las operaciones compuestas sin transacciones pueden dejar el sistema en estados parciales. Antes de ampliar cambios sobre backend, ficha académica o interfaz, las operaciones críticas necesitan garantías de integridad.

## 5. Se gobierna la evolución de la persistencia

El modelo de datos compartido debe poder evolucionar de forma compatible, verificable y reversible antes de ampliar la modernización del backend y de sus módulos consumidores.

## 6. Se moderniza el backend de forma gradual

Una vez controlados acceso, identidad y consistencia, el backend puede evolucionar módulo por módulo con menor riesgo de alterar reglas transversales.

## 7. Se fortalece la ficha académica

La ficha concentra información de múltiples entidades. Abordarla antes de estabilizar sus fuentes y procesos aumentaría el riesgo de introducir inconsistencias difíciles de detectar.

## 8. La interfaz se moderniza sobre procesos estables

La modernización frontend tiene alto impacto visible, pero debe apoyarse en permisos y operaciones backend confiables. De lo contrario, se corre el riesgo de crear una presentación nueva sobre comportamientos aún frágiles.

# Épicas habilitadoras y dependencias

```text
EPIC-001 Autorización ───────────────┐
                                     ├──> EPIC-005 Backend
EPIC-002 Identidad ────────┐         │
                           ├─> EPIC-003 Estados y roles
EPIC-001 Autorización ─────┘         │
                                     │
EPIC-004 Integridad transaccional ───┘

EPIC-008 Persistencia ──────────────────> EPIC-005 Backend

EPIC-004 + EPIC-005 + EPIC-008 ────────> EPIC-006 Ficha académica

EPIC-001 + EPIC-004 + EPIC-005 ───────> EPIC-007 Interfaz web
```

Las principales épicas habilitadoras son:

* **EPIC-001**, porque los permisos atraviesan todo el sistema.
* **EPIC-004**, porque protege la consistencia de las operaciones.
* **EPIC-008**, porque gobierna la evolución de la persistencia compartida.
* **EPIC-005**, porque proporciona la base para modernizar módulos funcionales y la interfaz de manera controlada.

# Estrategia de ejecución incremental por olas

## Ola 1 — Contención de riesgos críticos

Iniciar EPIC-001, EPIC-002 y EPIC-004 sobre sus ámbitos transversales. EPIC-003 puede comenzar en paralelo cuando se trabaje específicamente con cambios de estado y reconstrucción de permisos.

El objetivo de esta ola no es reemplazar el sistema, sino evitar que la modernización posterior amplifique riesgos existentes.

## Ola 2 — Modernización de un flujo vertical piloto

Seleccionar un módulo delimitado y modernizarlo como un recorrido completo:

* comportamiento backend;
* acceso a datos;
* permisos;
* operación transaccional, cuando corresponda;
* interfaz, solo si su comportamiento actual está suficientemente determinado.

Esta ola debe producir un patrón reutilizable, no una arquitectura global impuesta anticipadamente.

## Ola 3 — Expansión a módulos relacionados

Aplicar el patrón validado a módulos con dependencias semejantes, manteniendo cada incremento independiente y reversible.

No se recomienda comenzar por la ficha académica, porque integra múltiples entidades y concentra uno de los riesgos explícitos del sistema.

## Ola 4 — Ficha académica e integración transversal

Abordar EPIC-006 cuando sus principales módulos fuente y operaciones de persistencia hayan alcanzado suficiente estabilidad.

## Ola 5 — Modernización progresiva de la experiencia completa

Extender EPIC-007 al resto de la intranet, conservando convivencia temporal entre módulos heredados y modernizados.

# Estado de la selección del módulo piloto

**No existe evidencia suficiente para recomendar responsablemente un módulo piloto concreto.**

El contexto confirma los módulos existentes, pero no permite comparar cuál posee menor riesgo técnico y funcional. Elegir ahora cursos, catálogos, calendario u otro módulo sería una inferencia no respaldada.

# Información necesaria para seleccionar el piloto

Para cada módulo candidato se necesita conocer:

1. Cantidad y tipo de puntos de entrada que utiliza.
2. Entidades y tablas que modifica.
3. Dependencias con `login`, `usuario`, `profesor`, `estudiante`, `permiso_login` y `tipo_estudiante`.
4. Participación en la construcción de la ficha académica.
5. Uso de operaciones compuestas.
6. Dependencia de permisos distribuidos.
7. Cantidad de flujos de alta, modificación, consulta y eliminación.
8. Frecuencia y criticidad de uso.
9. Efecto operativo de una regresión.
10. Posibilidad de habilitar y revertir el módulo de forma aislada.
11. Dependencias con reportes u otros módulos.
12. Existencia de comportamiento observable que permita comparar antes y después.

## Criterio para minimizar el riesgo

El mejor piloto será el módulo confirmado que presente simultáneamente:

* alcance funcional acotado;
* pocas dependencias con identidad, estados y ficha académica;
* ausencia de operaciones destructivas críticas;
* permisos simples y verificables;
* baja criticidad operacional;
* posibilidad de reversión aislada;
* suficiente uso real para validar el patrón de modernización.

Con el contexto disponible, **Acceso, Estudiantes, Profesores y Ficha académica deben descartarse provisionalmente como primeros pilotos**, no porque se conozca toda su complejidad, sino porque están directamente vinculados a los riesgos confirmados de autorización, identidad, estados, permisos y dependencia múltiple. La selección entre los demás módulos requiere la comparación concreta indicada.

# Alcance deliberadamente no incluido

No se crean épicas independientes de infraestructura, observabilidad, rendimiento o reportabilidad porque el contexto confirma su existencia parcial o funcional, pero no confirma problemas concretos suficientes para definir objetivos de modernización separados sin introducir supuestos.

La estrategia resultante concentra el roadmap en los riesgos técnicamente confirmados:

1. autorización;
2. identidad;
3. estados y preservación de roles;
4. integridad transaccional;
5. backend heredado;
6. ficha académica;
7. interfaz web incremental.

# Observaciones arquitectónicas futuras

Se registran para evaluación posterior las siguientes observaciones:

* Posible separación entre arquitectura backend y acceso a datos.
* Posible incorporación de una épica de calidad y pruebas.
* Posible incorporación de una épica de observabilidad.

Estas observaciones todavía no forman parte del roadmap aprobado.
