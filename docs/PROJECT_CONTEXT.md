# PROJECT CONTEXT

## 1. Propósito

Este repositorio contiene una intranet web heredada en PHP para administrar el acceso, las personas y distintos ámbitos de la actividad académica. El sistema gestiona estudiantes, profesores, cursos, fichas académicas, publicaciones, congresos, proyectos, tesis, grados académicos, postdoctorados, becas, pasantías, líneas de investigación, catálogos, reglamento, calendario y reportes.

La modernización es incremental. Su objetivo es realizar cambios basados en conocimiento validado, con alcance controlado, compatibilidad funcional y trazabilidad documental.

## 2. Estado actual

El proyecto se encuentra en descubrimiento y documentación. El modelo de dominio funcional actual está validado. Los descubrimientos sobre arquitectura inicial, modelo de datos y roles y permisos están en revisión.

El sistema es un proyecto web PHP con persistencia MySQL. Dispone de cuentas, identidades personales, perfiles académicos, permisos y módulos de gestión académica. Persisten contradicciones y preguntas institucionales abiertas que no deben tratarse como reglas confirmadas.

No hay implementación de modernización documentada en las fuentes oficiales consultadas.

## 3. Stack tecnológico

| Categoría | Información confirmada |
| --- | --- |
| Lenguajes | PHP; SQL en el volcado de base de datos. |
| Frameworks | No documentados. |
| Base de datos | MySQL. |
| Frontend | Interfaz web; tecnologías específicas no documentadas. |
| Backend | PHP, autoload PSR-4 para el espacio de nombres `App\\` y modelos bajo `src/Model`. |
| Acceso a datos | PDO. |
| Herramientas | Composer se evidencia mediante `composer.json`; no se documentan otras herramientas. |
| Hosting | No documentado. |

## 4. Arquitectura actual

- **Arquitectura general:** aplicación web PHP heredada con modelos bajo `src/Model` y puntos de entrada distribuidos en directorios como `admin`, `ajax`, `fetchapi` y `form-doc`. La arquitectura completa continúa en revisión.
- **Renderizado:** no está documentado de forma concluyente.
- **Autenticación:** existe inicio de sesión y construcción de sesión sobre cuentas de acceso.
- **Política de sesión:** la aplicación utiliza un bootstrap dedicado que aplica strict mode, cookies-only, HttpOnly y SameSite=Lax antes de todos los `session_start()` publicados. La configuración `Secure` depende del entorno de despliegue y permanece pendiente de evidencia HTTPS productiva.
- **Autorización:** se asignan múltiples permisos a las cuentas; la autorización está distribuida y el alcance efectivo de cada permiso sigue en revisión.
- **Acceso a datos:** MySQL mediante PDO.
- **Frontend:** existe una interfaz web, pero su organización y tecnologías no están documentadas de forma concluyente.

## 5. Modelo de dominio

Los actores principales son estudiante, profesor, comité académico y administrador. Además, existen participaciones académicas contextuales —como profesor guía, coguía, investigador, autor o profesor patrocinante— que no necesariamente constituyen roles de seguridad.

Las entidades centrales incluyen la cuenta de acceso (`login`), la identidad personal (`usuario`), sus especializaciones académicas (`profesor` y `estudiante`), la asignación de permisos (`permiso_login`) y el estado académico del estudiante (`tipo_estudiante`). La ficha académica es una proyección construida desde múltiples entidades.

Los módulos confirmados abarcan acceso, personas, estudiantes, profesores, cursos, ficha académica, publicaciones, congresos, proyectos, tesis, grados académicos, postdoctorados, becas, pasantías, líneas de investigación, catálogos, reglamento, calendario y reportes.

Los procesos implementados confirmados incluyen inicio y construcción de sesión, registro de estudiantes y profesores, cambio de estado académico, reconstrucción de permisos, gestión de cursos y antecedentes, construcción de ficha académica y eliminación física de cuentas. Los estados de estudiante confirmados son postulante, aceptado, matriculado, graduado, retirado, eliminado y reprobado; sus transiciones institucionales aún no están validadas.

