# TASK-EPIC002-CSRF-CRUD-PRINCIPAL-001

## Autorización, base y alcance

Usuario: «Autorizo la Task de protección CSRF». Base: d75fa93cbe758f0f04c3ed16db97e4b04b26a429, rama refactor/fase-0-seguridad. Credenciales seguras cerrada con push confirmado. Tres documentos de cierre ya modificados al iniciar (TASKS, ROADMAP y Task de credenciales); se preservan e incorporan como trazabilidad. Se excluyen ADR-003, aviso JSON y PDF ajenos no rastreados.

Incorporar token de sesión a escrituras autenticadas de ajax/admin.php, ajax/docente.php y ajax/estudiante.php. Mantener permisos, ownership, transacciones y estados. Conservar tokens y validaciones propios de ambas autoaltas públicas. Las lecturas no requieren token CRUD; no se cambian módulos académicos, Cursos ni sus tokens existentes. Sin DDL, cambios de roles, commit o push autorizados.

Archivos previstos: src/Security/CrudCsrf.php; js/crudCsrf.js; form-doc/footer.php; ajax/{admin,docente,estudiante,login}.php; esta Task, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre documental de TASK-EPIC002-CREDENCIALES-SEGURAS-001.md. Total 11 archivos, incluidos los tres documentos previos. Tests aislados fuera del directorio público.

## Diseño y aceptación

- Token aleatorio de 32 bytes, vinculado al login y conservado por sesión para admitir varias pestañas. Regeneración al cambiar actor y retirada al ingresar nuevamente; logout destruye la sesión según contrato vigente.
- Cabecera X-CRUD-CSRF-Token en POST del mismo origen y rutas exactas de los tres endpoints; no sustituir cabeceras CSRF de otros módulos ni transmitir por URL.
- Validación antes de procesar formularios/transacciones. Sin token o token ajeno/malformado: 403 CSRF_INVALIDO, cero escrituras; método inválido de escritura autenticada: 405. No consumir tokens de autoalta por estos rechazos.
- El token no concede permisos: roles y ownership se siguen comprobando. Las peticiones sin actor pasan a los guards actuales de autenticación/autoalta; no se habilitan escrituras anónimas nuevas.
- Formularios envían el token automáticamente desde el footer común, incluidas altas administrativas que no usan header común. Ante token desactualizado se informa que debe recargar, sin reintento automático ni pérdida forzada de datos.
- Probar operaciones protegidas, token correcto/ausente/ajeno, lecturas, renovación de sesión, autoalta pública y permisos/rollback. Review Técnico explícito antes de VF.

## Implementación y validaciones

Se implementó el alcance previsto en 11 archivos: siete de aplicación y cuatro documentos (incluido el cierre anterior de credenciales). CrudCsrf centraliza las 17 operaciones WRITE: tres Admin, nueve Docente y cinco Estudiante. El guard se ejecuta después del bootstrap de sesión/actividad y antes de analizar campos o iniciar transacciones de escritura. Las peticiones sin actor siguen los guards vigentes de autenticación o autoalta; un token válido nunca sustituye permisos/ownership. No se alteraron modelos ni SQL.

El footer común emite configuración JSON escapada, luego carga el script con versión filemtime antes de los consumidores. Así quedan cubiertas también las altas administrativas con cabecera propia. Un prefilter jQuery envía X-CRUD-CSRF-Token únicamente en POST del mismo origen y pathname exacto de los tres endpoints. No envía secretos por URL ni a otros módulos/orígenes. Las respuestas CSRF_INVALIDO muestran aviso de recarga sin reintentar escrituras ni recargar automáticamente. El token permanece estable entre pestañas; un login correcto retira el anterior y logout destruye la sesión.

Artefactos privados fuera de htdocs/Git: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/csrf-crud-principal/. Las suites de regresión son copias: no se sobrescribe evidencia histórica. El fixture simula el token de una página previa para las peticiones legítimas; la matriz CSRF lo desactiva y especifica cada variante deliberadamente.

