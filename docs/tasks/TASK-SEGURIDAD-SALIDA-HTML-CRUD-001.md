# TASK-SEGURIDAD-SALIDA-HTML-CRUD-001

## Cierre vigente

TASK-SEGURIDAD-SALIDA-HTML-CRUD-001 cerrada en commit 16e0559, con Review Técnico, 433 comprobaciones automatizadas y VF-HTML-1/2/3 PASS. El usuario confirmó «push ejecutado siguiente task»; se registra su confirmación sin nueva comprobación remota. Las referencias anteriores a autorizaciones pendientes de esta Task son históricas. No se autoriza otra implementación ni un despliegue público por este cierre.

El usuario autorizó expresamente staging y commit de los trece archivos de TASK-SEGURIDAD-SALIDA-HTML-CRUD-001 tras Review Técnico y VF-HTML-1/2/3 PASS. Se conserva el alcance revisado y se excluyen los tres archivos ajenos no rastreados. Push pendiente de autorización separada; las referencias anteriores a autorización de commit pendiente son históricas.

## Autorización y alcance

Usuario: «Autorizo la Task de salida HTML segura», con prioridad de optimizar tiempo. Base 08073f188c67cc1bee0bafade6aea8c9eae53ecb, rama refactor/fase-0-seguridad. Existen tres cierres documentales modificados (TASKS, ROADMAP y Task de archivos internos) que se conservan. Excluir ADR-003, aviso JSON y PDF no rastreados ajenos.

Proteger la salida de datos en grillas y fichas personales/del programa de Docente y Estudiante, para Admin/Comité y perfil propio. Conservar presentación, identificadores, botones/modales, permisos, filtros, fechas y edición; no modificar datos almacenados, normalización backend, SQL o reglas académicas. Los módulos independientes de antecedentes académicos no se refactorizan ni se certifican globalmente por este incremento.

Implementar un helper de plantillas HTML con escape de todas las interpolaciones de texto/atributos entre comillas. Preservar entidades completas preexistentes para evitar doble escape de datos históricos; no decodificar texto y volver a insertarlo como markup. Los únicos fragmentos HTML anidados permitidos se construirán con una etiqueta de plantilla de la propia aplicación, que también escapa sus datos. No crear una función de «HTML confiable» para aceptar cadenas arbitrarias recibidas por API.

Mantener las plantillas estáticas y eventos existentes; convertir los dos atributos sin comillas de la grilla Estudiante. Los formateadores locales de nombres deben tolerar valores vacíos y conservar la capitalización habitual. Los mensajes dinámicos de error del editor Docente se muestran mediante text(). No sustituir globalmente jQuery ni funciones de módulos ajenos.

Archivos previstos: js/components/crudHtml.js, form-doc/footer.php, admin/scripts/ver.docente.js, admin/scripts/ver.estudiante.js, form-doc/scripts/info.docente.js, form-doc/scripts/info.estudiante.js, form-doc/scripts/ficha.docente.js, form-doc/info.docente.php, form-doc/info.estudiante.php, esta Task, docs/TASKS.md, docs/roadmap/ROADMAP.md y cierre de TASK-SEGURIDAD-ARCHIVOS-INTERNOS-001.md. Trece archivos; versionar scripts de perfil propio y cargar helper antes de consumidores. Las vistas Admin ya versionan sus scripts con filemtime.

## Criterios y estrategia de tiempo

- Valores literales o con entidades se muestran como texto, sin generar elementos, atributos de evento o ejecución; preservar acentos, comillas, ampersands y entidades heredadas de una o dos capas.
- Conservar estructura de grilla, botones/modales, ids y reglas de visibilidad; no aplicar escape de texto a fragmentos de controles ya generados de forma segura.
- Cubrir las cinco variantes reales de renderizado y ambas grillas con respuestas ficticias aisladas; no introducir payloads en datos reales. Verificar DOM real y pruebas de eventos/contratos donde corresponda.
- Reutilizar backend aprobado y ejecutar regresiones solo de consumidores afectados; no repetir migraciones, alta/inactivación o suites sin relación con este cambio de salida.
- Review explícito antes de VF. Sin commit, push, cambios de esquema, .env o permisos autorizados por esta Task.

Artefactos privados: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/salida-html-segura/. Estimación de referencia 4–6 horas; la fecha de producción depende además del hosting/dominio pendiente.

Estado: implementada. REVIEW TÉCNICO — APROBADO PARA VF. VF del usuario pendiente; sin staging, commit o push.

## Review Técnico — 2026-11-15

### Diff y conciliación de archivos

