# TASK-EPIC002-RECUPERACION-CONTRASENA-001

El usuario confirmó expresamente «VF-REC-1, VF-REC-2 y VF-REC-3: PASS». Recepción real del correo, coincidencia de contraseñas, restablecimiento/retorno al login y rechazo de reutilización aceptados funcionalmente. Review Técnico y VF aprobados; pendiente autorización específica de staging/commit de los catorce archivos y push separado. .env conserva la configuración privada del usuario y queda excluido de Git. Las referencias siguientes a VF pendiente son históricas.

## Autorización y estado

Usuario: «ahora se trabajara en recuperacion de contraseña para dejar operativo el requerimiento es ingresar correo y que llegue mail a su correo con link se acceso a un formulario donde podra agregar la nueva contraseña con 2 campos validando que sean iuales y luego redirigir a login». Esta instrucción autoriza implementar la nueva Task. Base 052c14a0bbd76e8c7881d9d37e40ba4f7a369254, rama refactor/fase-0-seguridad. El usuario pausó mientras buscaba el correo y aportó luego la cuenta/configuración del hosting; confirmó finalmente el guardado de la clave real. No se amplía a Reportes.

Estado: IMPLEMENTADA. **REVIEW TÉCNICO — APROBADO PARA VF.** Pendiente recepción real del correo y VF del usuario. Sin staging, commit o push.

## Alcance y conciliación

Catorce archivos: once de aplicación/configuración/dependencias y tres documentales. Ocho rastreados modificados y seis nuevos. Al iniciar la implementación tras la configuración ya estaban modificados composer.json/composer.lock por la instalación de PHPMailer autorizada en esta misma Task. No son cambios ajenos. .env fue actualizado manualmente por el usuario, está ignorado y no forma parte del staging ni del alcance versionable. vendor es runtime ignorado por Git.

| Archivo | Cambio revisado |
|---|---|
| composer.json | Dependencia PHPMailer ^7.2; descripción original preservada. |
| composer.lock | Versión resuelta 7.2.0 y content-hash válido. |
| .env.example | Claves SMTP y ruta privada de recuperación, sin credenciales reales. |
| src/Config/Configuration.php | Valores SMTP opcionales y PASSWORD_RESET_DIR; ausencia de SMTP no impide usar otros módulos. |
| src/Security/PasswordResetStore.php | Tokens hasheados y caducidad en SQLite operativo privado, consumo atómico y límites reutilizados. |
| src/Security/PasswordResetMailer.php | SMTP autenticado SSL/STARTTLS con PHPMailer, sin debug o secretos en respuestas/logs. |
| src/Security/PasswordReset.php | Emisión, verificación, cambio de contraseña y manejo transaccional del login destinatario. |
| ajax/recuperacion.php | POST, CSRF propio, tipos/validaciones, límites, respuestas JSON y limpieza de la sesión solicitante. |
| form-doc/rec.pass.php | Solicitud y formulario de contraseña/confirmación, headers de privacidad y CSRF. |
| form-doc/scripts/recuperacion.js | Envíos, comparación literal, verificación de enlace y redirección al login. |
| form-doc/login.php | Enlace real Olvidé mi contraseña y aviso tras restablecimiento. |
| docs/tasks/TASK-EPIC002-RECUPERACION-CONTRASENA-001.md | Esta Task, Review, evidencia y VF. |
| docs/TASKS.md | Registro del incremento y pendientes. |
| docs/roadmap/ROADMAP.md | Estado del requerimiento, alcance y dependencia de VF. |

## Diseño implementado

1. El usuario ingresa el correo de una cuenta registrada. El correo noreply es remitente; no se presupone que exista como cuenta de acceso a la intranet.
2. Se genera random_bytes(32), codificado como 64 hexadecimales. El store conserva solo SHA-256 del token, id_login y fingerprints del correo/credencial, con vencimiento de 900 segundos. No guarda contraseña ni correo textual. Puede haber varios enlaces vigentes; al completar uno, todos los anteriores quedan invalidados.
3. El link se construye exclusivamente desde APP_URL, nunca desde Host del request. Exige HTTPS fuera de localhost/loopback. Viaja como rec.pass.php#token=...; el fragmento no llega al access log HTTP ni al Referer y se retira de la barra al abrir. JavaScript lo envía por POST al endpoint. No hay scripts/recursos de terceros en la nueva página.
4. El formulario verifica el enlace y pide dos contraseñas iguales. Frontend y backend comparan literalmente, sin normalizar espacios/mayúsculas. Reutiliza PasswordCredentials: formato vigente de 1–200 caracteres Unicode, sin NUL ni solo espacios, hash Argon2id.
5. El token se consume en SQLite con BEGIN IMMEDIATE antes de la escritura; se bloquea el login destinatario con FOR UPDATE en MySQL, se contrastan fingerprints y se actualiza únicamente su pass dentro de transacción. Cambio de correo o contraseña desde otro flujo invalida el enlace. No reactiva perfiles ni cambia roles/permisos.
6. Completar limpia solo la sesión del navegador solicitante, regenera su id y redirige a login.php?reset=ok. No inicia sesión automáticamente. Otros navegadores previamente autenticados no se revocan globalmente por este incremento; se conserva el alcance actual de gestión de sesiones.

