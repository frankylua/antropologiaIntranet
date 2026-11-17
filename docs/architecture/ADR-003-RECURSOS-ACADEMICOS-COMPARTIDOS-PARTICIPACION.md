# ADR-003 — Recursos Académicos Compartidos y Participación

## Estado

```text
Propuesto.
Pendiente de aprobación arquitectónica.
Sin implementación asociada.
```

## Identificación

- **Clasificación:** [ARQ] [AUTH] [DATA] [SEC] [GOV].
- **EPIC principal:** EPIC-009 — Consolidación Funcional de Objetos y CRUD Integral.
- **Coordinación:** EPIC-003 — Identidad y autorización; EPIC-008 — Persistencia.
- **Aplicación inicial confirmada:** Publicación, Congreso, Proyecto y Tesis.
- **Fecha:** 2026-09-12.
- **Tipo de decisión:** gobierno arquitectónico transversal previo a la inspección y cierre integral de objetos compartidos.

Este ADR no autoriza código, cambios de base de datos, migraciones, Tasks ni
modificaciones de objetos funcionales. Tampoco modifica el Roadmap ni redefine
EPIC-003.

## 1. Contexto

La modernización incremental de EPIC-009 distingue dos configuraciones
funcionales que no deben recibir automáticamente el mismo modelo de
autorización:

1. antecedentes individuales, como Grado y Postdoctorado según la evidencia
   actual;
2. antecedentes compartidos en los que varias personas pueden participar en un
   mismo recurso, como Publicación, Congreso, Proyecto y Tesis.

En los antecedentes compartidos, la existencia de una relación con el recurso
no demuestra propiedad ni autoridad sobre el recurso completo. Confundir
participación con ownership global puede habilitar IDOR, modificación de datos
de terceros, eliminación de relaciones ajenas o destrucción del antecedente
compartido.

ADR-002 ya distingue identidad, perfil, rol institucional, participación
académica y objeto académico específico. Este ADR conserva esa separación y
define cómo debe aplicarse a la autorización de recursos compartidos, sin
seleccionar un modelo físico.

## 2. Problema

Un único antecedente académico puede contener simultáneamente:

- datos globales del recurso;
- una o más participaciones individuales;
- roles o funciones diferentes por participante;
- actores internos identificables por el sistema;
- actores externos mantenidos como información descriptiva;
- operaciones administrativas con impacto sobre terceros.

Una comprobación basada sólo en el rol general del actor o en que su ID aparece
en alguna relación no permite decidir correctamente quién puede leer, modificar
o eliminar cada parte.

La autorización debe distinguir expresamente:

```text
recurso global
≠
participación individual
≠
rol dentro de la participación
≠
administración global
```

## 3. Decisión arquitectónica

Se adopta como patrón obligatorio para los objetos inicialmente comprendidos
la autorización combinada por identidad, acción, recurso y relación con el
recurso.

```text
actor autenticado
+ acción solicitada
+ recurso objetivo
+ relación del actor con el recurso
+ regla institucional específica del objeto
→ decisión backend
```

La mera participación no concede propiedad global. La mera creación inicial
del recurso tampoco concede autoridad permanente e ilimitada sobre éste.

Toda autorización efectiva debe comprobarse en backend para cada request. La
interfaz puede reflejar la decisión, pero ocultar controles no constituye una
frontera de seguridad.

Este ADR no introduce un motor externo de autorización. Cada objeto debe
integrar el patrón con los mecanismos vigentes y con la matriz institucional
que resulte aprobada para ese objeto.

## 4. Recurso global y participación

### 4.1 Recurso global

El recurso global representa el antecedente compartido y los datos que afectan
al conjunto de sus participantes. Su ciclo de vida y sus operaciones deben
analizarse separadamente de las relaciones individuales.

La inspección de cada objeto determinará, mediante evidencia de código y
persistencia, qué atributos pertenecen realmente al recurso global.

### 4.2 Participación como entidad funcional

Cuando el modelo real disponga de una tabla o entidad de participación, la
participación se tratará como un recurso funcional con ciclo de vida, ownership
y autorización propios.

La participación puede contener, según el objeto real:

- rol;
- función;
- tipo de participación;
- datos propios de la contribución;
- atributos específicos del participante.

No debe reducirse conceptualmente a una FK. Esta regla no ordena crear una tabla
de participación donde no exista: cualquier modificación física requerirá la
inspección, decisión y autorización correspondientes.

### 4.3 Ownership

La autoridad ordinaria de un participante se evalúa sobre su propia
participación. Ser participante no autoriza automáticamente a:

