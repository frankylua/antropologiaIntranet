# Muestra interna — CRUD Docentes y Estudiantes

Preparación: **14/11/2026, America/Santiago**. Objetivo acordado: **20/11/2026**. Destino confirmado por el usuario: este equipo, **localhost**. Revisión de aplicación: **f482ed898bafa23abca1877f14dafd570a05781c**, rama `refactor/fase-0-seguridad`.

## Dictamen actual

**LISTA PARA MUESTRA INTERNA LOCALHOST. REVIEW TÉCNICO APROBADO Y VF OPERATIVA PASS («Guías PASS»).** D-01 resuelto con autorización expresa: el usuario eligió resolver las seis asignaciones y seleccionó el Docente de prueba ID usuario 18. Se confirmó que eran fichas TEST, IDs usuario 7–12, con autorreferencia como guía. Tras respaldo previo, una transacción cambió solo prof_guia de esas seis filas a 18; se comprobaron los demás campos y las otras 38 tablas sin cambios. Las seis lecturas reales de nombre de guía ahora resuelven ID 18 y el preflight detecta cero guías sin perfil Docente.

Respaldo actualizado después del ajuste y verificado. **No se ensayó una restauración.** El usuario confirmó visualmente las seis fichas TEST mediante «Guías PASS»; se cierra D-01 y no se repiten las VF ya aprobadas del CRUD. No se certifican módulos fuera del alcance de esta muestra localhost.

## Versión y cierres conciliados

| Incremento | Evidencia de cierre | Situación |
| --- | --- | --- |
| Protección personal Estudiante | Commit 4e002b7, VF del usuario y excepción expresa de VF-08 | Publicado; no reabrir las VF aprobadas |
| Popup de muestra | Commit 55f0aa3, presentación aprobada; obligatoriedad incorporada después | Vigente como texto de prueba |
| CRUD e inactivación | Commit 498134e; migración reportada por el usuario y verificada; «docente estudiante pass»; push ejecutado y aprobado por el usuario | Publicado |
| Edición personal Docente atómica | Commit f482ed8; «VF Docente PASS» en cuatro recorridos; push ejecutado informado por el usuario | Publicado |
| Preparación de entrega | TASK-ENTREGA-MUESTRA-INTERNA-001 | Documentación nueva, aún sin commit/push |

Se usa la confirmación de push del usuario; no se hizo una nueva comprobación remota. Las fuentes antiguas que dicen «no iniciada» o presentan DELETE físico son históricas: prevalecen los incrementos y decisiones posteriores enlazados en esta guía.

## Alcance comprobado y evidencia disponible

| Operación / cruce | Evidencia técnica | Evidencia funcional y límite |
| --- | --- | --- |
| Altas Admin y autoaltas de ambos perfiles | Suites previas y cadena de 54 comprobaciones integradas | PASS del usuario en altas Admin y autoaltas; otras variantes específicas no se inventan como VF |
| Docente autoalta Pendiente y alta administrativa Aceptado | Cadena nueva con persistencia entre peticiones; permiso 4 forzado | VF anterior de autoalta Docente PASS |
| Aceptación/rechazo y habilitación Docente | Login real en fixture tras cada estado; propio permitido, acceso académico condicionado | Cobertura técnica nueva; no se declara otra VF del usuario |
| Consulta, búsqueda y filtros Estudiante | Suite CRUD y pruebas previas | Usuario confirmó consulta/filtros PASS |
| Datos personales de ambos perfiles | Suites 71 y 91, frontend 12 y 8; contrato sin exposición de contraseña | Estudiante VF anterior PASS; Docente VF-DP-01 a 04 PASS |
| Datos del programa y estado | Transacciones y referencias comprobadas; cadena integrada usa Comité | Usuario confirmó programa/estado Admin e inactivación/reactivación en ambos perfiles |
| Eliminar = Inactivar, listado y recuperación | 111 pruebas previas; nueva cadena con cuentas persistentes | «docente estudiante pass» del usuario |
| Acceso tras inactivar y otros roles válidos | Cadena Docente+Comité mantiene Comité al inactivar Docente; rol Docente vuelve al iniciar sesión tras reactivar | VF anterior declarada PASS; no se atribuyen cuentas o pasos adicionales al usuario |
| Asignar/retirar rol adicional por Admin | Cadena integrada, Comité no asigna roles; no pierde permiso Docente | Evidencia técnica; no nueva VF manual |
| Admin agrega Docencia a una cuenta Comité existente | Cadena integrada conserva cuenta, credencial y rol administrativo, no duplica Docencia | Evidencia técnica; no nueva VF manual |
| Guía histórico de Docente inactivo | Relación creada en la cadena, lectura posterior a inactivar, mismos IDs | Conservación de historial incluida en VF previa; D-01 documenta datos actuales ajenos a ese caso |
| Arranque y recursos locales | 22 comprobaciones HTTP y 12 controles de autoalta tras iniciar servicios | Disponibilidad real, no una nueva VF autenticada de navegador |

