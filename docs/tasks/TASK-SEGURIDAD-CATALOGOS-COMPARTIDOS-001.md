# TASK-SEGURIDAD-CATALOGOS-COMPARTIDOS-001

El 2026-11-16 el usuario autorizó expresamente staging y commit de los seis archivos de TASK-SEGURIDAD-CATALOGOS-COMPARTIDOS-001, tras Review Técnico y VF-CAT-1/2 PASS. Se conserva el alcance aprobado y se excluyen los tres archivos no rastreados ajenos. Push pendiente de autorización separada; las menciones anteriores a autorización de commit pendiente son históricas.

El usuario confirmó expresamente «VF-CAT-1 y VF-CAT-2: PASS». TASK-SEGURIDAD-CATALOGOS-COMPARTIDOS-001 tiene Review Técnico y validación funcional aprobados. Pendiente autorización específica de staging/commit de los seis archivos conciliados; push separado. Las referencias anteriores a VF pendiente corresponden a la etapa previa. No se modificó aplicación ni se ejecutaron operaciones Git de escritura al registrar esta confirmación.

## Autorización y alcance

Usuario: «Autorizo la Task de catálogos compartidos». Base 16e0559, rama refactor/fase-0-seguridad. Tres documentos preexistentes de cierre/propuesta se conservan; ADR-003, aviso JSON y PDF no rastreados ajenos quedan excluidos.

Dos archivos de aplicación: js/funcAjax.js y form-doc/footer.php. Proteger ajaxSelect, ajaxListas y mostrarTabla con el helper CrudHtml existente, manteniendo transporte, formas indexadas/nominales de respuesta, ids, títulos, texto/entidades históricas, controles y condición de Otro para países/read_cursos. Versionar funcAjax.js con filemtime. No modificar endpoints, capacidades, permisos, SQL, datos, esquema ni reglas de catálogos. No ampliar a otros renderizadores académicos.

Cuatro documentos: esta Task, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre de TASK-SEGURIDAD-SALIDA-HTML-CRUD-001.md. Seis archivos conciliados. Sin staging, commit, push o publicación autorizados.

## Validación prevista

Ejecutar las funciones reales en navegador con jQuery real y respuestas ficticias interceptadas, sin conexión a la BD: etiquetas y atributos hostiles, entidades heredadas, acentos, vacíos, opciones, selección por valor y eventos de botones. Comprobar eliminación visible solo cuando corresponde, títulos y reglas de Otro. Inspeccionar consumidores y orden de carga; sintaxis JS/PHP y diff. Review Técnico antes de VF; estimación de referencia 2–3 horas, sujeta a hallazgos.

Estado: implementada. REVIEW TÉCNICO — APROBADO PARA VF; VF del usuario pendiente.

## Review Técnico

Se examinó el diff real completo contra 16e0559, el archivo nuevo de Task y los consumidores. Seis archivos conciliados: cinco rastreados modificados y uno nuevo. Los tres no rastreados ajenos se mantienen intactos y excluidos.

| Archivo | Resultado de revisión |
|---|---|
| js/funcAjax.js | Seis sustituciones puntuales: etiquetas de plantilla seguras en las tres funciones, dos formateadores tolerantes a vacíos y fragmento seguro del botón Eliminar. Todas las interpolaciones quedan como texto o atributos entre comillas, sin contextos JS/CSS/URL. No se cambian firmas, transporte, condición de Otro ni handlers de escritura. |
| form-doc/footer.php | Sustituye versión ficticia DateTime por filemtime del script. Helper antes de consumidor y formateadores antes de sus invocaciones. Única inclusión de funcAjax.js encontrada por git grep. |
| docs/tasks/TASK-SEGURIDAD-CATALOGOS-COMPARTIDOS-001.md | Autorización, alcance, pruebas, review, riesgos y VF. |
| docs/TASKS.md | Cierre anterior y estado de esta Task. |
| docs/roadmap/ROADMAP.md | Dependencias y avance vigente. |
| docs/tasks/TASK-SEGURIDAD-SALIDA-HTML-CRUD-001.md | Cierre previo en 16e0559 con push confirmado por el usuario. |

### Seguridad y contratos

Autenticación, autorización, ownership y CSRF no se modifican; este incremento protege salida, no cambia quién puede leer/escribir. Los botones mantienen las reglas de visibilidad existentes, sin otorgar capacidad backend. Los valores ya codificados se conservan como entidades completas mediante CrudHtml, sin decodificar y reinyectar markup.

READ conserva las estructuras nominales/indexadas y el parsing previo: ajaxSelect recibe JSON textual o array; ajaxListas/mostrarTabla continúan recibiendo JSON textual. Se preservan url, op, tipo, POST y lectura síncrona del selector. No se amplían contratos silenciosamente.