- actualizar el recurso global completo;
- eliminar el recurso global;
- modificar participaciones de terceros;
- eliminar participaciones de terceros;
- cambiar roles ajenos.

El ownership debe resolverse mediante identidad interna y relación persistente
cuando ambas existan. Nunca debe derivarse sólo de IDs suministrados por el
cliente.

## 5. Roles institucionales y relación con el recurso

La matriz efectiva debe combinar RBAC institucional con la relación concreta:

| Dimensión institucional | Relación con el recurso |
| --- | --- |
| Admin | Administrador global, participante o actor ajeno |
| Comité | Administrador global, participante o actor ajeno |
| Profesor | Propietario de participación, participante o actor ajeno |
| Estudiante | Propietario de participación, participante o actor ajeno |

Admin y Comité podrán ejercer administración global únicamente cuando la
matriz institucional específica del objeto lo apruebe. La sola presencia del
rol no sustituye la validación backend de identidad, acción, recurso y alcance.

Para un participante normal, la referencia mínima es:

| Operación | Regla conceptual |
| --- | --- |
| READ recurso global | Según matriz específica del objeto |
| READ participación propia | Permitido cuando exista acceso vigente al recurso |
| UPDATE participación propia | Según matriz específica del objeto |
| DELETE participación propia | Según matriz específica del objeto |
| UPDATE recurso global | No automático |
| DELETE recurso global | No automático |
| UPDATE participación ajena | Denegado |
| DELETE participación ajena | Denegado |

Las matrices específicas pueden ser más restrictivas. No pueden ampliar
privilegios por inferencia desde el comportamiento heredado.

## 6. Actores internos y externos

### 6.1 Actores internos

Cuando el participante existe en el sistema, debe utilizarse su identidad
interna válida —`id_usuario` o equivalente confirmado por la inspección— para:

- ownership;
- autorización;
- auditoría;
- validación de integridad.

El backend debe derivar o contrastar esa identidad con la sesión. Un ID recibido
del cliente no constituye identidad autenticada.

### 6.2 Actores externos

Cuando una persona participa en un antecedente sin existir como usuario, puede
mantenerse temporalmente como dato descriptivo textual si el modelo actual ya
lo soporta.

Son ejemplos un autor externo, un coautor externo u otro participante externo.
Esa representación textual:

- no crea identidad;
- no crea cuenta;
- no concede permisos;
- no participa en ownership;
- no puede utilizarse como credencial ni como prueba de autorización.

No se crearán usuarios automáticamente para participantes externos.

## 7. Entidad Persona

Este ADR no introduce una entidad Persona transversal ni ordena crear su tabla.
La distinción conceptual de Persona registrada por ADR-002 permanece vigente,
pero no se convierte aquí en solución física.

Una evolución posterior podrá evaluarla únicamente si las inspecciones de
EPIC-009 demuestran que las mismas personas externas se reutilizan de forma
estructurada en varios objetos y que la representación textual produce
duplicación o inconsistencia significativa.

Esa evolución requerirá una decisión específica posterior sobre identidad y
modelo de datos.

## 8. Creación del recurso compartido

Cualquier actor podrá crear inicialmente un recurso compartido sólo cuando la
matriz específica del objeto lo autorice.

La creación inicial no otorga propiedad permanente e ilimitada. Si se asocian
otros participantes, el recurso adquiere impacto compartido y las operaciones
posteriores deben evaluarse con la matriz y el modelo real del objeto, no sólo
con la identidad de quien creó la primera fila.

La inspección deberá determinar si existe un actor principal, cómo puede
cambiar y qué efectos produce ese cambio sobre el resto de participantes.

## 9. Eliminación

### 9.1 DELETE de participación

Eliminar una participación propia no debe eliminar automáticamente:

- el recurso global;
- participaciones ajenas;
- antecedentes de terceros.

DELETE de participación y DELETE del recurso global son operaciones distintas,
con autorización, validación y respuestas independientes.

### 9.2 DELETE global

DELETE del recurso compartido es una operación administrativa separada. Antes
de autorizarla, la inspección y la Task específica deben evaluar:

- participaciones existentes;
- dependencias y efectos sobre terceros;
- FK y reglas referenciales vigentes;
- comportamiento CASCADE;
- necesidad de RESTRICT;
- necesidad de archivo o borrado lógico;
- trazabilidad requerida.

No se adopta CASCADE silencioso como regla general. Este ADR tampoco selecciona
por anticipado borrado físico, lógico, archivo o RESTRICT para ningún objeto.

## 10. Concurrencia

Cuando más de un actor administrativo pueda editar datos globales, la
inspección del objeto debe evaluar el riesgo real de lost update.

