# TASK-SEGURIDAD-ARCHIVOS-INTERNOS-001

## Cierre vigente

Commit 08073f188c67cc1bee0bafade6aea8c9eae53ecb con los cinco archivos autorizados. Review Técnico, 158 comprobaciones HTTP y VF-ARCHIVOS-1/2 aprobados. El usuario confirmó posteriormente «push ejecutado siguiente task»; se registra su confirmación sin nueva comprobación remota. Task cerrada para el Apache verificado. Las referencias posteriores a autorizaciones pendientes son históricas; no constituye aprobación de despliegue público ni de otra implementación.

## Autorización y base

Usuario: «Autorizo la Task de protección de archivos internos». Base 72058dfdf66672c859dca8195192ab1e8bffbe97, rama refactor/fase-0-seguridad. Al iniciar existen tres cierres documentales modificados (TASKS, ROADMAP y Task de intentos de login): se conservan en este incremento. Excluir ADR-003, aviso JSON y PDF no rastreados ajenos. Sin staging, commit, push o publicación autorizados.

Hallazgo previo: HEAD del Apache local respondió 200 para .env, .git/HEAD, composer.json y una migración SQL. No se consultaron sus cuerpos. No existe .htaccess raíz. Se verificaron mod_rewrite/authz_core y AllowOverride All del directorio de aplicación; la protección existente de files/prog_curso se mantiene intacta.

## Alcance

Agregar .htaccess raíz de aplicación para denegar HTTP a configuración/metadata/código interno, scripts de migración y copias operativas, y deshabilitar listados de directorios. Conservar endpoints, pantallas, estáticos y descargas mediante los guards actuales. No editar Apache global, .env, PHP/JS/modelos, permisos institucionales ni base de datos. No retirar el módulo Cursos: se comprueba únicamente que su descarga técnica existente conserve autorización y bytes, sin redefinir su contrato ni levantar su bloqueo institucional.

Rutas internas: src, vendor, docs, migrations y directorios de metadata (.git, .svn, .hg, .vscode, .vs, .idea, .agents, .codex). Ficheros: .env y variantes, .htaccess/.htpasswd, metadata Git/DS_Store, composer.json/lock, Markdown interno, logs, dumps SQL/SQLite, locks/configuración auxiliar y archivos de respaldo/comprimidos. No se bloquean globalmente JSON, PDF, JS, CSS, imágenes ni rutas .well-known de validación de dominio; las reglas existentes de archivos privados siguen vigentes.

Usar Options -Indexes, FilesMatch/Require y reglas rewrite con [F,END,NC]. No envolver módulos requeridos en IfModule que omita silenciosamente el control: un servidor incompatible debe detectarse antes de publicar. Protección específica de Apache 2.4; otros servidores requieren reglas equivalentes y comprobación propia. No depende del estado de sesión ni confía en el frontend. Includes PHP y autoload son acceso local de servidor y permanecen disponibles.

Archivos previstos: .htaccess, esta Task, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre TASK-EPIC002-LOGIN-INTENTOS-001.md. Cinco archivos, incluidos los tres documentos previos. Fixtures/configuración de servidor de ensayo fuera de htdocs/Git: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/archivos-internos/.

## Criterios

- Configuración, Git, código privado y migraciones devuelven 403/404, nunca contenido. Probar HEAD/GET sin imprimir cuerpos de configuración o metadatos reales.
- Cubrir mayúsculas, rutas normalizadas/variantes y listados; mantener los recursos públicos explícitos y la raíz de la aplicación.
- Verificar autenticación, ambas grillas, CRUD representativo y descarga autorizada en Apache real con .htaccess activo, usando BD/cuentas de prueba aisladas. No usar PHP -S como prueba del guard Apache.
- Verificar descarga sin sesión denegada y archivos PDF privados no accesibles directamente; no cambiar reglas de Curso ni escribir documentos de origen.
- Aplicación/esquema/configuración/datos originales preservados, salvo la nueva política HTTP del proyecto. Review explícito antes de VF; autorizaciones Git separadas.

