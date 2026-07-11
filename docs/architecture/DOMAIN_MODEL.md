# Modelo de dominio actual

## 1. Propósito y alcance

Este documento consolida el modelo de dominio funcional del sistema actual. Describe conceptos, actores, módulos, procesos y comportamientos implementados y observables; no define una arquitectura futura.

## 2. Conceptos principales

- `login`: cuenta de acceso.
- `usuario`: identidad personal común.
- `profesor` y `estudiante`: especializaciones académicas de `usuario`.
- `permiso_login`: asignación múltiple de permisos.
- `tipo_estudiante`: estado académico persistido.
- Ficha académica: proyección construida desde múltiples entidades.

## 3. Actores principales

- Estudiante.
- Profesor.
- Comité académico.
- Administrador.

## 4. Roles académicos contextuales

- Profesor guía.
- Coguía.
- Investigador.
- Coinvestigador.
- Autor.
- Coautor.
- Profesor patrocinante.

Estos roles describen participaciones académicas contextuales y no son necesariamente roles de seguridad.

## 5. Módulos funcionales confirmados

- Acceso.
- Personas.
- Estudiantes.
- Profesores.
- Cursos.
- Ficha académica.
- Publicaciones.
- Congresos.
- Proyectos.
- Tesis.
- Grados académicos.
- Postdoctorados.
- Becas.
- Pasantías.
- Líneas de investigación.
- Catálogos.
- Reglamento.
- Calendario.
- Reportes.

## 6. Procesos implementados confirmados

- Inicio de sesión.
- Construcción de sesión.
- Registro de estudiante.
- Registro de profesor.
- Cambio de estado académico.
- Reconstrucción de permisos.
- Gestión de cursos.
- Gestión de antecedentes.
- Construcción de ficha académica.
- Eliminación física de cuenta.

## 7. Ciclo de vida de la cuenta

Creación
→ asignación de permisos
→ autenticación
→ modificación de permisos
→ eliminación.

Este ciclo representa el comportamiento implementado actualmente, no necesariamente el comportamiento futuro deseado.

## 8. Estados del estudiante

- Postulante.
- Aceptado.
- Matriculado.
- Graduado.
- Retirado.
- Eliminado.
- Reprobado.

La existencia de estos estados está confirmada, pero la máquina completa de transiciones sigue pendiente de validación institucional.

## 9. Profesor

No existe un estado persistido para `profesor` equivalente al estado académico persistido para `estudiante`.

## 10. Contradicciones abiertas

- Permiso 3: “postulante” en base de datos versus “aceptado” en PHP.
- Conservación histórica requerida versus eliminación física.
- Soporte multirrol versus reconstrucción destructiva de permisos.
- Definición incompleta de comité y tabla `admin`.
- Flujo de reportes incompleto.

## 11. Riesgos confirmados

- Autorización distribuida.
- Permisos mezclados con estados académicos.
- Eliminación física.
- Operaciones compuestas sin transacciones.
- Dependencia de la ficha académica respecto de múltiples entidades.
- Posible pérdida de roles durante cambios de estado.

## 12. Preguntas funcionales pendientes

Las siguientes preguntas están pendientes de respuesta por parte del usuario o cliente y no constituyen hechos confirmados:

- Permiso 3: ¿cuál es su significado institucional?
- Transiciones de estados: ¿qué transiciones son válidas y bajo qué condiciones?
- Aprobación de profesores: ¿qué reglas y responsables intervienen?
- Validación de antecedentes: ¿qué antecedentes se validan y con qué criterios?
- Composición de ficha académica: ¿qué información debe integrar y qué reglas determinan su presentación?
- Cursos: ¿qué reglas institucionales gobiernan su gestión?
- Reportes: ¿qué flujo, alcance y responsables corresponden?
- Conservación histórica: ¿qué información debe conservarse y durante cuánto tiempo?
- Reglas multirrol: ¿qué combinaciones de roles deben permitirse y cómo deben mantenerse?

## 13. Estado del documento

- Fuente: código, esquema y auditorías validadas.
- DISCOVERY relacionado: DISCOVERY-004.
- Estado: VALIDADO.
- Fecha: 2026-07-11.
