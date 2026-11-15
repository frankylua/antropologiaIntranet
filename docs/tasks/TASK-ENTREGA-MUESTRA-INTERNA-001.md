# TASK-ENTREGA-MUESTRA-INTERNA-001

## Estado y autorización

PREPARACIÓN COMPLETA; REVIEW TÉCNICO — APROBADO PARA VF OPERATIVA. D-01 corregido con autorización y Docente ID 18 elegido por el usuario; las seis fichas TEST tienen VF PASS («Guías PASS»). Muestra localhost validada para el alcance acordado; siete documentos pendientes de autorización de staging y commit. Autorización expresa del usuario: «Autorizo preparar la muestra interna». Destino confirmado: «En este equipo, usando localhost». Fecha de preparación: 14/11/2026; objetivo de muestra interna: 20/11/2026. No se autoriza por esta Task publicar en Internet ni modificar código ante nuevos defectos sin presentar evidencia y alcance.

## Línea base y alcance

Repositorio C:/xampp/htdocs/antropologiaIntranet, rama refactor/fase-0-seguridad, HEAD f482ed898bafa23abca1877f14dafd570a05781c. Commit anterior 498134eb9a22d48f3e94a624f6cb289d7b6d95a8. Ambos pushes fueron informados por el usuario; se usa esa confirmación y no se repite verificación remota. Sin cambios rastreados ni staging al inicio. Se preservan ADR-003, aviso institucional JSON y PDF académico no rastreados.

Preparar:

1. Conciliación documental de estados publicados y matriz de alcance/evidencia.
2. Comprobaciones integradas de cruces pendientes usando datos ficticios aislados, HTTP de lectura/rechazo y metadatos reales de solo lectura. Reutilizar PASS técnicos y funcionales existentes.
3. Respaldo actual de BD, archivos de ejecución y configuración privada fuera del directorio público; snapshot de código de la revisión identificada. Verificar archivos y checksums, sin afirmar restauración no ejecutada.
4. Instrucciones de puesta en marcha, recorrido de muestra y recuperación en localhost, con límites y pendientes explícitos.
5. Review Técnico y dictamen de entrega: listo para alcance acordado, bloqueantes y diferidos.

Archivos previstos: esta Task, docs/entrega/MUESTRA-INTERNA-2026-11-20.md, docs/TASKS.md, docs/roadmap/ROADMAP.md y conciliación acotada de las tres Tasks previas de Profesor integral, CRUD Estudiante y edición personal Docente. Máximo siete documentos. No se modifica aplicación, esquema, cuentas ni permisos reales.

Artefactos privados de preparación: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/entrega-muestra-interna-20261114/. No incorporar respaldos, .env, datos personales ni fixtures a Git o htdocs.

## Criterios

- Revisión de aplicación identificable por commit y separación de los documentos de entrega todavía no versionados.
- Cierres basados en evidencia; no convertir variantes sin resultado manual en PASS.
- Localhost operativo y recursos principales disponibles; roles, perfiles y relaciones conservados en integración aislada.
- Respaldo verificable, límites de recuperación explícitos y sin restaurar sobre la BD viva.
- Política de datos provisional identificada como texto de prueba. Cursos conserva su bloqueo institucional independiente y no se redefine por esta muestra de CRUD.
- Todo defecto nuevo documentado antes de cualquier corrección. No inventar aprobaciones de despliegue, commit o push.

## Trabajo ejecutado y conciliación

Se conciliaron los siete documentos previstos y se creó docs/entrega/MUESTRA-INTERNA-2026-11-20.md con matriz de evidencia, arranque localhost, recorrido sugerido, respaldos y recuperación. La Task original de Profesor ya no figura como no iniciada: se identifica la implementación vigente y se preserva su contenido como antecedente, sujeto a las decisiones posteriores. Las Tasks de CRUD/inactivación y edición personal registran los commits y la confirmación de push del usuario, conservando límites de las VF no informadas.