Si el riesgo está confirmado, el AT podrá considerar optimistic locking
mediante versión o timestamp. Este ADR no impone ese mecanismo globalmente ni
autoriza añadir campos sin inspección del schema y de los contratos actuales.

## 11. Auditoría

Las operaciones de alto impacto deben evaluar trazabilidad adicional,
especialmente:

- cambio de actor principal;
- cambio de roles de participación;
- eliminación de participación;
- eliminación global;
- modificación de datos compartidos.

La necesidad, granularidad, retención y persistencia de auditoría se decidirán
en la inspección y matriz institucional de cada objeto. Este ADR no crea un
sistema transversal de auditoría.

## 12. Reglas específicas para Congreso

### 12.1 Autor y Coautor

Autor y Coautor son roles de participación, no propiedad global del Congreso.
Un Coautor no obtiene por ese rol autoridad automática para modificar o
eliminar el recurso global.

Si un Coautor intenta transformarse en Autor, debe comprobarse que no exista ya
otro Autor en el alcance lógico aplicable.

### 12.2 Autor interno

Cuando el Autor existe como usuario, debe utilizarse una identidad interna
válida. La representación conceptual esperada es:

```text
id_aut != NULL
nombre_autor_externo = NULL
```

Los nombres de campos son ilustrativos y no fijan schema.

### 12.3 Autor externo

Cuando el Autor no existe como usuario, puede conservarse como nombre
descriptivo:

```text
id_aut = NULL
nombre_autor_externo != NULL
```

Esta representación no crea usuario, identidad autenticable ni ownership.

### 12.4 Invariante de Autor

Debe existir como máximo un Autor principal dentro del alcance lógico que
determine la inspección real de Congreso.

La inspección deberá precisar si ese alcance corresponde a:

- el Congreso completo;
- una participación;
- una ponencia;
- una mesa;
- otra unidad real del dominio.

No se fija constraint físico antes de inspeccionar código, schema, datos y
consumers.

### 12.5 Cambio Coautor → Autor

El cambio no puede reemplazar silenciosamente a un Autor existente. Si ya
existe uno, la operación debe rechazarse o tramitarse como reasignación
administrativa explícita, según la matriz específica que se apruebe.

La reasignación es una operación especial y debe evaluar autorización,
integridad, concurrencia, impacto en terceros y auditoría.

### 12.6 Autor y Coautor internos

La misma identidad interna no debe quedar simultáneamente representada como
Autor y Coautor dentro del mismo alcance lógico. La inspección integral de
Congreso determinará cómo se expresa y valida esa invariante en el modelo real.

### 12.7 Otros coautores VARCHAR

Los coautores externos actualmente descriptivos pueden continuar almacenados
como VARCHAR durante la modernización incremental. Ese contenido no concede
permisos, no equivale a usuario y no participa en ownership.

## 13. Aplicación inicial

### 13.1 Publicación

La inspección debe separar datos globales de Publicación y datos propios de
cada autor o coautor. Debe identificar autores internos, coautores internos,
participantes externos, ownership, cambios de rol, eliminación de relaciones e
impacto sobre terceros.

No se aplicará automáticamente el modelo exacto de Congreso cuando el schema o
el ciclo de vida de Publicación difieran.

### 13.2 Congreso

La inspección debe aplicar las reglas de Autor/Coautor de la sección 12 y
determinar el alcance lógico real de la unicidad, las representaciones interna
y externa, la reasignación y las facultades sobre recurso y participación.

### 13.3 Proyecto

La inspección debe separar:

- datos globales del Proyecto;
- investigador principal;
- coinvestigadores;
- participaciones individuales.

Un coinvestigador no adquiere autoridad automática para modificar o eliminar
globalmente el Proyecto.

### 13.4 Tesis

La inspección debe separar:

- Tesis como recurso;
- Estudiante;
- Guía;
- Coguía;
- cotutela;
- otros actores confirmados.

La presencia de varios actores no implica ownership global compartido. La
inspección debe determinar qué actor es autoridad sobre cada relación y qué
operaciones corresponden al recurso global.

## 14. Aplicación obligatoria en EPIC-009

Antes del cierre integral de Publicación, Congreso, Proyecto o Tesis, su
inspección y AT deben incluir una sección titulada:

```text
RECURSO COMPARTIDO Y PARTICIPACIÓN
```

La sección debe resolver expresamente:

1. recurso global;
2. participaciones;
3. actores internos;
4. actores externos;
5. roles;
6. ownership;
7. READ propio y ajeno;
8. UPDATE propio y global;
9. DELETE de participación;
10. DELETE global;
11. concurrencia;
12. auditoría;
13. integridad referencial.

