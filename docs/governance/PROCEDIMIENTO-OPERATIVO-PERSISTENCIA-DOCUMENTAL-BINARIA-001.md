# PROCEDIMIENTO OPERATIVO — PERSISTENCIA DOCUMENTAL BINARIA

Este procedimiento gobierna la preparación, migración, cutover y reversión de
Documento. Git/deploy por sí solo no prepara ningún ambiente. Sustituir todos
los placeholders localmente; nunca registrar credenciales, dumps ni secretos.

La estrategia obligatoria es un **cutover schema-first bajo ventana de
mantenimiento**. En Desarrollo, Staging y Producción, cuando exista despliegue
con tráfico posible, debe seguirse la secuencia completa de la sección 3.2 con
los valores y mecanismos efectivos del ambiente. El código nuevo de Curso y
Documento no es compatible con el schema antiguo. **EL CÓDIGO NUEVO NO PUEDE
RECIBIR TRÁFICO ANTES DE EJECUTAR Y APROBAR MIG-1.**

## 1. Desarrollo

1. Confirmar rama, HEAD, staging vacío y artefactos esperados.
2. Confirmar que ningún dump esté dentro del repositorio o de un
   `DocumentRoot` y que la URL histórica responda `403` o `404`.
3. Verificar espacio, permisos y protección HTTP efectiva de
   `files/prog_curso`; no asumir Apache ni XAMPP.
4. Consultar después del restart y exigir:

   ~~~text
   max_allowed_packet = 16M
   innodb_log_file_size = 64M
   innodb_log_buffer_size = 16M
   innodb_file_per_table = 1
   check_constraint_checks = 1
   ~~~

5. Verificar PHP efectivo: `upload_max_filesize >= 6M`,
   `post_max_size >= 8M` y `memory_limit >= 128M`. El backend mantiene el
   máximo exacto de 5 MiB (`5242880` bytes).
6. Crear un backup conforme a la sección Producción, ensayar restore aislado y
   registrar responsable, fecha y resultado sin contenido sensible.
7. Revisar y ejecutar MIG-1 paso a paso sólo con todos los gates aprobados.
8. Ejecutar primero `php migrations/TASK-DB-MIGRATION-DOCUMENTO-DATOS-001.php --dry-run`.
9. Revisar el inventario real; luego ejecutar el mismo script con `--execute`.
10. Completar postflight técnico, smoke test y VF antes del cutover.

## 2. Preproducción/Staging

Si el ambiente existe, repetir íntegramente el preflight, backup, restore,
MIG-1, MIG-2 y postflight con su propia configuración efectiva. No reutilizar
evidencia de Desarrollo ni asumir conteos históricos. MIG-2 debe descubrir la
realidad del ambiente y mantener intactos referencias faltantes y huérfanos.

Ensayar en este ambiente:

- CREATE/UPDATE/DELETE transaccionales de Curso y Documento;
- descarga autorizada por `id_curso` y fallback sólo con FK nula;
- límites, MIME falso, ownership, CSRF y actores READ;
- rollback Documento→filesystem en `--dry-run` y en una ventana controlada;
- restore completo en una base aislada.

No activar producción mientras alguna evidencia esté pendiente.

## 3. Producción

### 3.1. Gates y backup

Verificar directamente en producción, mediante el estado efectivo del ambiente
después de cualquier restart, estos valores obligatorios:

~~~text
max_allowed_packet      = 16777216
innodb_log_file_size    = 67108864
innodb_log_buffer_size  = 16777216
innodb_file_per_table   = 1
check_constraint_checks = 1
~~~

Verificar también los mínimos PHP efectivos:

~~~text
upload_max_filesize >= 6M
post_max_size >= 8M
memory_limit >= 128M
~~~

El límite exacto del backend permanece en `5242880` bytes. `my.ini`, `php.ini`
y la configuración del servicio MariaDB no necesariamente forman parte del
deploy Git: aplicarlos mediante el procedimiento propio del servidor de destino.
No basta revisar archivos de configuración, documentación o valores esperados;
Producción debe ejecutar y aprobar su propio preflight después de un restart
limpio.

Crear antes de MIG-1 un backup lógico completo fuera del repositorio y de todo
`DocumentRoot`, con acceso restringido. Comando conceptual, sin contraseña
embebida:

~~~text
mysqldump --single-transaction --quick --hex-blob --max-allowed-packet=16M \
  --host=<DB_HOST> --user=<DB_USER> <DB_NAME> > <BACKUP_DIR_PRIVADO>/<BACKUP>.sql
~~~

Backup creado no equivale a restore validado. Antes del cutover debe existir
evidencia de un procedimiento de restore utilizable. Confirmar además que el
respaldo está protegido y nunca versionado.

### 3.2. Checklist obligatorio