Se mantienen intactos aplicación, esquema y datos. Los tres archivos no rastreados iniciales permanecen excluidos de documentación/código; el PDF de ejecución se incluye únicamente en el respaldo privado para no perder un archivo actual. No se crean capacidades, cuentas ni relaciones por esta preparación.

## Evidencia técnica

- **54/54 comprobaciones integradas PASS**, en cuatro cadenas con BD SQLite persistente entre procesos y endpoints reales: alta administrativa de Docente + autoalta Estudiante + guía + inactivación/reactivación; asignar/retirar rol adicional y conservar acceso; agregar Docencia a una cuenta Comité existente sin duplicar login; autoalta pública Docente forzada a Pendiente y su rechazo/aceptación con login/capacidades actualizados. Sesiones proceden del Login real del fixture. No es VF de navegador ni ensayo de bloqueos InnoDB.
- **70 consultas preparadas nativamente en MariaDB**, sin errores y sin execute de mutaciones. Metadatos reales: PHP 8.2.12, MariaDB 10.4.32, 39 tablas InnoDB, inactivación habilitada, cero identidades personales/perfiles duplicados, cero personas/docentes/estudiantes sin cuenta/persona según las comprobaciones; existen roles Admin y Comité.
- **22/22 comprobaciones HTTP PASS** de ingreso, redirecciones protegidas, rechazos sin sesión y CSS/JS locales; **12/12 de autoalta HTTP PASS**, con HTML/versión de scripts/token/rechazo sin datos completos para impedir altas reales.
- **21/21 comprobaciones iniciales de artefactos/documentos/Git PASS**: checksums de archivos, CRC de ZIP, ausencia de duplicados, exclusión de .env/PDF no rastreado del código, enlaces relativos existentes, UTF-8 sin caracteres de reemplazo, solo documentos planeados y sin staging.
- Se reutilizan los PASS previos (inactivación 111 y frontend 11, edición Docente 91 y frontend 8, CRUD Estudiante y aceptación) y las VF del usuario. No se reejecutan matrices cerradas por actualizar documentación.

Comandos y artefactos reproducibles en entrega-muestra-interna-20261114/: `python test-integration.py`, `C:/xampp/php/php.exe preflight.php`, `C:/xampp/php/php.exe backup-database.php`, `python package-delivery.py`; resultados integration-results.json, preflight-results.json, http-smoke-results.json, delivery-qa-results.json y delivery-manifest.json. El HTTP de autoalta reutilizó `python ../test-autoalta-acuerdos-http.py`, conservado también como evidencia en esta carpeta. No se contabilizan como ejecuciones manuales del usuario.

## Arranque del entorno y respaldos

El primer preflight falló con conexión rechazada: Apache/MySQL no estaban activos ni escuchaban. Se comprobó configuración de Apache (Syntax OK) y se obtuvo permiso del sandbox para iniciar ambos en segundo plano, con salida privada fuera de htdocs. Se reejecutó preflight y HTTP con éxito. No se cambió configuración de red, puertos ni servicios instalados. Ambos procesos quedan operativos para la muestra localhost.

Respaldo actual de BD generado mediante mysqldump --single-transaction, con rutinas/triggers/eventos y hex-blob, exit 0, marcador de finalización y 39 CREATE TABLE. Temporal de opciones eliminado; sin imprimir credenciales. Archivo SQL y ZIP completo verificados con SHA-256. ZIP privado contiene código de f482ed8, configuración .env, vendor, files/, archivos/ y SQL; se contrastaron 19 archivos de ejecución y la copia de configuración, sin entradas duplicadas. Un primer empaquetado detectó diferencias de bytes entre archivos de ejecución rastreados y Git por normalización de fin de línea; se reconstruyó con las copias actuales de ejecución y la verificación final pasó.

