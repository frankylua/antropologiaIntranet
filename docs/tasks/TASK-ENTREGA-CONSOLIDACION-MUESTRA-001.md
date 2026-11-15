# TASK-ENTREGA-CONSOLIDACION-MUESTRA-001

## Cierre vigente

Commit 989ac6e13e353b3c747d6d07de8ead12405964d5, siete archivos autorizados, «Ayuda PASS» y «Consolidación PASS» confirmados. El usuario informó posteriormente «push ejecutado siguiente task»; se registra su confirmación sin repetir consulta remota. Task cerrada dentro del alcance de muestra interna.

Paquete final actualizado y verificado en consolidacion-muestra-20261115/version-final/delivery-manifest.json: incluye las ayudas desplegables del commit, 35/35 pruebas HTTP desde la copia extraída, ZIP/SQL/runtime conformes y 39 tablas/esquema/configuración de origen sin cambios. Servidor temporal detenido; respaldos anteriores conservados. Los resultados posteriores al commit están en artefactos privados; las menciones posteriores de paquete/versionado/push pendientes son históricas. Este cierre no autoriza producción ni otra implementación.

## Autorización y base

Usuario: «Autorizo consolidar la muestra interna». Aplicación base 93fff17ca547540d16d58b3a051eae1f3baa77a0, rama refactor/fase-0-seguridad. Destino vigente: este equipo por localhost, muestra del 20/11/2026. Credenciales y CSRF tienen Review/VF aprobados y push confirmado por el usuario.

Al iniciar existen cambios documentales de cierre en docs/TASKS.md, docs/roadmap/ROADMAP.md y TASK-EPIC002-CSRF-CRUD-PRINCIPAL-001.md. Se conservan dentro de esta consolidación. Los tres archivos ajenos no rastreados (ADR-003, aviso JSON y PDF de programa) se excluyen de Git y del código compartible; el PDF de ejecución se conserva en el respaldo privado.

## Alcance

1. Generar snapshot de código de la revisión identificada y respaldo privado nuevo de BD, configuración, vendor y archivos. No sobrescribir respaldos anteriores ni modificar cuentas/datos de origen.
2. Verificar ZIP, checksums y correspondencia con fuente/archivos/SQL; identificar un único paquete vigente mediante manifiesto privado. Conservar comprobantes de que la base principal no cambió durante la preparación.
3. Extraer el paquete fuera de htdocs y comprobar arranque/login con la base aislada de recuperación ya existente, configuración y sesiones independientes. No crear bases ni importar nuevamente SQL; la restauración anterior conserva su evidencia propia.
4. Conciliar la guía de muestra con credenciales, CSRF, recuperación aprobada y límites institucionales. Preparar recorrido y recuperación compatibles con hashes; no repetir VF del CRUD aprobado.
5. Review Técnico explícito y VF operativa mínima del paquete/instrucciones antes de autorizaciones Git separadas.

Cinco documentos previstos: esta Task, docs/entrega/MUESTRA-INTERNA-2026-11-20.md, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre de TASK-EPIC002-CSRF-CRUD-PRINCIPAL-001.md. No se modifica aplicación, esquema, permisos, navegación ni datos de origen. Respaldos/fixtures privados fuera de htdocs y Git: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/consolidacion-muestra-20261115/.

## Criterios

- Código ejecutable del paquete igual a 93fff17 y runtime íntegro, incluyendo archivos no rastreados de ejecución; documentación consolidada identificada por separado, todavía sin commit.
- Dump completo verificado de 39 tablas InnoDB, configuración coincidente y sin secretos en documentación versionada.
- Arranque e ingreso reales en copia extraída contra BD aislada; rechazo CSRF y regeneración compatibles, sin usar cuentas reales ni cambiar servicios de origen.
- Guía actual sin confundir la restauración anterior aprobada con una importación de este nuevo dump que no se ejecutará.
- Origen sin cambios, tres archivos ajenos preservados y staging vacío. No autoriza migraciones, restauración sobre origen, commit, push o publicación.

## Implementación y evidencia

Consolidación de aplicación 93fff17 completada. Se generaron snapshot Git, dump nuevo y paquete privado con app/ (código/configuración/vendor/archivos), database/ (SQL) y LEEME. Documentación consolidada posterior al commit se identifica y empaqueta por separado; los docs dentro del snapshot conservan su revisión original. Los respaldos de f482ed8 permanecen intactos.

Artefactos principales:

- codigo-93fff17.zip: 4.410.481 bytes, SHA-256 08019f48edf60e18c77a745c29717b7ca2ae5112bd294a86ded5ec467284576c.
- respaldo-local-93fff17-2026-11-15.zip: 4.452.444 bytes, SHA-256 495b2b04759b03d9ecfe4718f83342d2ee5e3dab7cdb1783339d7deba216a64c.
- respaldo-bd-muestra-20261115-150705.sql: 124.235 bytes, SHA-256 daa65ef68fba62d8cf933c650bb477575bc924ddbd33f4164a82353dbc0e31bf. Marca UTC; preparación 15/11/2026 America/Santiago.
- delivery-manifest.json: archivos, hashes, revisión, pruebas y límites. Documentación de cinco archivos en documentacion-consolidada-2026-11-15.zip y copia externa de la guía.

Comandos ejecutados (scripts en carpeta privada de consolidación salvo indicación):

| Comando / control | Resultado |
| --- | --- |
| `C:/xampp/php/php.exe ../source-consolidation-state.php before` | Huellas agregadas de 39 tablas, esquema y configuración antes del respaldo; sin volcar filas ni credenciales a la documentación |
| `C:/xampp/php/php.exe backup-database.php` | mysqldump exit 0, marcador final, 39 CREATE TABLE y SHA-256 conformes; opciones privadas temporales retiradas en finally |
| `python package-delivery.py` | CRC/rutas únicas y seguras; 353 entradas extraídas comprobadas; 223 archivos de código contrastados con origen; .env y 19 archivos de runtime coincidentes; SQL verificado dentro del ZIP |
| `python test-package-http.py` | 33/33 HTTP/MariaDB PASS desde el paquete extraído contra base aislada existente: login sintético, seis vistas, lecturas/grillas, guardado administrativo, CSRF, separación de sesiones, relogin/recarga y logout |
| `python test-origin-http.py` | 7/7 GET PASS del Apache original: raíz/ingreso, ambas páginas de autoalta y recursos; sin login real ni WRITE |
| `C:/xampp/php/php.exe ../source-consolidation-state.php after` | 39 tablas, esquema y configuración originales sin cambios después de los ensayos |
| `git diff --check` y conciliación de archivos | Solo cinco documentos del alcance; sin errores de whitespace ni staging; tres archivos ajenos preservados |

El script del paquete se detuvo inicialmente al comparar bytes con saltos de línea distintos: git archive aplica core.autocrlf y el working tree contiene LF/CRLF mixtos. Se contrastó con la forma LF del índice para los archivos que Git identifica como texto, sin normalizar binarios ni cambiar la fuente. Los ZIP parciales se conservaron como diagnóstico y se excluyen del paquete vigente. El ensayo HTTP prematuro de ese intento no llegó a iniciar la copia; se repitió después de completar/verificar el paquete y pasó 33/33. No se ocultan esos fallos de preparación como pruebas de aplicación aprobadas.

Servidor de ensayo: PHP privado 127.0.0.1:8099, código extraído, variable DB_NAME limitada por router a antropologia_muestra_restore_20261114, cookie y sesiones separadas; detenido en finally. Se utilizó exclusivamente la cuenta sintética ya existente en esa copia. No se crearon cuentas, bases ni tablas; no se reimportó SQL. El WRITE administrativo reenvió los datos de esa cuenta sin alterar personas de origen. Apache/MySQL originales permanecen operativos.

## Review Técnico

Se examinó la guía reescrita completa, las adiciones de esta Task y los tres cierres previos. Alcance conciliado: cinco documentos, cero archivos de aplicación modificados. El snapshot de código corresponde al commit identificado y las únicas diferencias de working tree son documentales. Configuración/datos/archivos privados se mantienen fuera de Git/htdocs; SQL separado de app/ y acceso al servidor de ensayo limitado por router a rutas expresas.

Se conservan permisos, autenticación, ownership, contratos READ/WRITE y transacciones de aplicación. Se reutilizan los Review/VF de credenciales y CSRF; no se repiten suites completas sin cambios de código. El ingreso desde la copia verifica consumidores actuales; la fuente conserva sus huellas. No hay migraciones, cambios institucionales, staging, commit ni push.

La guía concilia recuperación anterior aprobada, credenciales/CSRF publicados, parcialidad de fixtures EPIC003, ubicación/versiones de paquetes y contingencia compatible con hashes. Las comparaciones de ZIP y hashes prueban integridad, no acreditan importación del dump nuevo. La restauración anterior sigue aprobada por «Recuperación PASS»; una nueva importación requiere autorización separada. Publicación en Internet, aviso institucional y Cursos mantienen sus pendientes.

