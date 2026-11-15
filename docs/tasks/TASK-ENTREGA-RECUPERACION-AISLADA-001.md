# TASK-ENTREGA-RECUPERACION-AISLADA-001

Estado vigente: ENSAYO VALIDADO TÉCNICA Y FUNCIONALMENTE; «Recuperación PASS» comunicado por el usuario con criterio de ficha histórica completa. Servidor temporal cerrado y base aislada conservada. Pendiente autorización de staging/commit de cinco documentos. Los estados pendientes/observados de secciones posteriores son antecedentes resueltos por las aclaraciones y el cierre al final.

## Autorización y alcance

Autorización expresa: «Autorizo el ensayo aislado, incluida la creación de la base de prueba y la importación del respaldo». Destino exclusivo: antropologia_muestra_restore_20261114, inexistente en el preflight previo. No se autoriza reemplazar la base original, modificar su configuración ni eliminar el destino al terminar. Datos y cuentas de prueba del ensayo quedan confinados a la copia.

Línea base documental: 834f1d09d419abdbf066f0ad810a038f99c1213e. Código respaldado: f482ed898bafa23abca1877f14dafd570a05781c. Push de 834f1d0 informado por el usuario. Sin modificaciones rastreadas ni staging al iniciar; tres archivos ajenos preservados.

Preparación: verificar SHA-256/CRC, extraer en carpeta privada fuera de htdocs, adaptar solo CREATE DATABASE/USE al nombre aislado y rechazar referencias de esquema o código almacenado no revisado. Comprobar las 39 tablas, archivos/documentos y seis guías, y ejecutar la aplicación recuperada con configuración y sesión propias. No usar credenciales de cuentas reales para las pruebas HTTP.

Artefactos privados: carpeta ensayo-recuperacion-aislada-20261114 bajo el directorio de evidencia ya utilizado. El SQL adaptado, .env y datos no se versionan. Se documentarán resultados y límites antes de VF o commit. Sin autorización de staging/commit/push por esta instrucción.

## Implementación operativa, revisión y resultado

Ejecutado el ensayo autorizado en el destino nuevo y exclusivamente allí. [Informe completo](../entrega/ENSAYO-RECUPERACION-2026-11-14.md). SHA-256 del respaldo/SQL comprobados, extracción segura, dos directivas SQL adaptadas, guardas de esquema y SQL antes de importar. Importación exitosa, 39 tablas iguales al origen antes de las pruebas, 208 columnas/56 FK/6 CHECK coincidentes, dos documentos binarios y 19 archivos correctos, seis guías restauradas. 28/28 pruebas HTTP sobre la copia con cuentas ficticias. Revisión posterior del origen: 39 tablas intactas; todos sus registros también se conservan en la copia.

Se verificó el diff: exclusivamente documentación. Cinco archivos de alcance: esta Task, informe de ensayo nuevo, guía de muestra existente, docs/TASKS.md y docs/roadmap/ROADMAP.md. No se modifican scripts de aplicación ni migraciones del repositorio. Los tres archivos ajenos no rastreados continúan preservados. HEAD 834f1d0, rama refactor/fase-0-seguridad, índice vacío.

**REVIEW TÉCNICO — APROBADO PARA VF OPERATIVA DE LA COPIA.** Criterios técnicos conformes; resta la comprobación visual mínima de listado/ficha recuperada, con acciones definidas en el informe. No se inventa PASS manual a partir de las 28 pruebas HTTP. Límite: versiones y snapshot indicados, sin ensayo de carga/concurrencia o Internet.

Copia temporal habilitada en 127.0.0.1:8097, configuración y sesiones aisladas. La base de prueba se retiene para inspección; no se eliminó ni se autoriza su borrado por inferencia. Después de VF se cerrará únicamente el servidor temporal propio. Staging/commit/push requieren autorización separada.

## Incidencia en VF de la ficha TEST

El usuario mostró captura de ficha vacía con aviso genérico. Login confirmado por el usuario; no se registra Recuperación PASS. Diagnóstico de solo lectura: read_est_id devuelve HTTP 200 con [] en los usuarios 7–12 de la copia. La misma lectura del modelo devuelve cero filas en la base original. Ambas bases coinciden: los seis registros carecen de referencia válida de pueblo y no tienen líneas de investigación. La consulta de ficha utiliza INNER JOIN con esas relaciones y suprime toda la ficha.