Se inspeccionó el diff real completo contra 08073f1 y los dos archivos nuevos. Trece archivos de alcance: once rastreados modificados y dos nuevos; nueve de aplicación y cuatro documentales. No se mezclan tres archivos no rastreados preexistentes ajenos (ADR-003, aviso JSON, PDF).

| Archivo | Cambio verificado |
|---|---|
| js/components/crudHtml.js | Nuevo helper: escape por interpolación, fragmentos anidados tipados, entidades preexistentes y formateadores tolerantes a vacíos. |
| form-doc/footer.php | Helper compartido versionado por filemtime, después de jQuery y antes de consumidores. |
| admin/scripts/ver.docente.js | Ficha, líneas y grilla seguras; acciones anidadas conservan controles y atributos. |
| admin/scripts/ver.estudiante.js | Ficha, guía, líneas y grilla seguras; atributos ocultos entre comillas, modales conservados, escape histórico idempotente. |
| form-doc/scripts/ficha.docente.js | Renderizador compartido seguro; errores dinámicos mediante text(); formateo local tolerante a vacíos en editor. |
| form-doc/scripts/info.docente.js | Perfil propio seguro; inicialización vacía evita prefijo undefined. |
| form-doc/scripts/info.estudiante.js | Perfil propio, guía y líneas seguros. |
| form-doc/info.docente.php | Versionado de ambos scripts afectados del perfil propio. |
| form-doc/info.estudiante.php | Versionado del renderizador afectado; editor existente permanece igual. |
| docs/tasks/TASK-SEGURIDAD-SALIDA-HTML-CRUD-001.md | Autorización, alcance, evidencia, review y VF. |
| docs/TASKS.md | Cierre previo, propuesta y estado autorizado actual. |
| docs/roadmap/ROADMAP.md | Dependencia de hosting y avance de esta Task. |
| docs/tasks/TASK-SEGURIDAD-ARCHIVOS-INTERNOS-001.md | Cierre anterior con push confirmado por el usuario. |

### Seguridad, contratos y compatibilidad

- Autenticación, capacidades, ownership y CSRF: inspección del diff confirma que no se cambian endpoints, guardas PHP, identificadores enviados, token ni escrituras. La visibilidad conserva la cabecera de permiso Docente y data-elimina de Estudiante; la autorización continúa en backend.
- READ: mismas operaciones, estructuras JSON y transporte; incluidos perfil propio Docente con JSON textual y consultas de guía. Cinco renderizadores reales y dos grillas ejercitados con respuestas ficticias; no se introduce HTML de API como fragmento confiable.
- WRITE/CRUD: handlers, confirmaciones, payloads, success/error y reintentos se conservan; las regresiones verifican edición, estados e inactivación. Los errores dinámicos de Docente se muestran como texto y mantienen los datos para corregirlos. No se repiten altas/bajas de BD ya aprobadas para validar un cambio de salida.
- SQL, transacciones y rollback: sin cambios de modelos, SQL, esquema o transacciones; ninguna prueba de esta Task conecta a la BD. Restauración de código, si se necesitara, debe ser conjunta para helper, footer y consumidores; no requiere migración ni restauración de datos. No se ejecutó reversión.
- Interpolaciones revisadas: solo texto o valores de atributos entre comillas. Sin datos interpolados en manejadores JS, nombres de atributos, CSS o URLs. Plantillas anidadas construidas mediante fragment y datos nuevamente escapados; objetos recibidos por API no pueden suplantar la clase privada. El diálogo confirm usa texto nativo y no necesita escape HTML.
- Consumidores: todos los PHP que incluyen estos cinco scripts usan footer antes del consumidor y filemtime para el código modificado; la carga anterior del helper no invoca los formateadores hasta después de cargar funcForm.js. Las entidades completas se preservan como antes (equivalente al contrato histórico double_encode=false), sin decodificar y reinyectar markup. Se comprobaron también atributos con comillas codificadas.

### Validaciones ejecutadas y evidencia

Artefactos privados y comandos desde la raíz del repositorio; prefijo de ruta de los scripts: C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/salida-html-segura/.

| Comando / prueba | Resultado |
|---|---|
| node test-renderers.cjs | 191/191: callbacks reales, cinco fichas, dos grillas, estados/permisos, ids hostiles y helper. |
| node test-editor-regression.cjs | 8/8: editor Docente, conflictos de correo, errores, corrección/reintento, contraseña y lectura fallida. |
| node test-estudiante-frontend.cjs | 12/12: ficha/edición propia y administrativa. |
| node test-estudiante-crud-frontend.cjs | 14/14: contratos y eventos CRUD. |
| node test-perfiles-inactivos-frontend.cjs | 11/11: estados e inactivación. |
| Navegador IAB en http://127.0.0.1:8103/index.html?revision=2 | 197/197: DOM real de las salidas generadas por los callbacks anteriores, inserción con jQuery real; ausencia de elementos ejecutables, atributos de evento y ejecución; caracteres/entidades, botones, modales, ids, permisos y estados. |
| node --check sobre helper y cinco consumidores | 6/6. |
| C:/xampp/php/php.exe -l sobre footer y dos perfiles PHP | 3/3. |
| GET Apache localhost del helper y comparación de bytes con fuente | Conforme; puerto 80 original disponible. |
| git diff --check; git diff --cached --name-only; git rev-parse HEAD | Sin errores de whitespace, staging vacío, HEAD 08073f1. |