Se preparó además código rastreado por git archive de f482ed8 y documentación de entrega por separado. El código identificado por commit no incorpora estos documentos todavía sin versionar. No se restauró sobre la BD viva ni se certifica recuperación funcional ensayada. El SQL lleva CREATE DATABASE/USE de la base original; la guía explica por qué no debe importarse ingenuamente en otra base seleccionada por interfaz.

## Hallazgo D-01 y decisión pendiente

La consulta de relaciones actuales identificó seis Estudiantes cuyo guía es una persona existente sin fila en profesor: uno en cada estado 1,2,3,4,5,6. Cuatro Estudiantes sí tienen vínculo estructural de guía válido. Se confirmó en lectura de modelos reales que las seis consultas de nombre de guía devuelven cero filas; por tanto el contrato actual no resuelve ese dato. Las seis fichas sí tienen filas de programa para revisión administrativa.

No se concluye por inferencia que la persona asignada sea incorrecta ni que deba crearse un perfil Docente: podría requerir confirmación de datos o una definición del contrato histórico. Se generó REVISION-GUIAS-PENDIENTES.md, privado, con las fichas y personas a revisar; no se publican esos nombres en Git.

Se preguntó al usuario: resolver las seis asignaciones antes de la muestra, o presentar registros coherentes dejando estas seis en revisión. La segunda opción no oculta fichas ni cambia el sistema; únicamente delimita el recorrido del presentador y requiere aceptación expresa de la observación. El tiempo transcurrido no se toma como aprobación. Los datos y roles se conservan hasta conocer la decisión y, si hay que corregir, la asignación institucional correcta.

## Review Técnico y dictamen

Se revisaron diferencias y alcance: siete documentos (cinco existentes y dos nuevos), sin código/migraciones; fuentes de cierre trazables a commits y respuestas del usuario. El paquete de código es identificable, el respaldo queda fuera de htdocs/Git, y los contratos de autenticación/ownership/relaciones se verificaron mediante los cruces aislados descritos. Índice vacío, rama refactor/fase-0-seguridad, HEAD f482ed898bafa23abca1877f14dafd570a05781c. No hubo cambios ajenos ni nueva autorización institucional inferida.

**REVIEW TÉCNICO — OBSERVADO.** La preparación solicitada está realizada y los artefactos son verificables; D-01 impide declarar sin reservas una muestra general de las fichas actuales. El criterio de respaldo de archivo es conforme, el de restauración ensayada no se afirma. El arranque localhost y las funciones técnicas comprobadas son conformes, con las limitaciones de pruebas aisladas y de VF histórica indicadas en la guía. No se inicia una VF nueva mientras siga pendiente el tratamiento de D-01.

Diferidos explícitos: aprobación/versión/persistencia del aviso de datos para publicación abierta; modernización de credenciales; prueba de recuperación aislada; uso completamente sin Internet de recursos visuales externos. Cursos conserva su bloqueo institucional independiente. No se certifican esas áreas por esta preparación.

Acción mínima pendiente: decisión del usuario sobre D-01. Si elige resolver, confirmar guías/asignaciones correctos antes de cualquier cambio; si acepta muestra controlada, registrar alcance/observación y preparar la comprobación operativa mínima sin repetir VF aprobadas. Staging, commit, push, restauración o publicación requieren sus autorizaciones correspondientes; esta Task no las presupone.

## Resolución autorizada de D-01 y nuevo dictamen

El usuario eligió «Resolver las seis asignaciones antes de la muestra». El diagnóstico posterior precisó seis fichas TEST, IDs usuario 7–12, cada una asignada a sí misma. Se preguntó qué Docente correspondía, y el usuario eligió «Docente de prueba ID 18: prueba admin docente avila sandoval». Esta selección autoriza el ajuste concreto; no se infiere una asignación para datos académicos reales ni se crea un perfil.

