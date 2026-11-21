# Guía de entrega final — revisión local 2026-11-21

El usuario confirmó el 21/11/2026 «VF-CIERRE-1 y VF-CIERRE-2: PASS». Review Técnico y validación funcional del cierre interno aprobados. Pendiente autorización de staging/commit de los seis documentos y push separado. Las referencias anteriores a VF pendiente son históricas. Esta aceptación no autoriza publicación en Internet ni cierra pendientes de hosting/HTTPS, aviso institucional o decisiones académicas.

## Estado y revisión seleccionada

Destino autorizado: localhost en este equipo. Código: **4c16732 — Seguridad catalogos grilla**, rama refactor/fase-0-seguridad. Preparación técnica realizada el 21/11/2026, America/Santiago. Review Técnico APROBADO PARA VF operativa pendiente. La preparación no acredita que la presentación prevista para el 20/11 haya ocurrido; esa fecha ya pasó y no se inventa una nueva fecha.

Esta guía identifica el paquete actualizado con los incrementos de login, protección HTTP, salida HTML y catálogos que siguieron al paquete 989ac6e. Los respaldos anteriores quedan conservados como históricos. La aplicación ejecutada en el localhost original permanece en su ubicación y no necesita importar ni extraer nada para usarla.

## Ubicación del paquete privado vigente

Carpeta:

`C:/Users/volca/.codex/visualizations/2026/11/13/01a1d73e-4d1d-7201-8602-25624da60c81/cierre-final-20261121/`

| Archivo | Uso |
|---|---|
| respaldo-privado-cierre-4c16732-20261121.zip | Respaldo completo privado: app, vendor, configuración, documentos y SQL de 39 tablas. |
| codigo-cierre-4c16732.zip | Snapshot Git de la revisión exacta; no contiene .env ni vendor y no constituye instalación autónoma. |
| delivery-manifest.json | Commit, hashes SHA-256 y correspondencia de código/runtime/SQL. |
| PAQUETE-VIGENTE.json | Selección de archivos y estado del Review/VF. |
| documentacion-cierre-20261121-vf-pass.zip | Los seis documentos de este cierre, posteriores al commit base. |
| GUIA-ENTREGA-FINAL-2026-11-21.md | Copia de esta guía junto al respaldo. |

El dump verificado es el indicado por delivery-manifest.json y database-backup-report.json. No seleccionar por nombre un intento incompleto: el primer intento de respaldo falló mientras MySQL estaba apagado, no se declaró válido ni se incluyó en el paquete.

El ZIP completo contiene datos y configuración privados. Conservarlo fuera de htdocs y Git, con acceso controlado; no enviarlo como código público. El código y los docs dentro de app reflejan 4c16732. La guía vigente y el ZIP documental adjunto aclaran el estado posterior de entrega, sin alterar el snapshot del commit.

## Arranque normal en este equipo

1. En XAMPP, iniciar Apache y MySQL y esperar que ambos queden activos.
2. Abrir http://localhost/antropologiaIntranet/form-doc/login.php.
3. Ingresar con una cuenta autorizada habitual. No usar credenciales de prueba aislada en la base principal ni compartir contraseñas.
4. Como Admin, abrir Docentes y Estudiantes. Para cargar Información, usar registros históricos completos o TEST completos; los seis registros parciales EPIC003 ya documentados no deben utilizarse para exigir una ficha completa.
5. Recargar con Ctrl+F5 si el navegador conserva scripts anteriores. Las ayudas de recuperación siguen cerradas por defecto; la obligatoriedad de acuerdos en autoalta conserva el texto de muestra aprobado.

No importar SQL, ejecutar migraciones o reemplazar .env para este recorrido. Si el login indica espera temporal por intentos, respetar el contador y reintentar al finalizar; no cambiar estados académicos para eludirlo.

## Restauración y reversión

No restaurar sobre la base principal como parte de la muestra. El SQL contiene CREATE DATABASE/USE con el nombre de origen: una eventual recuperación requiere destino aislado, revisión del nombre y autorización específica antes de importar. En este cierre no se creó ni se importó una base: el arranque de la copia extraída usó la base de recuperación ya existente y una cuenta sintética, con sesiones/limitador separados.

