# TASK-EPIC002-CREDENCIALES-SEGURAS-001

## Cierre vigente

El usuario confirmó «push ejecutado siguiente task» después del commit d75fa93cbe758f0f04c3ed16db97e4b04b26a429. Se registra el push según su confirmación, sin repetir comprobación remota. Task cerrada con Review Técnico aprobado, 247 comprobaciones automatizadas conformes, lint 12/12 y VF-1 a VF-4 PASS. Las referencias posteriores a autorizaciones pendientes describen etapas anteriores. El cierre no autoriza publicación en Internet ni implementación de otra Task.

## Autorización y alcance

15/11/2026: «Autorizo la Task de credenciales seguras». Base: rama refactor/fase-0-seguridad, HEAD 9f5864e126e1526e3b7a0a76a5ca562e0deabbf6. Sin cambios rastreados iniciales. Se preservan los tres archivos ajenos no rastreados (ADR-003, aviso JSON y PDF de programa).

Centralizar hash Argon2id en altas y cambios explícitos de contraseña; conservar contraseñas al editar con campo vacío según contrato existente; adaptar login con actualización progresiva de credenciales heredadas después de validar autorización, sin cambiar roles ni habilitar cambios de contraseña Estudiante. Conservar el contenido exacto de las nuevas contraseñas. Las heredadas mantienen la comparación SQL previa hasta su primer ingreso correcto; desde ese momento se exige exactamente la contraseña escrita en ese ingreso, incluidas mayúsculas. No hay conversión masiva ni ejecución de migraciones.

Inventario de solo lectura: 27 credenciales heredadas, 0 hashes modernos y 0 cadenas hexadecimales SHA512 de 128 caracteres. PHP 8.2.12 dispone de Argon2id; hash de 97 caracteres frente a login.pass VARCHAR(200). No se requiere DDL. Instalaciones futuras requieren PHP con soporte Argon2id; no existe fallback a texto plano. Dos rutas ed.pass.php antiguas, sin consumidores encontrados y con dependencias faltantes, quedan retiradas con respuesta 410; no se crea un mecanismo de recuperación o privilegio nuevo.

Archivos modificados y revisados (15): src/Security/PasswordCredentials.php; src/Model/{Login,Admin,Docente,Estudiante}.php; ajax/{login,usuario,admin,docente,estudiante}.php; ed.pass.php; form-doc/ed.pass.php; esta Task, docs/TASKS.md y docs/roadmap/ROADMAP.md. El alta Estudiante también usa el validador central para rechazar bytes nulos antes de escribir. Pruebas y datos ficticios fuera de htdocs/Git. Sin staging, commit ni push autorizados.

## Criterios y validación previstos

- Hash en todos los escritores activos, sin persistir secretos en claro ni devolver hashes por READ/login.
- Acceso compatible de cuentas heredadas; rechazo de contraseña incorrecta y del propio hash; contraseña nueva sensible a mayúsculas, Unicode y espacios, sin truncamiento a 72 bytes.
- Conservar ownership, roles, inactivación, atomicidad y rollback. Revalidar credencial bajo bloqueo antes de actualizarla para no pisar un cambio concurrente.
- Cambios vacíos preservan credencial; altas y cambios no normalizan el secreto.
- Ensayos aislados de modelos/endpoints y SQL nativo; Review explícito antes de VF del usuario. La base viva se consulta únicamente para inventario/metadatos.

