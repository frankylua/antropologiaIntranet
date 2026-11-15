# Muestra interna — CRUD Docentes y Estudiantes

Aceptación vigente: el usuario confirmó **«Consolidación PASS»** y **«Ayuda PASS»**, y autorizó staging/commit de los siete archivos. Las menciones anteriores de VF o autorización pendientes son históricas. El paquete final se genera sobre ese commit y se identifica mediante el manifiesto privado indicado a continuación; push separado.

## Paquete final de la entrega aceptada

Ubicación privada estable:

`C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/consolidacion-muestra-20261115/version-final/`

Archivos seleccionados: **respaldo-muestra-interna-final.zip**, **codigo-muestra-interna-final.zip** y **delivery-manifest.json**. El manifiesto identifica el commit exacto que incluye la ayuda desplegable y los SHA-256 del código, SQL y respaldo. Se genera después del commit para conservar trazabilidad sin modificar la revisión que identifica; los resultados de esa comprobación quedan en esa carpeta. Utilizar este paquete final cuando su manifiesto registre las verificaciones conformes.

Los paquetes y hashes 93fff17 descritos en las secciones siguientes son el estado anterior conservado. No contienen el ajuste de ayuda y quedan sustituidos como selección de entrega por el paquete final. El procedimiento de recuperación y los límites institucionales siguen vigentes. No extraer ni importar respaldos para la presentación.

Ajuste posterior solicitado durante VF: la ayuda de recuperación de Docentes/Estudiantes queda desplegable y cerrada por defecto, con «Ayuda PASS» confirmado por el usuario. Los paquetes 93fff17 descritos abajo preceden ese ajuste visual y se conservan sin alteraciones. Falta versionar el ajuste y actualizar el paquete vigente antes del cierre definitivo de consolidación.

Guía operativa consolidada el **15/11/2026**, America/Santiago. Presentación prevista: **20/11/2026**, en este equipo mediante **localhost**. Aplicación: **93fff17ca547540d16d58b3a051eae1f3baa77a0**, rama `refactor/fase-0-seguridad`. Esta guía sustituye las instrucciones de preparación de f482ed8; el detalle histórico permanece en Git y en los artefactos privados anteriores.

## Estado de la entrega

**CONSOLIDACIÓN TÉCNICA COMPLETA — REVIEW TÉCNICO APROBADO PARA VF OPERATIVA MÍNIMA.** CRUD, estados/inactivación, recuperación anterior, credenciales seguras y CSRF tienen sus validaciones previas aprobadas por el usuario. El paquete actualizado y las instrucciones todavía requieren la VF operativa mínima de esta consolidación. No se repite el CRUD ni se atribuyen pruebas manuales nuevas al usuario.

| Incremento | Evidencia vigente |
| --- | --- |
| Protección personal Estudiante | 4e002b7; VF aprobada con excepción expresa de VF-08 |
| Acuerdos de muestra y obligatoriedad | Presentación 55f0aa3 y ajustes posteriores; autoaltas Docente/Estudiante PASS; contenido sigue provisional |
| CRUD, programa, estados e inactivación | 498134e; migración reportada ejecutada y verificada; «docente estudiante pass» |
| Edición personal Docente atómica | f482ed8 y «VF Docente PASS» |
| Preparación y recuperación | 834f1d0 / 9f5864e; «Guías PASS» y «Recuperación PASS» |
| Credenciales seguras | d75fa93; 247 comprobaciones y VF-1 a VF-4 PASS |
| CSRF del CRUD principal | 93fff17; 687 comprobaciones y VF-CSRF-1 a VF-CSRF-3 PASS |

Los pushes anteriores fueron confirmados por el usuario; no se repite comprobación remota. La documentación de esta consolidación se prepara después de 93fff17 y todavía no tiene autorización de staging/commit/push. No se modificó código ni esquema para consolidar.

## Iniciar la muestra