La suite integrada usa endpoints reales, sesiones devueltas por Login y una misma BD SQLite aislada por cadena: cuatro cadenas y **54/54 PASS**. Los bloqueos FOR UPDATE se retiran solo en el fixture. **70 consultas preparadas nativamente en MariaDB, cero errores, sin execute de mutaciones reales.** Esto no simula concurrencia InnoDB ni sustituye aprobación funcional de variantes no ejecutadas por el usuario.

## Observaciones, bloqueantes y diferidos

| ID | Hallazgo | Impacto y tratamiento |
| --- | --- | --- |
| D-01 — Resuelto por autorización | 6 de 10 Estudiantes apuntan como guía a una persona existente en usuario pero sin fila en profesor. Cinco están en estados 1–5 y uno en 6. El modelo actual devuelve cero filas de nombre de guía en los seis casos. | Se identificaron como fichas TEST autorreferentes; el usuario eligió Docente de prueba ID 18. Ajuste transaccional aplicado a seis filas y lecturas verificadas; cero vínculos inválidos al terminar. Comprobación visual PASS comunicada por el usuario. La descripción de la columna anterior corresponde al hallazgo previo. |
| D-02 — Recuperación | ZIP/SQL y hashes verificados; restauración no ejecutada | No afirmar recuperación ensayada ni importar SQL sobre la base viva para probar. |
| D-03 — Aviso de datos | Acuerdos siguen con texto provisional; aprobación institucional/versionado/persistencia pendientes | Uso de muestra interna según lo aprobado. No constituye cierre para publicación en Internet. |
| D-04 — Cursos | Bloqueo institucional independiente documentado en TASKS.md | Fuera del recorrido y de la certificación de esta entrega; no se modifica navegación ni autorización de Cursos. |
| D-05 — Credenciales y despliegue | Se mantiene normalización/almacenamiento heredado y APP_ENV=dev del entorno local | No se cambia como parte de esta preparación ni se certifica un despliegue público. |
| D-06 — Recursos externos | Tipografías Google e iconos Font Awesome usan servicios externos; CSS/JS principal local comprobado | No se ensayó el uso totalmente desconectado. Mantener la conectividad habitual para la presentación. |

Detalle privado de D-01: `REVISION-GUIAS-PENDIENTES.md` en la carpeta externa de artefactos indicada abajo. No incorporar nombres ni configuración privada al repositorio. Si la asignación actual es incorrecta y se confirma el guía correcto, Admin/Comité pueden ajustar la ficha por Datos del programa; si representa un guía histórico válido sin perfil Docente, primero debe definirse el contrato histórico correspondiente, sin sustituir el vínculo por conveniencia.

## Arranque de la muestra en este equipo

