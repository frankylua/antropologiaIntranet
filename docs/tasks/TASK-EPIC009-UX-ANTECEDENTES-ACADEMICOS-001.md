# TASK-EPIC009-UX-ANTECEDENTES-ACADEMICOS-001

## Estado

- Fase A: COMPLETADA.
- Fase B: COMPLETADA.
- Revisión técnica final: PASS.
- Validación funcional: PASS.
- Estado: CERRADA.

## Implementación técnica

Las fichas de Estudiante y Docente estandarizan Grado Académico,
Postdoctorado, Publicación/Otras Publicaciones, Congreso, Proyecto de
Investigación, Pasantía y Beca, cuando corresponde, dentro de su propio
desplegable. Cada OA contiene cards o estado vacío, su único listado, la acción
`Agregar nuevo` permitida por la autorización existente y los editores
CREATE/EDIT disponibles.

Los ciclos CREATE y EDIT ocultan listado y acción de alta mientras el editor
está abierto. La X estándar restaura el listado sin escribir datos. Después de
guardar se reutiliza la función de carga aprobada para el OA y se restaura el
estado de lista. El viewport se posiciona en el editor.

La opción general `Agregar Datos Académicos` y sus handlers sin consumidores
fueron retirados de los cuatro contextos de ficha. También se eliminó la
segunda carga de OA asociada a esa navegación y la recarga completa de la ficha
al eliminar o editar Grado.

Los parciales y scripts compartidos conservan una ruta compatible para las
pantallas de alta `form-doc/estudiante.php` y `form-doc/docente.php`.

## Tesis

Tesis no fue reactivada. En Docente se trasladó únicamente su CREATE existente
al desplegable de Tesis y se aplicó el ciclo abrir/cerrar con X y scroll. Hay
estado vacío disponible; cards siguen inactivas y no se habilitó READ visual ni
EDIT. En Estudiante Tesis no se incorporó como OA; se eliminó la llamada
residual a `cargarTesis()` sin efecto lateral.

## Congreso y Participación

El editor principal de Congreso vuelve a cards y `Agregar nuevo`. El editor
secundario de Participación conserva su navegación anidada y su cierre hacia el
editor de Congreso.

## Revisión técnica

- PASS: sin cambios en CRUD, backend, modelos, endpoints, payloads,
  autorización, CSRF, sesiones, SQL, migraciones ni persistencia.
- `php -l`, `node --check` y `git diff --check`: PASS.
- No se introdujeron `console.log` ni `debugger`.

## Validación funcional

PASS: Estudiante propio, Docente propio, Admin → Estudiante, Admin → Docente,
Alta Estudiante y Alta Docente.