Referencias: [Apache .htaccess](https://httpd.apache.org/docs/2.4/howto/htaccess.html), [mod_rewrite](https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html), [FilesMatch](https://httpd.apache.org/docs/2.4/mod/core.html#filesmatch).

## Implementación y validación

Se agregó únicamente .htaccess de aplicación y se conciliaron cuatro documentos (esta Task y los tres cierres preexistentes): cinco archivos del alcance. Options -Indexes elimina listados; -MultiViews evita negociación de nombres de archivo. Reglas rewrite [F,END,NC] deniegan directorios internos, variantes de puntos/espacios finales de Windows y archivos .env con sufijos/PATH_INFO. FilesMatch con Require all denied protege configuración, metadata, Markdown, dumps, logs, fuentes auxiliares, claves y copias comprimidas. No hay IfModule que omita silenciosamente los módulos requeridos.

Se preservó files/prog_curso/.htaccess sin cambios: los PDF privados permanecen fuera de descarga directa y se mantienen disponibles mediante el endpoint autorizado vigente. No se modificó PHP, JS, SQL, roles, navegación ni el contrato institucional de Curso. Includes/autoload siguen leyendo archivos locales: el bloqueo es de acceso HTTP.

Artefactos privados: archivos-internos/ bajo la carpeta indicada arriba. Evidencia de comandos:

| Comando / comprobación | Resultado |
| --- | --- |
| `python ../prepare-files-task.py` | Baseline de seis HEAD: .env, Git, composer, migración y listados assets/css respondían 200; cuerpos no leídos |
| `C:/xampp/php/php.exe state.php before` | Huellas readonly de 39 tablas InnoDB, esquema y configuración antes del trabajo |
| `python test-origin-http.py` | **115/115 PASS** en el Apache original: HEAD/GET internos denegados, mayúsculas/normalización/encoding/PATH_INFO/ADS de Windows, listados cerrados, estáticos/páginas públicos disponibles y canarios de 32 extensiones internas bloqueados |
| Canarios públicos del mismo ensayo | JSON y extensión PDF no bloqueados globalmente; archivo de challenge .well-known accesible y su directorio sin listado. Canarios sintéticos retirados en finally; no documentos personales nuevos |
| `C:/xampp/apache/bin/httpd.exe -t -f .../httpd-test.conf` | Syntax OK de la configuración del Apache privado; los requests HTTP posteriores verifican efectivamente el .htaccess de aplicación |
| `python test-apache-http.py` | **43/43 PASS** en Apache mod_php real, código 72058df con el .htaccess nuevo, BD aislada y cuenta sintética: ingreso, seis vistas, ayudas cerradas, ambas grillas, CSRF/relogin, lectura y guardado administrativo representativo, descarga PDF autorizada/hash/cabeceras, denegación sin sesión y política interna también para Admin |
| `C:/xampp/php/php.exe state.php after` | 39 tablas, filas, esquema y .env de origen sin cambios |
| `git diff --check` y conciliación de alcance | Conforme; solo los cinco archivos declarados, sin staging ni archivos ajenos incorporados |

Total: **158/158 comprobaciones HTTP PASS**, realizadas contra Apache con las reglas activas. No se usa PHP -S como evidencia de este control ni se confunde validación de config global con ejecución de .htaccess. Se reutilizan los Review/VF previos del CRUD/login, cuyos archivos ejecutables no cambian.

La primera validación de config del Apache privado no pudo resolver la ruta privada dentro del sandbox (AH00532). Se repitió con aprobación de ejecución fuera del sandbox y obtuvo Syntax OK; el ensayo se ejecutó con ese mismo permiso. No se cambió la configuración global para resolverlo. La instancia temporal escuchó solo en 127.0.0.1:8102, con PID/logs/sesiones/store propios y variables de proceso DB_NAME=antropologia_muestra_restore_20261114 y APP_URL del ensayo. En finally se detuvo únicamente el árbol del PID iniciado por el script; el Apache original sigue operativo.

El documento del ensayo ya existía en la copia aislada. Se compararon longitud, SHA-256 y cabeceras del flujo de descarga sin imprimir bytes/nombre del documento. El guard de lectura y los permisos de Curso permanecen intactos: esta prueba no es una resolución institucional de Cursos ni autoriza su ampliación. El único WRITE representativo reenvió los mismos datos del perfil administrativo sintético en la base aislada. No se crearon cuentas, documentos o tablas ni se importó SQL de aplicación.

## Review Técnico

Se examinó .htaccess completo y el diff de los cuatro documentos, contrastando rutas con consumidores reales. La política es del lado de Apache y deniega todos los usuarios, también Admin, en las rutas internas; no sustituye autenticación, ownership, CSRF o permisos de endpoints. Las variantes codificadas, mayúsculas, segmentos y nombres de Windows ensayados no sirvieron contenido privado. No se leyeron cuerpos de ficheros internos reales ni se volcaron secretos al informe. Los canarios eran datos ficticios y se eliminaron.

Se comprobó que archivos públicos explícitos (JS/CSS/imágenes/JSON/PDF/challenge) no se bloquean por una regla global incorrecta y que cerrar listados no afecta a index.php. El autoload y la configuración local siguen funcionando bajo mod_php. Las transacciones y contratos READ/WRITE se conservan; las pruebas nativas admiten las operaciones autorizadas y deniegan CSRF/descarga sin sesión como antes.

La regla de PDF privado existente y el endpoint de descarga se conservaron byte a byte. La configuración global de Apache solo se leyó; no se editó ni reinició su instancia original. MySQL y .env de origen conservan las huellas previas. Los tres archivos ajenos siguen excluidos; staging vacío, sin commit, push o migración.

**REVIEW TÉCNICO — APROBADO PARA VF.** Criterios técnicos conformes para el Apache actual; VF del usuario pendiente. No se declara publicación en Internet ni cierre de otros requisitos de producción.

## Operación y límites

- Incluir .htaccess raíz al desplegar. Requiere Apache 2.4, mod_rewrite/authz_core y AllowOverride que admita Options, FileInfo y AuthConfig (o reglas equivalentes del administrador del servidor). Las reglas se aplican por petición; no se necesita reiniciar el Apache local por este archivo.
- Un servidor que ignore .htaccess (por ejemplo AllowOverride None o una tecnología distinta) no queda protegido por el archivo. Verificar los mismos HEAD/GET en el destino antes de habilitar acceso público; no certificar otro hosting a partir de esta prueba local.
- Los includes PHP, Composer y documentación local continúan disponibles por filesystem. Los enlaces a docs por URL web quedan denegados deliberadamente; consultar guías como archivos locales o desde los paquetes privados.
- No colocar secretos ni respaldos en el directorio público aunque existan estas reglas. Las extensiones de archivo comprimido/configuración quedan protegidas; si posteriormente se necesitara publicar uno como recurso legítimo, revisar una ruta/endpoint autorizado específico en su Task, sin retirar el bloqueo global indiscriminadamente.
- La revisión no acredita ausencia de datos privados dentro de archivos públicos de otros nombres/formatos ni sustituye controles de hosting/HTTPS, producción, aviso institucional y módulos pendientes. No se rotan credenciales por un HEAD local sin evidencia de acceso externo.

## VF pendiente

1. **VF-ARCHIVOS-1 — Denegación:** abrir en localhost las rutas `/antropologiaIntranet/.env.example` y `/antropologiaIntranet/composer.json`. Esperado: «Forbidden», «Acceso denegado» o 403; no deben mostrar ni descargar el contenido. Son archivos sin credenciales reales, elegidos para la comprobación visual.
2. **VF-ARCHIVOS-2 — Uso normal:** abrir el ingreso habitual, recargar con Ctrl+F5, entrar con la cuenta de prueba y abrir las grillas Docentes y Estudiantes. Esperado: acceso y presentación normales. No repetir altas/ediciones ni acceder a Cursos para esta VF: la descarga ya tiene evidencia técnica aislada.

Informar VF-ARCHIVOS-1 y VF-ARCHIVOS-2 PASS, FAIL o BLOQUEADA. Sin SQL, consola o inspección de configuración privada. Después de VF aprobada se requiere autorización separada de staging/commit y de push.

Estado: implementada, Review aprobado para VF y pendiente de resultado del usuario. Estimación de referencia: 2–4 horas.

## Resultado funcional

El usuario confirmó «VF-ARCHIVOS-1: PASS» y «VF-ARCHIVOS-2: PASS». Se aprueban la denegación de los dos archivos internos indicados y el ingreso/carga habitual de ambas grillas. Esta evidencia procede de su confirmación, no de ejecución de navegador por el agente. Las referencias anteriores de VF pendiente describen el estado previo a esa confirmación.

Estado vigente: implementación, Review Técnico y ambas VF aprobados. Cinco archivos preparados, pendientes de autorización de staging y commit; push separado. Sin cambios adicionales de política HTTP, datos, configuración global, staging, commit, push o migración por registrar el PASS.

## Autorización de versionado

El usuario autorizó expresamente «Autorizo staging y commit» de los cinco archivos revisados. La política .htaccess se contrastó por SHA-256 con el Review aprobado; las diferencias documentales registran cierres, PASS y autorizaciones. Los tres archivos ajenos no rastreados permanecen excluidos. Las menciones anteriores de autorización pendiente son históricas.

Estado: implementación, Review y VF-ARCHIVOS-1/2 aprobados; staging y commit autorizados. El hash y comprobante del commit se conservarán en archivos-internos/commit-result.json fuera del repositorio. Push requiere autorización separada.