El almacén operativo sigue el patrón de SQLite privado del limitador de login, con nombre por aplicación/base y ruta absoluta configurable fuera del webroot. No crea tablas de negocio ni requiere migración MySQL. Utiliza PDO SQLite ya requerido por el limitador existente; en otro hosting debe comprobarse extensión, permisos y ruta. En instalaciones con varios nodos se requiere un diseño compartido antes de desplegar, igual que el limitador local; no se declara esa arquitectura disponible.

## Seguridad y contratos

- Endpoint público por necesidad de recuperación; autoridad de escritura exclusiva del token impredecible ligado a id_login, correo y credencial actuales. No se acepta id_login del cliente como autoridad. SQL parametrizado, sin exposición de hash o cuenta en la respuesta.
- POST obligatorio (405), CSRF de sesión (403), operación inválida 400, validación/enlace inválido 422, presupuesto excedido 429 con Retry-After, indisponibilidad 503. Solicitud admitida devuelve 200 con el mismo mensaje para correo existente/inexistente o fallo de entrega SMTP. Esta respuesta genérica no demuestra entrega efectiva ni garantiza tiempos idénticos; el envío es síncrono.
- Presupuesto de solicitud: 3 por identidad de cuenta y 10 por origen en 15 minutos, espera de 15 minutos. Verificar/completar también consumen presupuesto de origen. REMOTE_ADDR, no cabeceras proxy no verificadas. Reutiliza las transacciones, HMAC y validación de directorio del LoginAttemptLimiter sin cambiarlo.
- Store privado con umask 0077, exclusión de webroot/rutas relativas y rechazo de symlinks de archivos SQLite. Purga vencidos al emitir, límite 10000 tokens; borrar el store operativo invalida enlaces, no elimina cuentas. La privacidad final depende de permisos reales del hosting.
- En SMTP solo ssl o tls y autenticación; se preserva validación de certificados. Sin contraseñas/token en logging operativo o respuestas. PHPMailer instalado desde Composer oficial; no se modificó php.ini: ZIP se habilitó únicamente en el proceso de instalación. composer validate conforme. Referencia técnica: https://github.com/PHPMailer/PHPMailer.
- Mensajes JS por textContent, formulario sin caché, Referrer-Policy no-referrer, CSP de mismo origen, frame-ancestors none y X-Frame-Options DENY. No se altera CrudCsrf, LoginAttemptLimiter, PasswordCredentials, modelos, endpoints previos o reglas académicas.

## Persistencia y reversión

El consumo SQLite y el UPDATE MySQL no constituyen una transacción distribuida. El diseño falla cerrado: un error posterior al consumo exige solicitar otro enlace; no restituye un token potencialmente usado. El UPDATE MySQL hace rollback y conserva la credencial anterior ante fallo. Si el commit ocurrió y falló solo la respuesta, el mensaje indica intentar login o solicitar otro enlace. Fallo de limpieza posterior no vuelve utilizables los tokens antiguos, porque sus fingerprints ya no coinciden.

La transacción bloquea el login y vuelve a validar después de consumir el token; la atomicidad de take impide que el mismo token se use en dos solicitudes. El ensayo de rollback y los rechazos de replay se verificaron; no se presenta un ensayo multiproceso de concurrencia que no se ejecutó.

Reversión del incremento: recuperar conjuntamente los archivos anteriores y dependencias del lock previo solo si fuera necesario, manteniendo el verificador actual PasswordCredentials. Las contraseñas restablecidas permanecen hashes compatibles; no se revierte su valor ni se reactivan tokens. No requiere migración inversa ni restauración de la BD. No se ejecutó rollback del producto.

## Evidencia ejecutada

Artefactos privados fuera de webroot/Git: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/recuperacion-password/.