Total: 433 comprobaciones funcionales automatizadas aprobadas, más 9 verificaciones de sintaxis y entrega HTTP del helper. Los adaptadores Node simulan DOM/transporte; la prueba de navegador verifica el DOM real producido por esas salidas, sin autenticar usuarios ni inyectar datos en la BD. No sustituye la VF visual del usuario en la aplicación completa. Se reutilizó la evidencia histórica del backend sin declararla reejecutada.

Incidencia de la prueba: primera ejecución DOM 109/197; las 88 comprobaciones fallidas usaban .verDoc, selector inexistente en el código original y actual. Se corrigió únicamente el fixture a .verFicha. Reejecución observada en navegador: 197/197, cero fallos. No hubo que modificar el producto por esa incidencia. Servidor aislado 8103 detenido al terminar; fixture y respuestas sintéticas quedan fuera del webroot.

Evidencia: renderer-results.json, dom-results.json, frontend-results.json, estudiante-frontend-results.json, estudiante-crud-frontend-results.json, perfiles-inactivos-frontend-results.json y syntax-http-results.json. El manifiesto privado registra hashes de los trece archivos revisados.

### Criterios y riesgos residuales

Criterios técnicos de salida como texto, compatibilidad de entidades heredadas, controles por estado/permisos y ausencia de cambios en datos/contratos: aprobados con evidencia estática y automatizada. Validación visual en la aplicación completa: pendiente del usuario.

El helper sirve para texto y atributos entre comillas; no usar para URLs, CSS, JS o aceptar markup arbitrario. Este incremento cubre grillas y datos personales/del programa; no certifica todos los renderizadores de antecedentes académicos de la aplicación. No es un sanitizador general ni corrige entidades previamente duplicadas: conserva su presentación de una capa conforme al almacenamiento histórico. La publicación sigue dependiendo de hosting/HTTPS y del aviso institucional definitivo pendientes.

Estado Git: trece archivos conciliados, staging vacío, sin commit/push. Tres archivos no rastreados ajenos preservados. Dictamen: **REVIEW TÉCNICO — APROBADO PARA VF**.

## Validación funcional propuesta

Antes de empezar: recargar con Ctrl+F5. Usar fichas completas históricas o TEST completas; los registros parciales EPIC003 ya documentados no constituyen regresión de esta Task.

- VF-HTML-1: entrar como Admin, abrir grillas Docentes y Estudiantes y la Información de una ficha completa de cada perfil. Esperado: nombres, dirección, datos del programa, líneas y guía se muestran legibles; sin etiquetas, entidades nuevas visibles o prefijo undefined. Abrir Editar Datos Personales y volver sin guardar: campos y botones disponibles.
- VF-HTML-2: en un registro TEST de cada perfil, cambiar Dirección a Calle O'Connor & Los Álamos, guardar y reabrir Información. Esperado: se muestran apóstrofo, &, acentos y texto normal, respetando la capitalización habitual; no aparece &amp; ni desaparecen los datos. Restaurar la dirección original. No introducir scripts en datos reales.
- VF-HTML-3: abrir Información personal con una cuenta Docente y una Estudiante. Esperado: sus datos personales y del programa cargan; los botones de edición siguen disponibles conforme al perfil. No es necesario repetir altas, eliminación o cambios de estado ya aprobados.

Informar VF-HTML-1, VF-HTML-2 y VF-HTML-3: PASS / FAIL / BLOQUEADA. Si falla, indicar perfil, pantalla, acción y mensaje. Tras VF aprobada, solicitar autorización separada de staging/commit conforme al protocolo; aún no concedida.

## Confirmación funcional vigente

El usuario confirmó expresamente «VF-HTML-1, VF-HTML-2 y VF-HTML-3: PASS». Validación funcional aprobada. Las referencias anteriores a VF pendiente corresponden a la etapa previa. Pendiente autorización específica de staging y commit de los trece archivos conciliados; push requiere autorización separada. Sin cambios adicionales de aplicación, staging, commit o push al registrar esta confirmación.