Se generó respaldo previo verificado. En una transacción se bloquearon y comprobaron el Docente 18 Aceptado y las seis fichas TEST aún autorreferentes; se modificó exclusivamente prof_guia a 18. Resultado: seis filas afectadas, demás campos de Estudiante idénticos, hashes de las otras 38 tablas idénticos, seis consultas del modelo real resolviendo al guía 18. Commit de la transacción confirmado. No hubo cambio de esquema ni de código. Evidencia: guide-correction-result.json.

Preflight posterior: cero guías sin perfil, conteos de cuentas/personas/perfiles conservados. Se generó respaldo posterior y se reconstruyó el ZIP privado con sus nuevos checksums; el respaldo previo sigue disponible. La guía y el manifiesto reflejan el estado corregido. Los dictámenes observados anteriores son históricos y quedan superados por esta comprobación.

**REVIEW TÉCNICO — APROBADO PARA VF OPERATIVA.** Bloqueante D-01 resuelto; no quedan bloqueantes detectados para la muestra localhost dentro del alcance. La restauración continúa sin ensayo, como límite declarado. No se reejecutan suites del código sin cambios; se verifican las seis relaciones afectadas y los artefactos posteriores.

VF mínima pendiente: Admin o Comité abre las seis fichas TEST (IDs 7–12) y comprueba que muestran «prueba admin docente avila sandoval» como Profesor guía, conservando el estado y antecedentes. Las lecturas técnicas ya son conformes; falta ese resultado visual del usuario. Respuesta: «Guías PASS» o indicar la ficha que falla. No solicitar nuevas pruebas de autoalta, roles o inactivación ya aprobadas. Staging/commit/push documental permanecen pendientes de autorización.

## Resultado funcional de preparación — 14/11/2026

El usuario confirmó «Guías PASS» a la comprobación visual de las seis fichas TEST, una en cada estado indicado. Se registra PASS de la VF operativa de guías después del ajuste autorizado al Docente ID 18. D-01 queda cerrado técnica y funcionalmente; no se atribuye una VF nueva a otros recorridos ni se repiten las VF anteriores.

**DICTAMEN DE ENTREGA: LISTA PARA MUESTRA INTERNA EN ESTE EQUIPO POR LOCALHOST**, dentro del alcance documentado. Review Técnico aprobado y VF operativa PASS. Continúan explícitos los límites: recuperación preparada sin ensayo de importación; publicación en Internet y Cursos fuera del cierre. No quedan bloqueantes detectados del alcance de muestra acordado.

Antes de registrar el PASS, los siete documentos coincidían con el manifiesto revisado, HEAD f482ed898bafa23abca1877f14dafd570a05781c e índice vacío. Se actualiza únicamente la documentación y su ZIP; código y respaldos posteriores al ajuste de datos conservan sus checksums. No se repiten pruebas por registrar este resultado.

Siguiente paso preparado: autorización específica de staging y commit de los siete documentos del manifiesto externo commit-manifest.json. Mensaje propuesto: `TASK-ENTREGA: prepara muestra interna y concilia cierres`. Excluir respaldos, .env, datos/fixtures y los tres archivos ajenos ya preservados. Este PASS no autoriza staging, commit ni push. Publicación documental pendiente.

## Autorización de staging y commit — 14/11/2026

El usuario confirmó «Autorizo staging y commit» para los siete documentos revisados. Preflight conforme: hashes coincidentes con el manifiesto posterior a «Guías PASS», HEAD f482ed898bafa23abca1877f14dafd570a05781c, rama refactor/fase-0-seguridad e índice vacío. Se registra esta autorización sin modificar código, datos ni respaldos de la aplicación.

Este registro forma parte del commit autorizado `TASK-ENTREGA: prepara muestra interna y concilia cierres`. Hash y comprobación posterior se conservarán en el artefacto externo entrega-muestra-interna-20261114/commit-result.json. El ZIP documental se actualiza junto con su checksum. Código base y respaldo posterior al ajuste de guías conservan sus hashes. Push pendiente de autorización específica; respaldos, configuración, datos privados y archivos ajenos quedan excluidos del commit.