1. Abrir XAMPP Control Panel y comprobar Apache y MySQL. Ambos fueron iniciados en segundo plano durante esta preparación porque estaban detenidos. Si ya están activos, no iniciar otra instancia. No se cambió configuración de red ni de servicios.
2. Abrir [Ingreso local](http://localhost/antropologiaIntranet/form-doc/login.php). La raíz [intranet local](http://localhost/antropologiaIntranet/) redirige al destino de la sesión o al ingreso. Usar las cuentas de prueba que el usuario ya empleó en VF; no se crean ni se incluyen contraseñas en esta guía.
3. Entrar como Admin/Comité y abrir [Docentes](http://localhost/antropologiaIntranet/admin/ver.docente.php) o [Estudiantes](http://localhost/antropologiaIntranet/admin/ver.estudiante.php). Refrescar con Ctrl+F5 antes del recorrido si existen pestañas antiguas.
4. Usar una ventana privada separada para la autoalta y para comprobar la sesión del titular, sin mezclarla con la sesión administrativa.
5. Comprobar en pantalla que las seis fichas TEST muestran al Docente de prueba ID 18 como guía. La corrección no oculta registros ni cambia sus estados.

Entorno verificado: PHP CLI 8.2.12, Apache 2.4.58, MariaDB 10.4.32, pdo_mysql/fileinfo/mbstring/json/ctype/session disponibles. APP_URL apunta a `http://localhost/antropologiaIntranet/`; autoload de Composer presente. Las 39 tablas son InnoDB. Estado Docente admite 1/2/3/4; Estudiante 6 es inactivo(a); la guardia de inactivación está habilitada.

## Recorrido sugerido de presentación

No es una solicitud de repetir las VF ni un nuevo PASS automático.

1. Ingreso administrativo → lista → búsqueda y filtro → ficha de un registro de prueba coherente.
2. Editar dirección y datos del programa; volver a abrir la ficha. Mostrar estados y grilla sin mezclar estado con rol de cuenta.
3. En ventana privada, mostrar autoalta con acuerdos de prueba; si se decide registrar durante la presentación, utilizar exclusivamente datos de prueba y correo nuevo. Docente entra Pendiente; Estudiante entra Postulante. Las pruebas técnicas de esta preparación no crearon cuentas reales.
4. Mostrar Eliminar como inactivación, recuperación desde Inactivos y conservación de antecedentes/guías; utilizar cuentas de prueba y anotar el estado original para restituirlo.
5. Mostrar, si interesa, el conflicto de correo y la conservación de los valores guardados. Terminar dejando los registros de prueba en un estado válido conocido.

## Artefactos preparados

Carpeta privada local:

`C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/entrega-muestra-interna-20261114/`

| Archivo | Contenido | Verificación |
| --- | --- | --- |
| codigo-f482ed8.zip | Código rastreado de la revisión f482ed8; no incluye .env, vendor ni el PDF no rastreado | Git archive de commit identificado; checksum abajo |
| respaldo-local-f482ed8-2026-11-14.zip | Código f482ed8, configuración .env privada, vendor, contenido actual de files/ y archivos/, SQL completo y LEEME de respaldo | ZIP sin errores ni entradas duplicadas; 19 archivos de ejecución contrastados; SQL y configuración coinciden con el original |
| respaldo-bd-muestra-20261115-003051.sql | Dump completo de BD; el nombre usa UTC, corresponde a esta preparación del 14/11 America/Santiago | Exit 0, marcador final, 39 CREATE TABLE y SHA-256 verificados |
| delivery-manifest.json | Revisión, archivos, tamaños y checksums | Evidencia externa de preparación; no contiene credenciales |
| integration-results.json / preflight-results.json / http-smoke-results.json | Resultados técnicos y metadatos agregados | Sin escrituras de aplicación reales |
| REVISION-GUIAS-PENDIENTES.md | Detalle privado de D-01 para confirmar con el programa | Solo lectura; mantener fuera de Git/htdocs |

El respaldo completo contiene configuración y datos privados. Mantenerlo fuera de htdocs y del paquete de código compartible. El PDF no rastreado se conserva como parte del respaldo de ejecución, **no se agrega al commit de esta Task**. El borrador institucional y ADR no rastreados no se incorporan al paquete de código.

Checksums SHA-256:

- Código: `25484bfc19919077c65b62f3f841a31a1a7f8e354a24b7bbb3fc49803a88d7ee`.
- Respaldo completo: `ec0024d3ebbf748d49a4e8c901b7232901e61eaec7a22b399def222da41c051f`.
- SQL: `4f7c64eaaf379eabd0263920600bb334f8e56a1dee67db4cbe2f6fbc62ff5abc`.

El respaldo tiene el esquema **posterior a la migración de inactivación**. No aplicar nuevamente las migraciones por orden de archivo ni ejecutar el SQL histórico de Profesor para «instalar»: podría contradecir el estado 4 ya vigente.

## Recuperación preparada, no ejecutada

### Si solo faltan los servicios

Abrir XAMPP y arrancar Apache/MySQL; volver a ingresar. No restaurar la base para resolver únicamente servicios detenidos. Si el panel informa puerto ocupado, comprobar primero si ya existe una instancia activa; no iniciar una segunda ni cambiar puertos del proyecto por ensayo.

### Si se necesita recuperar código o datos

La restauración requiere autorización específica en el momento de realizarla. Esta Task preparó el procedimiento y el respaldo; no reemplazó archivos de ejecución ni importó SQL en la BD viva.

1. Interrumpir el uso de la intranet y detener Apache para evitar nuevas escrituras. Mantener MySQL operativo para exportar e importar. Tomar un respaldo del estado que se va a reemplazar, junto con .env y los archivos actuales, y guardarlo con otro nombre fuera de htdocs.
2. Verificar el SHA-256 del ZIP seleccionado contra delivery-manifest.json. Ejemplo de PowerShell, sustituyendo la ruta por la del archivo preparado: `Get-FileHash -Algorithm SHA256 -LiteralPath 'C:\ruta-privada\respaldo-local-f482ed8-2026-11-14.zip'`.
3. Extraer el respaldo **en una carpeta privada de recuperación fuera de htdocs**. El SQL y la configuración no se deben servir por HTTP. Conservar también una copia de la carpeta de aplicación que se va a reemplazar.
4. Recuperar el código revisado en la misma ruta `C:/xampp/htdocs/antropologiaIntranet`, junto con vendor y los archivos de ejecución del respaldo. Recuperar .env como configuración de la aplicación, según la estructura vigente. No copiar el SQL ni LEEME-RESPALDO.txt al directorio público. El código solo, sin la configuración y vendor, no constituye la recuperación completa.
5. En [phpMyAdmin local](http://localhost/phpmyadmin/), comprobar la instancia y nombre de la base contra DB_NAME de la configuración antes de importar. **El SQL contiene CREATE DATABASE y USE del nombre original**: elegir otra base en la interfaz no redirige esas sentencias. No usar este dump sin adaptar y revisar para ensayar en otra base. Para recuperar la base original, importar únicamente tras confirmar la autorización para sustituir ese estado y el respaldo previo del paso 1.
6. Importar `respaldo-base-datos.sql` desde la copia privada; comprobar finalización sin errores. El respaldo usa hex-blob y contiene también los documentos binarios almacenados en BD. Restaurar los archivos de files/ y archivos/ del mismo snapshot para mantener la correspondencia con el código/BD.
7. Comprobar metadatos: estado Docente 1/2/3/4, catálogo Estudiante 6 inactivo(a), conexión y autoload. Arrancar Apache, abrir login, iniciar una sesión nueva y revisar una ficha de prueba con guía coherente. No reutilizar sesiones antiguas para acreditar que el contexto se reconstruyó correctamente.
8. Si falla, detener la apertura al uso, conservar evidencia y evaluar volver al snapshot anterior completo. No eliminar antecedentes ni reactivar perfiles en masa para forzar compatibilidad.

No se ofrece un comando automático destructivo ni se ha validado una restauración mediante importación. Una futura prueba de recuperación debe realizarse en un destino aislado, con autorización y dump preparado para ese nombre de base, antes de certificar ese criterio.

## Estado de control

- Ningún archivo de aplicación ni esquema fue modificado por esta preparación.
- No se crearon cuentas ni se alteraron roles. Se corrigió prof_guia de seis fichas TEST por elección expresa del usuario; existe respaldo previo y posterior. Se iniciaron Apache/MySQL para la comprobación local.
- Los siete documentos de entrega/conciliación están pendientes de autorización de staging y commit.
- D-01 está resuelto en BD y la VF visual de las seis fichas tiene PASS comunicado por el usuario; no se confunden las lecturas técnicas con esa aprobación manual.

## Evidencia posterior de D-01

Resultados: guide-correction-result.json, con seis filas modificadas, otros campos sin cambios, 38 tablas adicionales sin cambios y seis lecturas de guía conformes. El preflight posterior tiene cero guías sin perfil. El respaldo pre-guide-correction-backup-report.json conserva el estado previo, y database-backup-report.json / delivery-manifest.json identifican el respaldo posterior. El reporte privado de guías se conserva como antecedente y se marca resuelto.

## Confirmación de cierre funcional

«Guías PASS» comunicado por el usuario el 14/11/2026. Preparación y guía actualizadas; muestra interna localhost lista dentro del alcance. Siete documentos pendientes de autorización de staging/commit. El código base sigue siendo f482ed8; no hay nuevo despliegue ni publicación abierta por este registro.


Autorización documental del 14/11/2026: el usuario aprobó staging y commit de los siete documentos de TASK-ENTREGA-MUESTRA-INTERNA-001. La publicación documental por push conserva autorización separada. Las menciones a autorización pendiente anteriores son históricas.