**REVIEW TÉCNICO — APROBADO PARA VF OPERATIVA MÍNIMA.** Paquete, instrucciones y criterios técnicos conformes; sin bloqueantes detectados dentro de la consolidación autorizada. No se declara recuperación nueva ni VF de navegador ejecutada por el agente.

## VF operativa pendiente

1. Abrir docs/entrega/MUESTRA-INTERNA-2026-11-20.md y ubicar el respaldo completo y delivery-manifest.json en la carpeta privada indicada, sin extraer ni importar nada.
2. En localhost, ingresar con la cuenta de prueba habitual y abrir las grillas Docentes y Estudiantes. Esperado: ingreso y ambas grillas disponibles.

Respuesta: «Consolidación PASS», FAIL o BLOQUEADA con el paso. No repetir CRUD ni pruebas de credenciales/CSRF aprobadas. Después se requiere autorización específica de staging/commit; push documental separado.

Estado: consolidación técnica completa, Review aprobado y VF operativa mínima pendiente. Estimación de referencia: 2–3 horas.

## Ajuste de presentación solicitado durante VF

El usuario confirmó que «carga bien» y señaló como molesto el aviso permanente de recuperación de la grilla Docentes. Se aplica un ajuste acotado de presentación a admin/ver.docente.php y admin/ver.estudiante.php: la ayuda común queda dentro de details/summary, cerrada por defecto y con texto secundario pequeño. No cambia estados, filtros, permisos, solicitudes ni operaciones. Se incorpora esta solicitud posterior al alcance: siete archivos totales (cinco documentos y dos vistas).

Los paquetes identificados como 93fff17 y sus huellas anteriores se conservan: preceden este ajuste visual y no se presentan como si lo incluyeran. Tras validar/versionar el ajuste se debe actualizar el paquete vigente antes del cierre definitivo de consolidación. La ubicación del respaldo fue confirmada por el usuario; no se declara Consolidación PASS por inferencia. La captura del aviso corresponde a Docentes.

Revisión del ajuste: diff limitado a dos párrafos convertidos en ayuda nativa desplegable; sin atributo open ni dependencia JavaScript. PHP lint de ambas vistas y git diff --check conformes. REVIEW TÉCNICO DEL AJUSTE — APROBADO PARA VF VISUAL. Pendiente comprobar ayuda cerrada al cargar y apertura/cierre al pulsar su título. Staging, commit y push siguen sin autorización.

Resultado posterior: el usuario confirmó «Ayuda PASS». Se aprueba funcionalmente el ajuste de ayuda desplegable; la mención anterior de VF visual pendiente queda superada. No se atribuye por esta respuesta una confirmación nueva de la grilla Estudiantes ni «Consolidación PASS» completo. Pendiente cierre operativo de consolidación, autorización Git y actualización del paquete para incluir las dos vistas ajustadas.

## Aceptación operativa de consolidación

El usuario confirmó posteriormente «Consolidación PASS». Se aprueba la VF operativa completa: ubicación de respaldo/manifiesto, ingreso y disponibilidad de ambas grillas. Se conserva también «Ayuda PASS» del ajuste visual. Las referencias anteriores a VF operativa pendiente quedan superadas por esta confirmación expresa.

Estado vigente: Review Técnico y VF aprobados; siete archivos preparados (cinco documentos y dos vistas). Pendiente autorización de staging y commit. Después de versionar las vistas se actualizará el paquete privado para incluirlas, conservando el respaldo 93fff17 como histórico y comprobando sus nuevos hashes. Esa actualización necesaria sigue dentro del alcance de consolidación ya autorizado; no implica migración ni publicación. Push requiere autorización separada. No se realizó staging, commit ni push por registrar este PASS.

## Autorización de versionado y paquete final

El usuario autorizó expresamente «Autorizo staging y commit» para los siete archivos del alcance. Se conserva la aprobación «Ayuda PASS» de ambas vistas y «Consolidación PASS» de la entrega. Las referencias anteriores a autorización pendiente son históricas. Los tres archivos ajenos permanecen excluidos; push no autorizado.

Para que el paquete incluya exactamente las vistas versionadas, se generará después del commit en la subcarpeta privada consolidacion-muestra-20261115/version-final/, conservando intactos los respaldos anteriores. Archivos vigentes previstos: codigo-muestra-interna-final.zip, respaldo-muestra-interna-final.zip y delivery-manifest.json; este último identificará el hash real del commit, SQL, archivos y verificaciones. El procedimiento de extracción/HTTP aislado se repetirá únicamente sobre el paquete final y sus dos vistas ajustadas; no se repite la VF del usuario ni se importa SQL. Los resultados y hash del commit quedarán en artefactos externos para evitar modificar el commit que identifican.