La corrección anterior de guía sigue verificada: solo resolvió prof_guia. La recuperación conserva fielmente los datos y sus carencias; el fallo visual no demuestra pérdida en la restauración. La preparación técnica no había probado la ficha completa de estos seis registros, únicamente sus nombres de guía; se reconoce esa limitación de cobertura y se registra la incidencia sin convertirla en PASS.

**REVIEW TÉCNICO — OBSERVADO PARA LA VF DE FICHAS TEST.** Recuperación de tablas/archivos e invariancia del origen siguen conformes; la comprobación visual queda observada. Evidencia: incomplete-test-profiles.json. Sin modificaciones adicionales de datos o código. Cualquier corrección del contrato READ o completado de datos de prueba requiere alcance y autorización concretos; no inventar pertenencia a pueblo ni líneas reales.

## Aclaración del usuario: alcance de los registros EPIC003

El usuario aclaró: «solo los datos históricos conservan sus profesores guías; los de prueba epic003 no contienen ficha como el hallazgo anterior». Se reconoce que EPIC003 son fixtures parciales, no fichas personales completas representativas de la muestra. El vacío de esa ficha no se trata como defecto nuevo de restauración ni justifica modificar el contrato READ. Se retira la propuesta de ampliación; no hubo autorización ni cambios de código por ella.

La VF de recuperación se corrige: ingreso y carga de las grillas; comprobación de guía en una ficha histórica completa, en vez de exigir una ficha completa a EPIC003. Se conserva la confirmación del usuario sobre guías históricas y la evidencia técnica del ensayo. Las asignaciones de guía de los seis TEST, antes autorizadas, no se revierten por inferencia. No se repite la VF del CRUD ni se completan datos ficticios automáticamente.

La observación por usar EPIC003 como ficha de demostración queda retirada. REVIEW TÉCNICO — APROBADO PARA VF CON CRITERIO CORREGIDO. Cierre manual global pendiente de confirmación explícita «Recuperación PASS» de ingreso, listados y ficha histórica; no se inventa ese resultado.

## Cierre de validación funcional — confirmación del usuario

El usuario confirmó «Recuperación PASS» después de aclarar que EPIC003 son fixtures parciales. Se registra PASS del ingreso a la copia, de ambas grillas y de la conservación del Profesor guía en una ficha histórica completa. No se exige ni se declara una ficha completa para EPIC003. La propuesta de modificar READ fue retirada y no hubo cambios de código ni completado de datos por ese incidente.

**Ensayo de recuperación validado técnica y funcionalmente.** Se conservan 39 tablas restauradas iguales al snapshot antes de las pruebas, estructura/documentos/archivos conformes y 28 pruebas HTTP PASS; el resultado manual corresponde al usuario y se mantiene separado. Base original y configuración intactas; la copia contiene únicamente las adiciones ficticias del ensayo descritas antes.

Se cerró exclusivamente el servidor PHP temporal del puerto 8097 tras comprobar la identidad de su proceso. La comprobación inicial de procesos fue denegada por el sandbox y Stop-Process falló; con permiso del sistema se utilizó taskkill solo para el PID verificado. Puerto 8097 cerrado al finalizar. Apache/MySQL de la intranet habitual no se detuvieron. La base antropologia_muestra_restore_20261114 y archivos privados se conservan para inspección; no se eliminan por inferencia.

Siguiente paso: autorización separada de staging y commit de cinco documentos: Task del ensayo, informe de recuperación, guía de muestra, docs/TASKS.md y docs/roadmap/ROADMAP.md. Mensaje propuesto: `TASK-ENTREGA: valida recuperacion aislada del respaldo`. Manifiesto externo commit-manifest.json. Sin cambios de aplicación, migraciones ni credenciales en Git; preservar los tres archivos ajenos iniciales. Este PASS no autoriza staging, commit ni push.

## Autorización de staging y commit — 15/11/2026

El usuario confirmó «Autorizo staging y commit» después de recibir el resumen del ensayo. Se verificaron los hashes de los cinco documentos contra el manifiesto posterior a Recuperación PASS, HEAD 834f1d09d419abdbf066f0ad810a038f99c1213e, rama refactor/fase-0-seguridad e índice vacío. Solo se incorpora este registro de autorización a la documentación; aplicación y datos no cambian por el commit.

Este registro forma parte del commit autorizado `TASK-ENTREGA: valida recuperacion aislada del respaldo`. Hash y comprobación posterior se conservan fuera del repositorio en ensayo-recuperacion-aislada-20261114/commit-result.json. Push pendiente de autorización específica. Respaldos, SQL, .env, datos privados y tres archivos ajenos se mantienen excluidos.