WRITE/CRUD conserva ids, names y clases de los botones. El consumidor real admin/scripts/listas.js obtiene editar_lista.attr(id/name) y usa .val(nombre), compatible con la nueva salida; las pruebas ejercitan eventos delegados y selección por valor. Alta, actualización y borrado del backend permanecen idénticos y no se reejecutan con datos reales para un cambio de renderizado. mostrarTabla no tiene callers activos encontrados en código versionado; se protege por compartir el patrón heredado, sin añadir nuevas llamadas.

SQL/transacciones/rollback: no se modifican modelos, endpoints, SQL, esquema o transacciones, ni se conecta el fixture a la BD. Si fuera necesaria una reversión, restaurar los dos archivos de aplicación de forma conjunta a 16e0559; el helper anterior permanece disponible. No requiere migración o restauración de datos. No se ejecutó reversión.

Compatibilidad: consumidores de usuario, formularios personales, programa, tesis y postdoctorado comparten el footer. Se revisaron los callers con git grep y el orden de carga. Se conserva Otro para catálogos habilitados y se excluye en países/read_cursos; no se altera el bloqueo institucional de Cursos.

### Evidencia ejecutada

Artefactos privados: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/catalogos-compartidos/.

- python <ruta>/server.py: fixture loopback 8103 fuera del webroot, sin BD ni cuentas reales; scripts copiados byte a byte de las fuentes actuales. Navegador IAB abrió http://127.0.0.1:8103/index.html?catalogos=1 y mostró PASS catálogos, 337/337 y cero fallos. dom-results.json conserva el resultado.
- Las pruebas ejecutan las tres funciones reales con jQuery y DOM reales, interceptando únicamente $.ajax. Cubren etiquetas/atributos hostiles, entidades heredadas de una/dos capas, comillas, ampersands, acentos, vacíos, formatos nominal/indexado y textual/array según contrato; botones, ids/names, delegación de eventos, opción seleccionada y change; regla Otro por URL/operación y listas vacías. No se generaron elementos/atributos ejecutables ni ejecución diferida. tests.js conserva casos y aserciones.
- node --check js/funcAjax.js: aprobado.
- C:/xampp/php/php.exe -l form-doc/footer.php: aprobado.
- GET al Apache original de js/funcAjax.js: bytes coinciden con fuente actual; la aplicación original sigue disponible.
- Comparación de las cuatro dependencias del fixture con fuente, orden de carga y cierre del puerto 8103: conformes.
- git diff --check: conforme. git diff --cached --name-only: vacío. HEAD permanece 16e05596a9a0b869df32456b4b2a13e3cefd2ffe; sin commit/push.

Criterios técnicos aprobados por inspección y automatización; VF en aplicación completa pendiente del usuario. No se atribuye PASS funcional del usuario a las pruebas sintéticas. No se reejecutaron ni se suman suites previas sin relación con el cambio.

Riesgos residuales: CrudHtml sirve para texto y atributos entre comillas, no URLs/JS/CSS. La preservación de entidades mantiene el contrato histórico, sin corregir datos con doble codificación. Otros renderizadores independientes de módulos académicos y las políticas de escritura de catálogos conservan su alcance previo; esta Task no constituye certificación global de seguridad o autorización de producción. Hosting/HTTPS y aviso institucional definitivos siguen pendientes.

**Dictamen: REVIEW TÉCNICO — APROBADO PARA VF.**

## VF propuesta

Recargar con Ctrl+F5. No requiere crear ni eliminar registros.

- VF-CAT-1: como Admin, abrir una ficha completa de Docente y otra de Estudiante → Editar Datos Personales. Comprobar país de nacionalidad y residencia cargados y seleccionados, cambiar una selección y volver a la original sin guardar. Si aparece selector Pueblo Indígena, comprobar sus opciones. Abrir Datos del Programa y comprobar instituciones. Esperado: etiquetas legibles, selecciones y controles normales; País no ofrece Otro e instituciones conserva Otro cuando corresponde. Volver sin guardar.
- VF-CAT-2: como Admin, abrir Actualizar Listas (admin/act.list.php), seleccionar Pueblos Indígenas e Instituciones, pulsar Editar en un elemento y cancelar sin guardar. Esperado: nombres y caracteres legibles; editor precargado con el elemento correcto; botones Editar/Eliminar y comportamiento habitual conservados. No ejecutar eliminación para esta VF.

Informar VF-CAT-1 y VF-CAT-2: PASS / FAIL / BLOQUEADA. Staging/commit y push requieren autorización posterior separada conforme al protocolo.
