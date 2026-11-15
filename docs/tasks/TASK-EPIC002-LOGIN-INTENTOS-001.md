# TASK-EPIC002-LOGIN-INTENTOS-001

## Cierre vigente

Commit 72058dfdf66672c859dca8195192ab1e8bffbe97 con los once archivos autorizados. Review Técnico, 363 comprobaciones, sintaxis 6/6 y VF-LOGIN-1 a 3 aprobados. El usuario confirmó posteriormente «push ejecutado siguiente task»; se registra su confirmación sin nueva comprobación remota. Task cerrada; las referencias posteriores a autorizaciones pendientes son históricas. No autoriza otra implementación ni publicación en Internet.

## Autorización y base

Usuario: «Autorizo la Task de protección de intentos de login». Base 989ac6e13e353b3c747d6d07de8ead12405964d5, rama refactor/fase-0-seguridad. Al iniciar existen tres documentos de cierre modificados (TASKS, ROADMAP y Task de consolidación): se conservan en este incremento. Excluir ADR-003, aviso JSON y PDF no rastreados ajenos. Sin staging, commit, push ni publicación autorizados.

## Alcance y diseño

Limitar intentos admitidos de autenticación por cuenta y origen antes de verificar contraseñas. Defaults configurables: 5 intentos/cuenta, 60/origen, ventana 900 segundos y pausa temporal 60 segundos. El intento que alcanza el umbral se procesa; los posteriores reciben 429 con Retry-After y retry_after. Cada contador bloqueado conserva su vencimiento sin prolongarlo por nuevos rechazos. El presupuesto independiente de origen sigue consumiéndose antes de consultar la cuenta y puede activar su propia pausa si alcanza su umbral. Al vencer una pausa, su presupuesto se recupera automáticamente. Un login totalmente válido limpia el contador de cuenta; el origen conserva su presupuesto para limitar también intentos distribuidos entre correos y altas tasas de ingresos válidos. No se modifican roles, estados ni tablas de personas/cuentas.

Persistencia local SQLite privada, compartida entre procesos del mismo servidor y con transacciones BEGIN IMMEDIATE; pdo_sqlite confirmado en Apache PHP 8.2.12. No hay migración de MySQL ni creación de bases de aplicación: el archivo operativo SQLite se inicializa en runtime fuera del webroot. Por defecto se usa una subcarpeta específica de aplicación/BD del directorio temporal de PHP; LOGIN_RATE_LIMIT_DIR permite fijar una ubicación privada persistente. Rechazar rutas dentro de aplicación/document root y rutas de red. Requiere directorio local escribible y extensión pdo_sqlite; fallo de almacenamiento cierra la admisión con 503, no omite el control.

El origen procede exclusivamente de REMOTE_ADDR, con IPv4/IPv6 canónicos; no se confía en X-Forwarded-For aportado por clientes. Primero se reserva el presupuesto del origen, antes de consultar credenciales. Para el presupuesto de cuenta se resuelve id_login con la misma comparación SQL vigente de correo: variantes de mayúsculas/acentos/espacios que resuelvan la misma cuenta comparten contador. Cuentas inexistentes/ambiguas usan una clave derivada del correo normalizado, con las mismas respuestas genéricas. Solo se persisten claves HMAC y contadores/tiempos; no correos, IP legibles ni contraseñas. Limpieza de entradas vencidas y límite de filas para acotar crecimiento.

El aviso de espera debe ser claro, no revelar existencia de cuentas y permanecer visible durante el bloqueo. No reenviar automáticamente credenciales ni ampliar la duración con clicks. Conservar autenticación, hashes, regeneración de sesión, invalidación CSRF, inactividad y permisos actuales. Los tests no saturan cuentas/orígenes reales: usan store y BD aislados.

Archivos previstos: src/Security/LoginAttemptLimiter.php, src/Security/PasswordCredentials.php (resolución canónica sin comprobar contraseña), src/Config/Configuration.php, .env.example, ajax/login.php, form-doc/scripts/login.js, form-doc/login.php (versión del script), esta Task, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre de TASK-ENTREGA-CONSOLIDACION-MUESTRA-001.md. Once archivos. No modificar .env real ni la política institucional. Artefactos privados: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/login-intentos/.

## Criterios