1. Abrir XAMPP Control Panel y comprobar **Apache y MySQL**. Si están funcionando, conservar esas instancias. Si un servicio está detenido, iniciarlo; ante un puerto ocupado comprobar primero la instancia existente.
2. Abrir [Ingreso local](http://localhost/antropologiaIntranet/form-doc/login.php). Usar las cuentas de prueba que ya empleaste en VF; esta guía no contiene contraseñas.
3. Recargar con **Ctrl+F5**. Ingresar como Admin o Comité y abrir [Docentes](http://localhost/antropologiaIntranet/admin/ver.docente.php) y [Estudiantes](http://localhost/antropologiaIntranet/admin/ver.estudiante.php).
4. Usar una ficha histórica completa o un registro de prueba creado mediante alta integral. Los TEST EPIC003 parciales de usuarios 7–12 no tienen todos los antecedentes de ficha; sus lecturas vacías conocidas no acreditan fallo de recuperación. No rellenar etnia, líneas o perfiles ficticios para completar esos registros.
5. Para autoalta, usar una ventana privada independiente de la cuenta administrativa. El texto de acuerdos debe presentarse como **texto de prueba para muestra interna**.

Entorno comprobado: Apache localhost disponible; PHP 8.2.12 con Argon2id, MariaDB 10.4.32 y 39 tablas InnoDB. Autoload/configuración íntegros. Docente admite estado 4 Inactivo y Estudiante usa tipo 6 Inactivo(a). No se cambiaron puertos, configuración ni servicios por esta consolidación.

## Recorrido de presentación

Duración orientativa: 10–15 minutos. Es un recorrido de demostración, no una repetición obligatoria de las VF.

1. Ingreso administrativo → grilla → búsqueda/filtro → ficha completa de prueba.
2. Datos personales y del programa; mostrar que estado académico y rol de cuenta conservan sus reglas.
3. Autoalta con aceptación obligatoria: Docente queda Pendiente y Estudiante Postulante. Registrar solo si se necesita mostrar el resultado, con datos de prueba y correo nuevo.
4. Eliminar equivale a **inactivar** ambos perfiles. Admin/Comité pueden reactivar desde el estado del programa; los vínculos y antecedentes históricos permanecen. Una cuenta con otro rol válido lo conserva.
5. Mostrar conservación de contraseña cuando el campo queda vacío. Las contraseñas nuevas/convertidas distinguen mayúsculas y espacios.
6. Si una ficha abierta antes de cerrar sesión y volver a ingresar pide recargar al guardar, recargarla: el token CSRF del formulario anterior fue invalidado. El rechazo no guarda cambios ni reenvía la solicitud automáticamente.

Utilizar registros de prueba para cualquier modificación y restituir sus valores al terminar. No demostrar eliminaciones físicas ni cambiar roles para forzar una pantalla.

## Paquete vigente y ubicación

Carpeta **privada, fuera de htdocs y Git**:

`C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/consolidacion-muestra-20261115/`

| Archivo | Contenido / uso |
| --- | --- |
| `codigo-93fff17.zip` | Snapshot Git de 93fff17; no contiene la configuración .env ni el runtime completo |
| `respaldo-local-93fff17-2026-11-15.zip` | Respaldo privado completo: `app/` contiene código, vendor, configuración y archivos; `database/` contiene el SQL; LEEME explica la estructura |
| `respaldo-bd-muestra-20261115-150705.sql` | Dump completo nuevo de 39 tablas; incluye CREATE DATABASE/USE del nombre original |
| `documentacion-consolidada-2026-11-15.zip` | Cinco documentos actuales, posteriores al commit base; aún pendientes de versionado autorizado |
| `MUESTRA-INTERNA-2026-11-20.md` | Copia externa de esta guía para la presentación |
| `delivery-manifest.json` | Versión, tamaños, SHA-256, archivos y resultados; identifica los artefactos vigentes |
| `native-http-results.json`, `origin-http-results.json`, `source-verification.json` | Evidencia agregada de ejecución y origen sin cambios |

La documentación incluida dentro de `app/docs/` pertenece a 93fff17. Para operar la muestra prevalecen esta guía consolidada y el ZIP documental externo. Los respaldos de f482ed8 siguen conservados como históricos, pero el paquete de esta tabla es el vigente. Los ZIP de intentos parciales conservados para diagnóstico no son respaldos vigentes: no figuran como paquete seleccionado en delivery-manifest.json.

El respaldo completo incluye configuración y datos privados, posiblemente contraseñas heredadas aún no convertidas: conservarlo privado. No poner el ZIP, SQL o .env en el directorio público ni compartirlos como material de presentación. El PDF de ejecución no rastreado se preserva únicamente en el respaldo; ADR-003 y el borrador institucional JSON ajenos siguen excluidos.

SHA-256 de los artefactos principales:

- Código: `08019f48edf60e18c77a745c29717b7ca2ae5112bd294a86ded5ec467284576c`.
- Respaldo completo: `495b2b04759b03d9ecfe4718f83342d2ee5e3dab7cdb1783339d7deba216a64c`.
- SQL: `daa65ef68fba62d8cf933c650bb477575bc924ddbd33f4164a82353dbc0e31bf`.

Comprobación opcional de lectura en PowerShell:

```powershell
Get-FileHash -Algorithm SHA256 -LiteralPath 'C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/consolidacion-muestra-20261115/respaldo-local-93fff17-2026-11-15.zip'
```

## Validaciones de esta consolidación

- Dump: exit 0, marcador final, 39 CREATE TABLE, tamaño y SHA-256 conformes.
- ZIP: CRC, rutas únicas/seguras y 353 entradas extraídas verificadas. Los 223 archivos de código comparados coinciden con la fuente del commit; se respeta la normalización LF/CRLF de Git. Configuración y 19 archivos de runtime coinciden byte a byte con origen.
- **33/33 pruebas HTTP/MariaDB PASS desde el paquete extraído**, contra la base aislada de recuperación existente: login sintético, seis vistas, token estable, lecturas/grillas, guardado administrativo, CSRF, sesión ajena/relogin/recarga y logout. Servidor temporal 127.0.0.1:8099 detenido.
- **7/7 comprobaciones HTTP GET PASS en el Apache original**: ingreso, raíz, ambas páginas de autoalta y recursos locales. Sin iniciar sesión con cuentas reales.
- Huellas antes/después de las 39 tablas, esquema y .env: **sin cambios en origen**. No se ejecutó DDL ni se crearon cuentas. Las peticiones autenticadas de prueba usaron exclusivamente la cuenta sintética de la base aislada.

El ensayo de recuperación anterior importó el respaldo de f482ed8 con autorización y terminó en «Recuperación PASS». En esta consolidación **no se reimportó el SQL nuevo**: se verificó su integridad y se probó el código extraído con la base aislada ya existente. Esa distinción evita afirmar una restauración completa nueva que no se ejecutó.

## Recuperación y contingencia

Si solo falta un servicio, iniciar Apache/MySQL y volver a ingresar. No restaurar la base para corregir únicamente un servicio detenido.

Una recuperación que sustituya código o datos necesita autorización específica y respaldo previo del estado que se reemplazará:

1. Detener el uso de la aplicación y conservar un respaldo nuevo del estado actual, configuración y archivos antes de reemplazar cualquier componente.
2. Verificar SHA-256 del paquete completo contra delivery-manifest.json y extraerlo **fuera de htdocs**, conservando la estructura `app/` y `database/`.
3. Recuperar el contenido de `app/` con su vendor, .env y archivos coherentes. Mantener `database/`, LEEME y ZIP fuera del directorio público. Configurar/verificar el destino antes de iniciar la copia.
4. Revisar el nombre de BD de la configuración y las sentencias CREATE DATABASE/USE del dump. Elegir otra base en phpMyAdmin **no redirige esas sentencias**. Una importación original sustituiría ese estado; no ejecutarla sin su autorización y respaldo previo. Para un ensayo aislado se necesita adaptación/revisión del dump y autorización de ese destino.
5. No ejecutar otra vez las migraciones por orden de archivo: este snapshot ya incluye los estados de inactivación vigentes. Conservar documentos binarios del SQL y archivos del mismo snapshot.
6. Iniciar una sesión nueva y comprobar ingreso, ambas grillas y una ficha completa. Recargar páginas para renovar el token CSRF.
7. No volver al login antiguo de comparación en texto plano: las cuentas convertidas requieren el verificador de hashes. Cualquier contingencia debe conservar código compatible con Argon2id y sus consumidores CSRF.

Ante un fallo de recuperación, conservar evidencia y evaluar el snapshot completo correspondiente. No eliminar antecedentes, reasignar guías o reactivar perfiles en masa para forzar compatibilidad.

## Pendientes fuera de la muestra

| Tema | Estado / límite |
| --- | --- |
| Aviso institucional de datos | Texto provisional. Responsables, contacto, conservación, accesos/hosting y aprobación siguen pendientes del cliente; persistencia/versionado del consentimiento pendiente para publicación |
| Cursos | Bloqueo institucional independiente; fuera del recorrido y de la certificación de esta muestra |
| Publicación en Internet | Sin autorización de despliegue; pendientes revisión de hosting/HTTPS, configuración de producción y demás controles de acceso público |
| Credenciales heredadas | Se convierten tras ingreso correcto o cambio autorizado; los respaldos anteriores mantienen su contenido histórico |
| Recursos externos | Tipografías e iconos dependen de servicios externos; no se certifica funcionamiento totalmente desconectado |

## VF operativa mínima pendiente

1. Abrir esta guía y ubicar el respaldo completo y delivery-manifest.json en la carpeta privada indicada, sin extraer ni importar nada.
2. En localhost, iniciar sesión con la cuenta de prueba habitual y abrir las grillas Docentes y Estudiantes. Esperado: ingreso y ambas grillas disponibles.

Informar **«Consolidación PASS»** o indicar FAIL/BLOQUEADA y el paso. No repetir alta, edición, inactivación, credenciales ni CSRF ya aprobados. Después se requiere autorización separada de staging/commit y de push documental. Esta guía y su paquete no autorizan publicación abierta.