| Comando desde artefactos | Resultado | Evidencia |
| --- | --- | --- |
| `python test-csrf-backend.py` | 145/145 | Los 17 WRITE rechazan cabecera ausente/incorrecta/malformada, token de sesión ausente/otro actor/otra sesión y token solo en body. Método incorrecto rechazado. Cero cambios/transacciones abiertas/intentos de escritura en los rechazos CSRF. Lecturas sin token, acceso anónimo no ampliado, permisos sin privilegios nuevos y login invalida token previo. |
| `C:/xampp/php/php.exe test-csrf-token.php` | 13/13 | Aleatoriedad/formato, estabilidad, cambio de actor, reparación de contexto corrupto, login inválido y sesión nueva. Sesiones privadas de prueba. |
| `node test-csrf-frontend.cjs` | 33/33 | JS real mediante VM: header automático en objetos/form-urlencoded, rutas/origen/métodos, localhost y 127.0.0.1, ausencia de fuga a otros destinos y aviso de error sin reintentos. |
| `python test-integration.py` | 54/54 | Altas públicas/administrativas, guía, roles acumulativos, estados, inactivación/reactivación y login en cadenas persistentes SQLite. |
| `python test-backend.py` | 91/91 | Edición personal Docente, ownership, permisos, conflictos, fallos de escritura/transacción y rollback con token válido. |
| `python test-estudiante.py` | 71/71 | READ/UPDATE personal Estudiante, ownership y conservación de credenciales con token válido. |
| `python test-estudiante-crud-activos.py` | 91/91 | CRUD/programa/estados y autoalta Estudiante. |
| `python test-perfiles-inactivos.py` | 111/111 | Sesiones, roles, inactivación/reactivación e historial académico. |
| `node test-frontend.cjs` | 8/8 | Formulario Docente mediante VM/DOM adaptado. |
| `python test-credentials.py` | 37/37 | Altas/cambios de credenciales, campos inválidos, rollback, READ sin secretos y autoalta. |
| `python test-native-http.py` | 33/33 | HTTP/MariaDB reales: seis vistas renderizan token estable antes de sus consumidores; ambas autoaltas anónimas sin token CRUD; tres endpoints rechazan cabecera ausente/errónea; lecturas sin token; perfil administrativo guarda con token; sesión ajena/relogin rechazan token anterior y recarga habilita guardar; logout bloquea formulario anterior. |

Total: **687/687 comprobaciones funcionales automatizadas PASS**. Lint de los seis PHP afectados y `node --check js/crudCsrf.js` conformes (7/7). `git diff --check` conforme; staging vacío. No es VF visual del navegador.

Ajustes de los ensayos: la primera prueba de sesión intentó usar la carpeta global de XAMPP y falló por permisos; se corrigió el fixture para usar exclusivamente sesiones privadas de artefactos (13/13). Una comprobación de intento de escritura contaba el seed inicial: se delimitó el registro de queries después del snapshot. El caso de permiso Docente recibió primero errores por datos incompletos; se completó su payload para alcanzar el guard de permisos. Cinco expectativas de suites anteriores se actualizaron al hash/progresión de credenciales ya aprobados, sin alterar la aplicación ni sus resultados históricos. Todas las pruebas afectadas se repitieron y pasaron.

Las pruebas nativas usan únicamente la base de recuperación previamente autorizada antropologia_muestra_restore_20261114 y su cuenta administrativa sintética, con una instancia PHP temporal en 127.0.0.1:8098, cookie y sesiones propias, configuración DB_NAME verificada por router y servidor detenido en finally. No se ejecutó DDL ni se crearon cuentas. El login puede convertir la contraseña sintética de esa copia según el contrato vigente; la prueba WRITE administrativa reenvía los mismos datos, sin alterar información de personas. No se hicieron escrituras ni logins de cuentas reales sobre la base principal.

## Review Técnico