- [ ] 1. Abrir ventana de mantenimiento/despliegue.
- [ ] 2. Bloquear todas las operaciones de usuarios sobre la aplicación.
- [ ] 3. Verificar que no existan escrituras de Cursos activas.
- [ ] 4. Crear backup completo previo fuera de webroot, repositorio y Git.
- [ ] 5. Confirmar capacidad de restore y evidencia del procedimiento utilizable.
- [ ] 6. Consultar y aprobar los gates MariaDB/PHP efectivos del ambiente.
- [ ] 7. Colocar los artefactos del release sin permitir tráfico de usuarios.
- [ ] 8. Ejecutar y aprobar el preflight de MIG-1.
- [ ] 9. Ejecutar MIG-1 por etapas, sin asumir rollback transaccional.
- [ ] 10. Ejecutar y aprobar el postflight de MIG-1.
- [ ] 11. Ejecutar MIG-2 con `--dry-run`.
- [ ] 12. Revisar y aprobar el inventario real de MIG-2.
- [ ] 13. Ejecutar MIG-2 con `--execute` desde CLI.
- [ ] 14. Ejecutar postflight de MIG-2: FK, bytes, checksum y huérfanos.
- [ ] 15. Activar/publicar el código nuevo de Documento; no habilitar dual-write.
- [ ] 16. Ejecutar smoke test de CRUD y descarga.
- [ ] 17. Ejecutar la VF mínima crítica antes de reabrir tráfico.
- [ ] 18. Reabrir el tráfico de usuarios.
- [ ] 19. Ejecutar la VF funcional completa aprobada por actor.
- [ ] 20. Monitorear errores, memoria, conexiones y logs sin BLOB ni secretos.
- [ ] 21. Cerrar la ventana sólo con todas las validaciones aprobadas.

El responsable del despliegue debe emplear y verificar antes de MIG-1 el
mecanismo real disponible para impedir tráfico y escrituras: maintenance mode
del hosting, deshabilitación temporal del sitio, regla del servidor web o una
restricción equivalente. No se presupone Apache, XAMPP ni una infraestructura
concreta. Si no existe una forma verificable de impedir que usuarios ejecuten
el código nuevo contra el schema antiguo, abortar el despliegue, no ejecutar
MIG-1 y escalar el bloqueo operativo del ambiente.

Antes de habilitar el código nuevo, cualquier fallo de MIG-1 o MIG-2 obliga a
mantener la aplicación en mantenimiento y recuperar según el estado efectivo.
No reabrir tráfico hasta restaurar una combinación compatible de código y
schema. La combinación código nuevo + schema antiguo nunca es admisible.

Documento BD es autoridad para nuevas escrituras desde el cutover.
`arch_prog`, `files/prog_curso` y el fallback permanecen exclusivamente para
compatibilidad histórica y rollback. Mantener denegación HTTP equivalente a
`Require all denied` sobre el directorio legacy.

### 3.3. Restore aislado

Nunca restaurar una prueba sobre la BD activa. Crear una BD aislada y ejecutar
conceptualmente:

~~~text
mysql --host=<DB_HOST_AISLADO> --user=<DB_USER_AISLADO> \
  <DB_RESTORE_AISLADA> < <BACKUP_DIR_PRIVADO>/<BACKUP>.sql
~~~

Validar schema, constraints, UNIQUE, FK, conteos de Curso/Documento,
`OCTET_LENGTH(archivo)=tamanio`, SHA-256, asociación Curso→Documento y descarga
autenticada. Confirmar que faltantes y huérfanos no fueron fabricados,
asociados ni eliminados.

## 4. Rollback

1. Detener nuevas escrituras y crear/verificar un backup.
2. Determinar si existen CREATE/UPDATE sólo-BD.
3. Si no existen, revertir aplicación y después aplicar el rollback manual de
   MIG-1 conforme al estado efectivo.
4. Si existen, ejecutar primero:

   ~~~text
   php migrations/TASK-DB-MIGRATION-DOCUMENTO-ROLLBACK-FILESYSTEM-001.php --dry-run
   ~~~

5. Revisar inventario, espacio, permisos, colisiones y cobertura. Con aprobación
   operativa ejecutar el mismo script con `--execute`.
6. Verificar por Curso: Documento → archivo confinado → tamaño/MIME/SHA-256
   equivalentes a la metadata vigente → `arch_prog`; confirmar descarga legacy
   y ausencia de FK sólo-BD sin fallback.
7. Revertir la aplicación únicamente después de aprobar la cobertura.
8. Revertir schema al final, nunca primero, verificando cada estado parcial del
   DDL con commits implícitos.

La exportación de rollback no elimina Documento, no modifica
`id_documento_programa` y no constituye dual-write permanente.