No revertir a un verificador de contraseñas que solo soporte texto plano: hay hashes convertidos que deben seguir siendo verificables. Los scripts/dumps de recuperación anteriores mantienen su propia evidencia de ensayo; este dump nuevo se verificó por integridad y completitud, no mediante una nueva importación.

## Archivos incorporados manualmente

| Archivo | Tratamiento en entrega |
|---|---|
| ADR-003 | Incluido como documento de propuesta; su estado sigue PROPUESTO y sin implementación asociada. Su inclusión en Git no aprueba una nueva arquitectura o permisos. |
| AVISO-DATOS-AUTOALTA.json | Incluido como borrador de preparación, habilitado=false, campos institucionales pendientes. No está integrado al popup ni sustituye el texto de muestra. |
| curso_45afc744db0fb82f1db6f16d31ded67a.pdf | Conservado por integridad del runtime. PDF de una página con DOCUMENTO GENERICO, sin cifrado, acciones de apertura, JavaScript de catálogo ni adjuntos detectados en la inspección básica. No representa un programa académico aprobado. Su URL directa continúa denegada; no se habilita Cursos. |

No se modificaron estos tres archivos. El examen del aviso evalúa su estado e integración técnica; no constituye validación jurídica o institucional de su contenido. El examen básico del PDF no es un análisis antimalware.

## Lista única de pendientes

| Pendiente | Efecto / siguiente acción |
|---|---|
| VF operativa de este cierre | Confirmar ubicación de respaldo y apertura/login/grillas del localhost; no repetir CRUD aprobado. |
| Versionado documental del cierre | Tras VF, autorización de staging/commit de seis documentos; push separado. Código ejecutable ya identificado en 4c16732. |
| Hosting, dominio, TLS/proxy y configuración real | Necesarios para configurar y verificar HTTPS/cookie_secure, protección HTTP, extensiones PHP, rutas y permisos del limitador operativo. No inferirlos del localhost. |
| Aviso institucional y decisiones de tratamiento | Cliente debe completar responsables, unidad, domicilio, contacto, finalidades, datos obligatorios/opcionales, destinatarios/hosting, conservación y aprobación; luego autorizar integración y registro de evidencia si corresponde. El texto actual sigue siendo de muestra. |
| Cursos y otras decisiones académicas pendientes | Mantener las restricciones documentadas; no habilitarlos por este cierre. |
| ADR-003 y recursos compartidos | Requieren aprobación arquitectónica e inspección/Task por objeto; diferidos del CRUD priorizado de muestra. |
| Datos parciales TEST EPIC003 | Condición conocida, no corregir guías/roles ni fabricar datos para ocultarla. |

El cierre acredita preparación y recuperación del alcance interno priorizado, no terminación de todas las épicas ni autorización para publicar en Internet. No se asigna porcentaje global sin un inventario aprobado del alcance restante.

## Evidencia resumida y VF

Paquete: 363 entradas extraídas verificadas, 226 archivos de código comparados, 19 archivos de runtime y SQL completo de 39 tablas. Copia aislada: 30/30 comprobaciones HTTP/MariaDB. Apache original: 16/16 comprobaciones, incluidas denegaciones de rutas internas y scripts idénticos. Base principal, esquema y .env contrastados antes/después sin cambios. Servidor temporal detenido. Se reutilizan las VF históricas de CRUD/seguridad; no se declaran reejecutadas.

**VF-CIERRE-1:** abrir la carpeta indicada y confirmar que existen el respaldo completo, delivery-manifest.json, PAQUETE-VIGENTE.json y esta guía. No extraer ni importar nada.

**VF-CIERRE-2:** con Apache/MySQL activos, abrir la URL de login, ingresar como Admin y abrir ambas grillas. Esperado: acceso normal y carga de Docentes/Estudiantes. No editar ni eliminar registros para esta comprobación.

Responder VF-CIERRE-1 y VF-CIERRE-2: PASS / FAIL / BLOQUEADA. Las autorizaciones de commit/push se solicitan después de la VF.