## 6. Estado del descubrimiento

| Área | Estado |
| --- | --- |
| Arquitectura | En revisión |
| Modelo de dominio | Validado |
| Modelo de datos | En revisión |
| Autenticación | Parcialmente documentada |
| Autorización | En revisión |
| Frontend | Pendiente |
| Reportes | Pendiente |
| Reglas de negocio | Pendiente de validación institucional |
| Roadmap | Pendiente |

## 7. Riesgos conocidos

- Autorización distribuida.
- Permisos mezclados con estados académicos.
- Eliminación física de cuentas.
- Operaciones compuestas sin transacciones.
- Dependencia de la ficha académica respecto de múltiples entidades.
- Posible pérdida de roles durante cambios de estado.

## 8. Principios del proyecto

- Comprender antes de modificar.
- No asumir reglas de negocio.
- Un objetivo por tarea.
- Un cambio lógico por commit.
- No modificar archivos fuera de alcance.
- No ejecutar migraciones destructivas sin autorización.
- No incluir secretos ni credenciales.
- Revisar siempre el diff.
- Mantener compatibilidad funcional.
- Aplicar cambios pequeños, reversibles y verificables.
- El repositorio es la fuente principal de verdad.
- ChatGPT valida, planifica y supervisa.
- Codex inspecciona y ejecuta tareas controladas.

## 9. Documentación oficial

| Documento | Objetivo |
| --- | --- |
| `docs/WORKFLOW.md` | Define el flujo oficial, los principios, estados y controles para descubrir, validar e implementar cambios. |
| `docs/discovery/DISCOVERY_LOG.md` | Registra los descubrimientos, su estado, resultado resumido y trazabilidad. |
| `docs/architecture/DOMAIN_MODEL.md` | Consolida el modelo de dominio funcional actual validado, incluidos actores, módulos, procesos, riesgos y preguntas pendientes. |
| `docs/PROJECT_CONTEXT.md` | Proporciona el contexto inicial resumido y dirige hacia la documentación especializada. |

## 10. Cómo utilizar este proyecto

```text
Leer PROJECT_CONTEXT
        ↓
Leer el documento especializado correspondiente
        ↓
Trabajar según el flujo oficial y el alcance autorizado
```

Para comprender el proceso de trabajo, consultar `WORKFLOW.md`. Para revisar el avance del descubrimiento, consultar `DISCOVERY_LOG.md`. Para trabajar con conceptos, actores, módulos o procesos del negocio, consultar `DOMAIN_MODEL.md`.

## 11. Estado actual de la modernización

| Etapa | Estado |
| --- | --- |
| Descubrimiento | En progreso |
| Documentación | En progreso |
| Planificación | Pendiente |
| Implementación | Pendiente |
| Frontend | Pendiente |

## 12. Próximos pasos

De acuerdo con el registro de descubrimiento, corresponde:

- Completar la revisión de la arquitectura inicial.
- Completar la revisión integral del modelo de datos.
- Completar la revisión del alcance efectivo de roles y permisos.
- Resolver mediante la Matriz de Negocio las reglas institucionales que permanecen abiertas tras la validación del modelo de dominio.

## 13. Información deliberadamente excluida

Este documento no reemplaza:

- `DOMAIN_MODEL`, que contiene el modelo de dominio funcional validado.
- `BUSINESS_RULES`, destinado a las reglas de negocio y su validación.
- `ROADMAP`, destinado a la planificación de la modernización.
- El Manual Maestro, que resume el conocimiento validado y orienta la modernización.

`PROJECT_CONTEXT.md` es solamente la puerta de entrada al proyecto. Resume información confirmada y remite a sus fuentes especializadas; no contiene evidencia detallada, reglas no validadas, decisiones de arquitectura futura ni propuestas de mejora.

Antes de comenzar cualquier análisis o implementación se recomienda leer este documento completo y luego consultar la documentación especializada correspondiente.