Referencias: [password_hash](https://www.php.net/manual/en/function.password-hash.php), [password_verify](https://www.php.net/manual/en/function.password-verify.php), [password_needs_rehash](https://www.php.net/manual/en/function.password-needs-rehash.php).

## Implementación y evidencia

Argon2id usa 65536 KiB, 4 iteraciones y 1 hilo; PHP genera la sal. Todos los escritores hashean el texto recibido, incluso si parece un hash. El login verifica hashes con password_verify y los actualiza cuando corresponde; nunca los compara mediante SQL. Ante email ambiguo o hash conocido dañado rechaza la autenticación. Las rutas antiguas devuelven 410 sin escrituras. js/buttons.js conserva un handler huérfano para #editar_pass, sin ese elemento en las vistas actuales; los botones vigentes #btn_cambiar_pass conservan sus editores.

La conversión ocurre después de validar permisos, identidad y actividad. Una transacción bloquea el login y vuelve a comprobar la credencial antes de actualizarla. Si cambió entre la primera consulta y el bloqueo, se rechaza el ingreso anterior sin sobrescribirla. Fallos de begin, escritura y commit no conceden sesión ni dejan hash parcial. No se amplía autorización ni se corrige la carrera general entre lectura de permisos y creación de sesión; AcademicSession conserva su revalidación en cada petición.

Artefactos privados: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/credenciales-seguras/. No se incorporan secretos ni snapshots de cuentas a Git. Comandos ejecutados desde ese directorio:

| Comando | Resultado | Cobertura |
| --- | --- | --- |
| `C:/xampp/php/php.exe ../credenciales-native.php` | 50/50 | MariaDB real, tabla temporal InnoDB; hash, Unicode, espacios, barras, salto de línea, más de 72 bytes, mayúsculas, collation heredada, bcrypt y rehash, hash dañado, email ambiguo, cambio de credencial antes del bloqueo y entradas inválidas. Ninguna cuenta persistente modificada. |
| `python test-integration.py` | 54/54 | Endpoints reales en SQLite: altas, login, guía, estados, inactivación/reactivación, roles acumulativos y transición administrativa a Docente. |
| `python test-backend.py` | 91/91 | Edición Docente: ownership, roles, conflictos, rollback, fallos de transacción, campo vacío y cambio explícito. Copia de suite previa con expectativa adaptada al hash y texto exacto; evidencia histórica intacta. |
| `node test-frontend.cjs` | 8/8 | Formulario Docente real mediante VM/DOM adaptado. |
| `python test-credentials.py` | 37/37 | Altas Admin/Comité/Docente/Estudiante sin normalizar contraseña, cambios administrativos, vacío, rechazo de contraseña anterior/case/espacios incorrectos, entradas inválidas, fallos de conversión, READ/respuestas/sesión sin secretos y rutas retiradas 410. |
| `C:/xampp/php/php.exe test-login-model.php` | 7/7 | Escritores genéricos Login, conservación con null, cambio explícito, permisos sin secretos y rechazo de contraseña incorrecta/hash. |
| `python check-runtime.py` | 12/12 lint | Apache PHP 8.2.12 con Argon2id confirmado; probe temporal protegido por loopback/token retirado en finally. |
| `git diff --check` | Conforme | Sin errores de whitespace; avisos LF/CRLF del repositorio no son errores. Staging vacío. |

Total: **247/247 comprobaciones funcionales automatizadas y 12/12 lint**. SQLite no prueba contención entre conexiones InnoDB. La suite nativa prueba SQL y revalidación de credencial, sin afirmar un ensayo simultáneo de bloqueo. No es VF de navegador. Durante las pruebas no se ingresó con cuentas reales ni se modificaron sus credenciales.

## Review Técnico

Se examinó el diff completo de 11 PHP rastreados, el helper nuevo y tres documentos. Inventario de escritores PHP y consumidores JS revisado: no quedan escritores activos de texto plano/SHA512. READ y login mantienen datos de perfil/permisos sin hashes. Estudiante no obtiene capacidad nueva de cambiar contraseña; editar email conserva su credencial mediante null. Se mantienen estados, roles y relaciones académicas. SQL parametrizado; transacciones existentes de alta/edición conservadas. Los tres archivos ajenos iniciales permanecen excluidos. No hay migración, staging, commit ni push.

**REVIEW TÉCNICO — APROBADO PARA VF**. Criterios técnicos conformes. Posteriormente el usuario confirmó expresamente «VF-1, VF-2, VF-3 y VF-4: PASS»: las cuatro VF quedan aprobadas. No implica aprobación para publicar en Internet.

Riesgos: las cuentas sin ingreso correcto siguen heredadas hasta entrar o recibir un cambio autorizado. Los respaldos previos mantienen su contenido anterior y deben permanecer privados. Volver al login antiguo impediría ingresar a las cuentas convertidas: una reversión debe conservar el verificador compatible. Fortaleza de contraseña, recuperación, rate limiting y CSRF conservan planificación separada. Se mantienen los límites de los formularios actuales (por ejemplo Admin 20), aunque el backend admite hasta 200 caracteres.

## VF aprobada por el usuario: casos ejecutados

1. Cerrar sesión e ingresar con una cuenta existente; salir y volver a ingresar escribiendo exactamente la misma contraseña. Esperado: acceso y perfil habituales en ambos ingresos.
2. Desde Admin, cambiar la contraseña de un Docente de prueba a `MuestraX9!2026`. Salir: la anterior debe fallar y la nueva debe ingresar. `muestrax9!2026` debe fallar. Después, guardar una edición personal dejando vacía la contraseña: debe seguir ingresando con `MuestraX9!2026`.
3. Autoalta Estudiante con correo nuevo, aceptación marcada y contraseña `AlumnoX9!2026`; ingresar con ella. Esperado: perfil de postulante, sin cambiar automáticamente su estado.
4. Desde Admin, crear una cuenta administrativa de prueba con `AdminX9!2026`; salir e ingresar con ella. Esperado: acceso con el rol elegido.

Resultado informado por el usuario: VF-1 PASS (cuenta existente), VF-2 PASS (cambio y conservación de contraseña Docente), VF-3 PASS (autoalta Estudiante) y VF-4 PASS (cuenta administrativa nueva). Esta evidencia funcional procede de su confirmación, no de ejecución de navegador por el agente. Se requiere autorización específica de staging y commit; push separado.

Autorización posterior del usuario: «Autorizo staging y commit». Autoriza versionar exclusivamente los 15 archivos de esta Task; los tres archivos ajenos iniciales permanecen excluidos. Las menciones anteriores a autorización pendiente describen etapas previas. No autoriza push.

Estado: implementada, Review Técnico aprobado y VF-1 a VF-4 PASS; staging y commit autorizados. Sin cambios adicionales de aplicación ni migración al registrar esta autorización. Push pendiente de autorización separada.
