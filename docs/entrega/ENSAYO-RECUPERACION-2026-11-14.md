# Ensayo de recuperación aislada — 14/11/2026

Estado vigente: ENSAYO VALIDADO TÉCNICA Y FUNCIONALMENTE; «Recuperación PASS» comunicado por el usuario con criterio de ficha histórica completa. Servidor temporal cerrado y base aislada conservada. Pendiente autorización de staging/commit de cinco documentos. Los estados pendientes/observados de secciones posteriores son antecedentes resueltos por las aclaraciones y el cierre al final.

## Resultado técnico

**REVIEW TÉCNICO — APROBADO PARA VF OPERATIVA DE LA COPIA.** Se recuperó el respaldo completo en la base `antropologia_muestra_restore_20261114`, creada e importada con autorización expresa del usuario. La base original y su configuración no fueron sustituidas. Falta únicamente la comprobación visual mínima del usuario sobre esta copia; no se repite la VF completa del CRUD.

## Límites y aislamiento

- Aplicación original/documentación al iniciar: HEAD 834f1d09d419abdbf066f0ad810a038f99c1213e. Código contenido en el respaldo: f482ed8.
- Respaldo íntegro verificado por SHA-256 y CRC; SQL original 4f7c64eaaf379eabd0263920600bb334f8e56a1dee67db4cbe2f6fbc62ff5abc.
- Se adaptaron exclusivamente CREATE DATABASE y USE del dump al destino nuevo; no se reemplazaron cadenas dentro de datos. No había rutinas, eventos, triggers ni referencias de esquema cualificadas que requirieran otra adaptación.
- Se rechazaba un destino ya existente, evitando sobrescribirlo. El nombre de destino se verificó distinto de la base original.
- La copia de aplicación se extrajo fuera de htdocs; DB_NAME y APP_URL solo se cambiaron en esa copia y en el proceso temporal. El servidor se liga a 127.0.0.1:8097 con cookie de sesión y directorio de sesiones propios.
- El router temporal exige el destino aislado y bloquea .env, SQL, logs, vendor y documentación por HTTP. No se publica la copia en red externa.

## Comprobaciones ejecutadas

| Verificación | Resultado |
| --- | --- |
| Importación mediante mysql.exe, SQL adaptado | Exit 0 |
| Datos restaurados antes de pruebas | 39/39 tablas coinciden con el snapshot del origen comprobado, por conteo y hash de filas canonizadas |
| Estructura | Coinciden 208 columnas, 56 claves foráneas con reglas y 6 CHECK |
| Documentos binarios | 2/2 con tamaño y SHA-256 correctos |
| Archivos de ejecución | 19/19 recuperados y contrastados contra manifiesto |
| Seis guías corregidas | Usuarios 7–12 restaurados con guía ID 18 |
| Aplicación recuperada por HTTP | 28/28 PASS: ingreso, acceso privado bloqueado, listados, lectura de las seis guías, altas ficticias, ficha/edición personal, inactivación/reactivación y denegación de sesión inactiva |
| Origen después de todas las pruebas | 39 tablas con datos idénticos al snapshot inicial; configuración original sin modificar |
| Datos originales dentro de la copia tras probar | Todas las filas originales preservadas; los datos añadidos pertenecen exclusivamente a cuentas ficticias del ensayo |

No se leyeron ni utilizaron contraseñas de cuentas reales para ejecutar la prueba HTTP. Se crearon tres cuentas ficticias exclusivamente en la base aislada (Admin de prueba y altas de Docente/Estudiante). La igualdad inicial de las 39 tablas se verificó antes de esas altas. La copia de prueba final ya contiene esas filas adicionales; el respaldo original permanece intacto.

Una primera comprobación de hash detectó conversión automática de finales de línea del SQL al escribirlo en Windows y detuvo la ejecución antes de crear/importar. Se reconstruyó desde el respaldo verificado escribiendo bytes explícitos; el importador aceptó entonces el hash y ejecutó el destino autorizado. No hubo importaciones fallidas sobre la base original.

## Evidencia y comandos

Carpeta privada: `C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/ensayo-recuperacion-aislada-20261114/`.

- preparation-report.json: límites, revisión y hashes del SQL original/adaptado.
- restore-result.json / source-before.json: datos recuperados y snapshot agregado, sin valores personales.
- http-recovery-results.json: 28 casos HTTP.
- final-verification.json: origen intacto, filas recuperadas preservadas y metadatos coincidentes.
- Scripts ejecutados: `C:/xampp/php/php.exe restore.php`, `python test-restored-http.py`, `C:/xampp/php/php.exe verify-final.php`.
- preview-server.json: proceso de la copia habilitado temporalmente para VF. Tras las pruebas automáticas se detuvo su servidor y se abrió nuevamente solo para la comprobación visual del usuario.

Estos scripts de importación y siembra son de ejecución única y se conservan como evidencia. No reejecutarlos sobre el destino existente. SQL, .env y credenciales ficticias de acceso a la copia permanecen fuera de Git.

## VF mínima de la copia — pendiente

1. Abrir http://127.0.0.1:8097/form-doc/login.php y entrar con la cuenta ficticia entregada en el chat/ACCESO-PRUEBA.md privado.
2. Abrir Estudiantes, localizar una ficha EPIC003_TEST_REGLAMENTO y comprobar que aparece el guía «prueba admin docente avila sandoval».
3. Abrir el listado Docentes y comprobar que carga. No repetir altas, roles o modificaciones de la intranet original.

Responder «Recuperación PASS» o indicar el paso fallido. Esta VF es específica del entorno recuperado. La intranet habitual continúa en http://localhost/antropologiaIntranet/ con su base original.

## Cierre y límites

Ensayo verificado para el respaldo y versiones indicados: PHP 8.2.12/MariaDB 10.4.32 en este equipo. No es una certificación de todas las futuras versiones, de concurrencia bajo carga ni de publicación abierta. La base aislada se conserva para inspección; no se elimina sin autorización. El servidor temporal se cerrará tras la VF. No cambió código de aplicación; se preparan cinco documentos, sin staging/commit/push autorizados.

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


Autorización vigente del 15/11/2026: el usuario aprobó staging y commit de los cinco documentos del ensayo de recuperación aislada, tras Recuperación PASS. Las menciones anteriores a autorización pendiente son antecedentes. Push requiere autorización separada.