- Se examinó el diff de todos los archivos, incluidos los tres cierres documentales preexistentes, y ambos archivos nuevos de aplicación. Se conciliaron las 17 operaciones con los handlers reales y sus consumidores jQuery.
- Autenticación/roles/ownership/estados permanecen en los guards existentes; CSRF es una condición adicional, no un permiso. La cobertura de lectura no cambia.
- Las cabeceras se validan con tipos y formato estrictos, binding de login y hash_equals. Un token ajeno o un campo de formulario no permite omitir el control. No se genera un token nuevo para validar una petición entrante.
- SQL, transacciones y rollback conservados, rechazo antes de WRITE; no hay migración ni nuevas capacidades. Los tokens propios de autoalta y los módulos académicos se mantienen independientes.
- Riesgo de compatibilidad visible: una página abierta antes de instalar el cambio o antes de otro login necesita recarga; hay aviso y no se reenvía la operación. El token CSRF requiere la sesión vigente y no incorpora un vencimiento independiente.
- Se preservaron los tres archivos ajenos iniciales. Sin staging, commit ni push. Las pruebas aisladas comprueban contratos y HTTP real; no se atribuye VF del usuario ni contención simultánea InnoDB.

**REVIEW TÉCNICO — APROBADO PARA VF.** Criterios técnicos conformes; VF pendiente. Protección limitada a escrituras autenticadas del CRUD principal: no certifica seguridad global para Internet ni resuelve XSS, fuerza bruta, política institucional de datos o autorización de Cursos.

## VF: acciones concretas

Antes de probar, recargar las páginas con Ctrl+F5 para cargar el script nuevo. Usar fichas/cuentas de prueba existentes.

1. **VF-CSRF-1 — Guardado normal:** como Admin, editar y guardar la dirección de un Docente de prueba y de un Estudiante de prueba. Editar también un dato no sensible del perfil administrativo propio, sin cambiar contraseña. Esperado: los tres guardados funcionan y conservan sus valores al recargar; no aparece aviso CSRF.
2. **VF-CSRF-2 — Formulario anterior a nuevo ingreso:** abrir una ficha de prueba en pestaña A. En pestaña B cerrar sesión y volver a ingresar con la misma cuenta. Sin recargar A, modificar su dirección y Guardar. Esperado: aviso que solicita recargar y ningún cambio guardado. Recargar A para comprobar que conserva el dato anterior; repetir la edición: ahora guarda correctamente.
3. **VF-CSRF-3 — Autoalta pública:** en ventana privada sin sesión autenticada, completar una autoalta de prueba con correo nuevo y aceptación marcada. Esperado: alta correcta con estado postulante/pendiente habitual, sin aviso CSRF del CRUD. Las suites automatizadas ya cubren ambas variantes de autoalta; basta un recorrido visual.

Informar VF-CSRF-1, VF-CSRF-2 y VF-CSRF-3 PASS, FAIL o BLOQUEADA. Sin SQL ni inspección manual de tokens. Después de VF aprobada, staging/commit y push conservan autorizaciones separadas.

## Resultado de VF

El usuario confirmó expresamente «VF-CSRF-1, VF-CSRF-2 y VF-CSRF-3: PASS». Se registran aprobados el guardado normal en los tres endpoints, el rechazo del formulario anterior al nuevo ingreso con recuperación tras recargar y la autoalta pública. Esta evidencia procede de la confirmación del usuario; no se atribuye al agente una VF de navegador. Las menciones previas de VF pendiente describen el estado anterior a esa confirmación.

Autorización posterior del usuario: «Autorizo staging y commit». Abarca exclusivamente los 11 archivos declarados, incluidos los tres documentos de cierre preexistentes. Los tres archivos ajenos no rastreados permanecen excluidos. Las menciones anteriores de autorización pendiente describen etapas previas; esta autorización no incluye push.

Estado: implementada, Review Técnico aprobado y VF-CSRF-1 a 3 PASS; staging y commit autorizados. Código contrastado por SHA256 con el Review aprobado, sin cambios adicionales de aplicación ni migración. Push pendiente de autorización separada. Estimación de referencia: 4–6 horas.