- Persistencia entre sesiones/procesos y admisión atómica bajo concurrencia; 429 sin verificar/modificar credenciales ni conceder sesión, con espera válida.
- Separación de cuentas/orígenes, resistencia a variantes del correo y cabeceras de proxy falsificadas; recuperación por tiempo y limpieza solo de cuenta tras éxito.
- Contadores fuera del webroot, sin secretos/datos legibles y sin fallback inseguro ante corrupción, lock o error de permisos/capacidad.
- Login correcto/incorrecto, perfiles inactivos, roles acumulativos, hashes heredados y CSRF conservan contratos; frontend muestra espera y no reenvía ni inserta HTML del servidor.
- Validar configuración, límites/bordes, concurrencia multiproceso, endpoint y HTTP/MariaDB aislados; Review explícito antes de VF. Cambios Git requieren sus autorizaciones separadas.

Referencias: [OWASP Authentication — Login Throttling](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html#login-throttling), [PHP flock y sus límites](https://www.php.net/manual/en/function.flock.php). SQLite evita depender del comportamiento de flock por proceso/hilo del servidor; el mecanismo elegido y sus límites se probarán localmente.

Límites de despliegue: el store es de un servidor; despliegues con varias réplicas necesitan un contador compartido apropiado. Con proxy inverso, REMOTE_ADDR puede representar al proxy; su integración requiere una lista confiable y calibración previa. Orígenes compartidos pueden necesitar ajustar el umbral. Los límites de cuenta implican pausa temporal inducible por terceros; no se considera MFA o defensa completa contra ataques distribuidos.

## Implementación y evidencia

Implementación en los once archivos previstos (siete de aplicación/configuración y cuatro documentos, incluidos los tres cierres previos). No se modificó .env real, ninguna tabla MySQL, roles, estados ni fichas. El contador operativo SQLite se inicializa automáticamente fuera del webroot con transacciones cortas; la verificación Argon2id ocurre fuera de esas transacciones. Primero se admite el origen, luego se resuelve la identidad de cuenta y se admite su presupuesto. Si el correo cambia de titular entre la resolución y la verificación se rechaza, evitando contabilizar una identidad y autenticar otra.

Un ingreso plenamente válido limpia únicamente el presupuesto de su cuenta después de validar permisos/actividad, contraseña y regeneración de sesión. La transacción MySQL que actualiza un hash y la del contador son independientes: un fallo posterior del contador rechaza conceder la sesión, pero una conversión de contraseña correcta ya confirmada puede conservarse. No se pretende una transacción distribuida. Las respuestas existentes de login conservan su lista de permisos; se agregan 429/503 y 405 para un método no POST. El frontend versionado conserva las contraseñas sin normalizarlas, mantiene el aviso durante la pausa, deshabilita envío y no reintenta automáticamente. Una respuesta de error anterior que llegue tarde no reemplaza el aviso de espera.

Artefactos privados: login-intentos/ bajo la carpeta indicada arriba. Las copias de fixtures inyectan directorios privados, REMOTE_ADDR sintético y límites altos para reutilizar regresiones; los ensayos específicos usan límites bajos explícitos. Ningún parámetro de esos fixtures se expone por POST en la aplicación real.

| Comando desde artefactos | Resultado | Alcance |
| --- | --- | --- |
| `C:/xampp/php/php.exe test-limiter.php` | 41/41 | SQLite/helper reales: umbral, nuevas instancias, cuentas/orígenes independientes, vencimiento exacto, ventana y pausa de distinta duración, éxito, IPv6/IPv4 mapeada, privacidad, limpieza, capacidad, rutas inseguras, archivo/metadata corruptos y parámetros inválidos |
| `python test-concurrency.py` | 2/2 | Veinte procesos PHP independientes: exactamente 5 admitidos y 15 bloqueados; un store retenido por otra transacción rechaza con error controlado en menos de 5 segundos |
| `python test-endpoint.py` | 29/29 | Login real con BD/store aislados: cuenta compartida entre orígenes, spray de correos, cookies/Forwarded-For sin bypass, recuperación, éxito, entradas inválidas, 503 y ausencia de verificación de secreto/cambios de credenciales en los 429 |
| `C:/xampp/php/php.exe test-config.php` | 33/33 | Defaults, compatibilidad de configuración anterior, archivo/entorno y rechazo de valores fuera de límites |
| `node test-frontend.cjs` | 24/24 | JS real por VM: 401/403/503/429, espera visible, countdown, ausencia de reenvío, recuperación, mensajes sin HTML, timer anterior/respuesta tardía y contraseña exacta |
| `C:/xampp/php/php.exe test-canonical-native.php` | 8/8 | MariaDB real, tabla temporal: mayúsculas, acentos y espacios finales resuelven mismo id_login; desconocidos/ambiguos no inventan identidad; SQL parametrizado |
| `python test-native-http.py` | 24/24 | HTTP/MariaDB con cuenta sintética de recuperación: Retry-After coincide con JSON, contraseña correcta durante pausa rechazada, cookies/correo acentuado/proxy falso sin bypass, mensaje genérico para inexistentes, recuperación por tiempo, limpieza tras éxito, límite de origen y 503 real ante corrupción |
| `python test-integration.py` | 54/54 | Altas, login, guía, estados, roles acumulativos, inactivación/reactivación y transición a Docente |
| `python test-perfiles-inactivos.py` | 111/111 | Autenticación/actividad/estados/permisos, historial y cuentas con otros roles válidos |
| `python test-credentials.py` | 37/37 | Altas/cambios, contraseñas exactas, rollback, READ sin secretos y rutas retiradas |

Total: **363/363 comprobaciones automatizadas PASS**. Sintaxis de cinco PHP afectados y `node --check form-doc/scripts/login.js` conformes, **6/6**. Configuración adicional validada en su suite. `git diff --check` conforme y staging vacío. No es VF de navegador.

Preflight Apache: PHP 8.2.12 con pdo_sqlite y temporal escribible. El helper pudo inicializar su store predeterminado con el Apache original; el probe no reservó intentos ni requirió conexión MySQL, se retiró en finally y no imprimió ubicación/secretos. Pruebas de carga/denegación exclusivamente en stores de artefactos y cuenta sintética de antropologia_muestra_restore_20261114; servidores temporales 127.0.0.1:8101 detenidos. Los tests HTTP usan pausa de cinco segundos para comprobar expiración sin cambiar el default real de 60 segundos.

Comparación readonly posterior contra las huellas de consolidación 989ac6e: **39 tablas, filas, esquema y .env originales sin cambios**. Las operaciones sobre la tabla de correo del ensayo nativo fueron temporales y se descartaron con su conexión. No se importó SQL ni se modificaron credenciales/roles de cuentas reales.

Ajustes de validación: el primer runner trataba la sesión PHP vacía (serializada como []) como un diccionario Python y falló en ocho comprobaciones pese a recibir 429/503 correctos. Se corrigió solo esa interpretación del fixture, se repitió la suite y pasó 29/29. El Review añadió protección de la pantalla frente a respuestas 401 tardías y ensayos de pausa mayor que ventana; las suites afectadas se repitieron (24/24 y 41/41). Los demás PASS se conservan porque no cambió su código backend.

## Review Técnico

Se examinó el diff completo de los nueve archivos rastreados y los dos archivos nuevos. El alcance coincide con la autorización. Los tres cierres documentales iniciales se conservaron; ADR-003, aviso JSON y PDF permanecen excluidos. Las respuestas no revelan correo, IP, id de cuenta, clave HMAC, contraseña ni detalles de SQLite/ruta. No se desactiva el límite al fallar: 503 con mensaje genérico y Retry-After 60.

Autenticación, roles, ownership y capacidades permanecen en el constructor de contexto existente. El contador no otorga permisos; la sesión solo se asigna tras éxito completo. Se conservan hashes y su revalidación transaccional y la invalidación CSRF del login. Lecturas y escrituras CRUD no se alteraron; las regresiones verifican consumidores. SQLite reserva con BEGIN IMMEDIATE, usa parámetros y rollback; el ensayo multiproceso comprueba admisión sin superar el límite. El contador mantiene un máximo de 10.000 entradas y elimina vencidas. Rutas de store verificadas mediante resolución canónica contra aplicación/document root; no se admiten rutas relativas/de red ni fichero de BD enlazado.

**REVIEW TÉCNICO — APROBADO PARA VF.** Criterios técnicos conformes. Pendiente VF del usuario; no autoriza publicación en Internet, staging, commit ni push.

## Operación y riesgos residuales

- Mantener pdo_sqlite disponible y directorio local privado escribible. Para producción configurar LOGIN_RATE_LIMIT_DIR fuera del webroot; la ubicación por defecto depende del temporal de PHP. El store contiene estado operativo temporal, no reemplaza el respaldo MySQL; no restaurarlo desde un snapshot antiguo para prolongar pausas. Borrar el store o limpiar el temporal reinicia presupuestos, por lo que su ubicación debe protegerse.
- Defaults: 5 intentos por cuenta, 60 por REMOTE_ADDR, ventana 900 s y pausa 60 s. Los ingresos exitosos no restablecen el presupuesto global de origen. Una red compartida/proxy puede necesitar calibración; nunca confiar automáticamente en cabeceras aportadas por usuarios.
- Alcance de un servidor. Réplicas, red de almacenamiento o proxy confiable requieren decisión de despliegue y contador compartido adecuados; no se declara seguridad global contra ataques distribuidos ni MFA.
- Una cuenta puede recibir pausa temporal inducida por terceros; no cambia su estado académico ni persiste un bloqueo administrativo. Cada contador conserva su vencimiento, pero un segundo presupuesto puede activar su propia pausa.
- Fallo/corrupción/contención o capacidad del store pueden impedir temporalmente nuevos ingresos (503); se privilegia no omitir el límite. La limpieza automática permite recuperar capacidad tras vencer entradas. Mensajes genéricos no equivalen a una mitigación completa de canales temporales de enumeración.
- El paquete de muestra 989ac6e permanece identificado como entrega ya aprobada; no se reescribe para aparentar que contiene este incremento pendiente de versionado.

## VF pendiente

Recargar el ingreso con Ctrl+F5. Usar una cuenta de prueba existente cuya contraseña correcta conozcas, no una cuenta administrativa indispensable. No compartir la contraseña.

1. **VF-LOGIN-1 — Ingreso normal:** entrar con esa cuenta y cerrar sesión. Esperado: acceso y perfil habituales.
2. **VF-LOGIN-2 — Pausa:** con el mismo correo, enviar cinco contraseñas incorrectas. En el sexto intento usar la correcta: debe mostrar aviso de espera y deshabilitar Ingresar durante el tiempo indicado; no debe conceder acceso ni reenviar el formulario solo.
3. **VF-LOGIN-3 — Recuperación:** esperar a que termine el contador (hasta 60 segundos), comprobar que Ingresar se habilita y pulsarlo con la contraseña correcta. Esperado: ingreso habitual, sin reactivar la cuenta ni cambiar su estado.

Informar VF-LOGIN-1, VF-LOGIN-2 y VF-LOGIN-3 PASS, FAIL o BLOQUEADA. Si ya existían intentos previos, la pausa puede aparecer antes: esperar a su vencimiento y repetir el bloque. Sin SQL, inspección de archivos ni pruebas adicionales del CRUD.

Estado: implementada; Review aprobado para VF; pendiente VF del usuario y autorizaciones Git separadas. Estimación de referencia: 4–6 horas.

## Resultado funcional

El usuario confirmó expresamente «VF-LOGIN-1, VF-LOGIN-2 y VF-LOGIN-3: PASS». Quedan aprobados ingreso normal, pausa tras intentos repetidos y recuperación automática con la contraseña correcta. La evidencia funcional procede de su confirmación, no de una VF de navegador ejecutada por el agente. Las menciones anteriores de VF pendiente describen el estado previo a esa confirmación.

Estado vigente: implementación, Review Técnico y tres VF aprobados; once archivos preparados, pendientes de autorización de staging y commit. Push separado. No se modificó código ni se ejecutó staging, commit, push o migración por registrar el PASS.

## Autorización de versionado

El usuario autorizó expresamente «Autorizo staging y commit» para los once archivos revisados. Se verificó por SHA-256 que los siete archivos de aplicación/configuración coinciden con el Review aprobado; las diferencias documentales registran sus PASS y autorizaciones. Los tres archivos ajenos permanecen excluidos. Esta autorización no incluye push.

Estado: implementación, Review y VF-LOGIN-1 a 3 aprobados; staging y commit autorizados. Las menciones anteriores a autorización pendiente describen etapas previas. El hash y comprobante del commit se registrarán en login-intentos/commit-result.json fuera del repositorio; push requiere autorización separada.