La evidencia de un objeto no se trasladará automáticamente a otro. Cualquier
objeto futuro sólo quedará comprendido después de su propia inspección y de una
decisión explícita de aplicación.

## 15. Grado y Postdoctorado

Grado y Postdoctorado son antecedentes individuales según la evidencia actual.
Este ADR no cambia sus contratos, decisiones ni estados documentales y no
reabre cierres existentes. Tampoco declara cerrado un objeto que permanezca
pendiente por su propio proceso EPIC-009.

## 16. Relación con decisiones vigentes

### ADR-001

Permanece vigente para distinguir contratos de escritura, filas afectadas, ID
insertado y necesidades transaccionales. Este ADR no altera
`ejecutarEscritura()` ni autoriza migraciones de consumidores.

### ADR-002

Permanece vigente como separación conceptual de identidad, perfiles, roles,
participaciones y objetos académicos. ADR-003 precisa el patrón de autorización
por recurso/relación y mantiene la prohibición de crear automáticamente tablas
Persona o Participación.

### EPIC-003

Continúa siendo la autoridad sobre identidad, roles, estados, permisos,
capacidades y reglas institucionales. ADR-003 no redefine esas materias.

### EPIC-008

Continúa gobernando persistencia, integridad referencial, compatibilidad,
migraciones y reversión. ADR-003 no selecciona schema ni política de borrado.

### EPIC-009

ADR-003 es obligatorio como criterio de inspección para los cuatro objetos
iniciales, pero no sustituye su inspección, AT, Task, matriz institucional ni
VF integral.

## 17. Consecuencias

### Consecuencias positivas

- evita convertir participación en privilegio global;
- establece una frontera verificable contra IDOR y exceso de privilegios;
- protege participaciones y antecedentes de terceros;
- permite conservar actores externos sin fabricar identidades;
- obliga a evaluar DELETE global, concurrencia, auditoría e integridad;
- permite que cada objeto conserve su propio modelo y ciclo de vida.

### Costos y riesgos

- cada objeto requiere inspeccionar más de una frontera CRUD;
- pueden necesitarse matrices separadas para recurso y participación;
- los modelos heredados pueden no expresar todavía todas las relaciones;
- una futura corrección física puede requerir coordinación con EPIC-008;
- la administración global necesita decisiones institucionales específicas;
- la representación textual de externos puede mantener duplicación hasta una
  decisión posterior.

## 18. Fuera de alcance

Este ADR no autoriza:

- implementación de código;
- cambios de base de datos o migraciones;
- creación de Tasks;
- creación de una entidad o tabla Persona;
- creación automática de usuarios externos;
- creación obligatoria de una tabla Participación;
- schema concreto para Publicación, Congreso, Proyecto o Tesis;
- motor externo de autorización;
- política global de CASCADE, RESTRICT, borrado físico o lógico;
- optimistic locking o auditoría sin evidencia específica;
- modificación de Grado o Postdoctorado;
- ampliación automática a otros objetos;
- redefinición de EPIC-003, EPIC-008 o EPIC-009.

## 19. Criterios de aplicación

Una inspección futura cumple este ADR cuando:

- delimita recurso y participaciones con evidencia;
- identifica actores internos y externos sin equipararlos;
- define ownership persistente y derivación de identidad backend;
- separa facultades propias, ajenas y globales;
- trata DELETE de participación y DELETE global como operaciones distintas;
- inspecciona FK, CASCADE, RESTRICT y efectos sobre terceros;
- evalúa concurrencia y auditoría sin imponer soluciones anticipadas;
- obtiene las decisiones institucionales específicas que falten;
- incorpora los casos correspondientes en su VF integral.

## 20. Gobierno documental

```text
ADR-003: PROPUESTO
Implementación: NO AUTORIZADA
Migraciones: NO AUTORIZADAS
Tasks: NO CREADAS
Aplicación automática a otros objetos: NO AUTORIZADA
Siguiente paso: revisión y aprobación arquitectónica del ADR
```

## 21. Referencias

- [ADR-001 — Contrato explícito para operaciones de escritura](../adr/ADR-001-contrato-explicito-operaciones-escritura.md)
- [ADR-002 — Evolución del modelo de identidad y participación académica](../adr/ADR-002-evolucion-modelo-identidad-participacion-academica.md)
- [Modelo de dominio actual](DOMAIN_MODEL.md)
- [ROADMAP — EPIC-003, EPIC-008 y EPIC-009](../roadmap/ROADMAP.md)