| Comando / prueba | Resultado |
|---|---|
| C:/xampp/php/php.exe test-service.php | 37/37: formato/entropía/hash, desconocido, vencimiento exacto, contraseñas diferentes, entradas inválidas, actualización/Argon2id, replay, cambio de correo/credencial, invalidación de enlaces hermanos, límites/recuperación, fallo SMTP, URL pública HTTP denegada, store público rechazado y rollback con fallo inyectado. PDO SQLite aislado y transporte de captura. |
| python test-http.py | 24/24: endpoints reales PHP/MariaDB contra BD de recuperación existente, cuenta sintética; CSRF, métodos, mensajes uniformes, token no expuesto, verificación, mismatch, cambio real de la credencial sintética, acceso con nueva/denegación de anterior, logout/CSRF y enlaces. Transporte SMTP sustituido solo en el router privado; credencial sintética restaurada al finalizar. |
| Navegador IAB, fixture ui/ | 10/10: JS real con fetch simulado, formularios, errores mostrados como texto, reintento, fragmento retirado, contraseñas distintas y redirección observada a login.php?reset=ok. El fixture inicial corrigió navegación entre fases y MIME/caché de .php en su servidor estático; no fueron cambios del producto. Resultado ui/dom-results.json. Servidor detenido. |
| php -l sobre siete PHP; node --check recuperacion.js | 8/8 correctos. |
| composer validate --no-check-publish | Conforme; PHPMailer 7.2.0 resuelto bajo ^7.2. |
| smtp-check.php con cliente real | Autenticación SMTP SSL verificada con c1441353.ferozo.com:465, cuenta noreply. message_sent=false; no se envió correo real desde herramientas. |
| state.php before/after y source-verification-final.json | 39 tablas/esquema de origen iguales. .env cambió al guardar el usuario su contraseña SMTP real; no se afirma que ese archivo permaneciera igual ni se incluye en Git. |
| git diff --check, status y staging | Catorce archivos del alcance conciliados; staging vacío; sin commit/push. |

Total: **71/71 comprobaciones automatizadas** de servicio/HTTP/DOM, además de 8 verificaciones sintácticas y autenticación SMTP. No sumar pruebas históricas como reejecutadas. No se enviaron correos a estudiantes/docentes reales ni se cambiaron credenciales de origen durante las pruebas.

Incidencia SMTP: la configuración leída inicialmente conservaba el texto de ejemplo de contraseña; el usuario lo reemplazó y guardó. Autenticación real posterior correcta. El lector admite valores con o sin comillas; no era un requisito usar comillas salvo para preservar espacios de borde. No se reveló la contraseña. La aceptación de SMTP no prueba recepción en la bandeja del destinatario: falta VF real.

## Review Técnico y dictamen

Se inspeccionaron el diff real completo de los rastreados y los seis archivos nuevos. El alcance es recuperación; no incluye Reportes, cambios de estados/permisos o reformulación de políticas de contraseña. Compatibilidad con login confirmada en la copia aislada; configuración opcional sin SMTP no rompe los módulos existentes. READ/WRITE previos no se modifican; solo nuevo endpoint y flujo público. Propiedad de la cuenta deriva del token y de fingerprints vigentes, no del correo/ID enviado al formulario final.

Criterios técnicos de formulario, coincidencia, caducidad, uso único, cambio seguro, rollback, protección de datos y regreso a login: aprobados por inspección y automatización. Autenticación SMTP real: aprobada. Recepción real del mensaje y recorrido de usuario: pendientes de VF.

Riesgos residuales explícitos: entrega/spam/cuotas del hosting fuera del control del código; posible diferencia temporal de respuesta por envío síncrono; no revocación global de sesiones en otros navegadores; store local de un nodo; consumo fallido exige enlace nuevo; URL localhost del correo abre esta intranet solo desde el equipo donde corre la aplicación. Para publicación se debe configurar APP_URL con dominio HTTPS real y verificar runtime/permisos sin inferirlos del WordPress existente. El respaldo de cierre anterior no contiene esta Task aún sin commit.

**REVIEW TÉCNICO — APROBADO PARA VF.** Sin staging, commit, push o publicación. El usuario realiza el cambio de contraseña de su propia cuenta de prueba durante VF.

## VF propuesta

Usar una cuenta propia o TEST registrada en la intranet, cuyo buzón controle el usuario; no usar noreply solo por ser remitente.

- VF-REC-1: abrir login → Olvidé mi contraseña, ingresar el correo registrado y Enviar enlace. Verificar recepción del mensaje, también spam, y abrir el enlace en el navegador de este mismo equipo mientras Apache/MySQL estén activos. Esperado: aparece formulario Nueva contraseña/Confirmar nueva contraseña.
- VF-REC-2: ingresar valores diferentes y Guardar. Esperado: «Las contraseñas deben ser iguales» y no se guarda. Corregir ambos a la misma nueva contraseña y Guardar. Esperado: regreso automático al login con aviso; la nueva contraseña permite ingresar y la anterior deja de hacerlo.
- VF-REC-3: volver a abrir el mismo enlace después del cambio. Esperado: informa que venció, fue utilizado o no es válido y permite solicitar otro; no muestra un formulario utilizable para cambiar nuevamente. La caducidad de 15 minutos ya fue automatizada y no obliga a esperar para esta VF.

Informar VF-REC-1, VF-REC-2 y VF-REC-3: PASS / FAIL / BLOQUEADA. Sin compartir contraseñas ni enlaces/token del correo. Solo después del PASS se solicita autorización de staging/commit de catorce archivos y push separado según el protocolo.
