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
|     5 | EPIC-005 — Backend PHP                       | Alta      | XL       |
|     6 | EPIC-006 — Ficha académica                   | Alta      | XL       |
|     7 | EPIC-007 — Interfaz web                      | Media     | XL       |

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

## 5. Se moderniza el backend de forma gradual

Una vez controlados acceso, identidad y consistencia, el backend puede evolucionar módulo por módulo con menor riesgo de alterar reglas transversales.

## 6. Se fortalece la ficha académica

La ficha concentra información de múltiples entidades. Abordarla antes de estabilizar sus fuentes y procesos aumentaría el riesgo de introducir inconsistencias difíciles de detectar.

## 7. La interfaz se moderniza sobre procesos estables

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

EPIC-004 + EPIC-005 ───────────────────> EPIC-006 Ficha académica

EPIC-001 + EPIC-004 + EPIC-005 ───────> EPIC-007 Interfaz web
```

Las principales épicas habilitadoras son:

* **EPIC-001**, porque los permisos atraviesan todo el sistema.
* **EPIC-004**, porque protege la consistencia de las operaciones.
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
