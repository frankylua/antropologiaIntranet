# TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001

## Integración paralela de identidad derivada en login

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **AT fuente:** AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001.
- **Task antecedente:** TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001.
- **Clasificación:** [TEC] [ARQ] [SEC] [GOV] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Integrar `IdentityResolver` en paralelo dentro del inicio de sesión, sin
sustituir permisos, sesiones históricas, capacidades, respuesta, navegación ni
redirección.

### 3. Archivo implementado

```text
ajax/login.php
```

### 4. Punto de integración

La resolución se ejecuta después de autenticar correctamente y antes del
bloque histórico de construcción de sesiones.

El identificador utilizado corresponde a `login.id_login`, obtenido desde el
resultado autenticado y validado como entero positivo. La integración reutiliza
la instancia PDO entregada por `conexion()`.

### 5. Resolución derivada

```text
login
→ usuario
→ estudiante
→ tipo_est
→ profesor
```

`IdentityResolution` permanece local a la solicitud. No se guarda en sesión,
no se devuelve al frontend y no participa en autorización.

### 6. Comparación histórica

Se comparan únicamente:

```text
estudiante derivado ↔ permiso 5
profesor derivado ↔ permiso 4
```

Las discrepancias se observan, pero no se corrigen. No se agregan, eliminan ni
reconstruyen permisos. Las discrepancias históricas sin relación exigen que la
cardinalidad de usuario sea `SINGLE`.

### 7. Observabilidad

Se utiliza `error_log()` con códigos técnicos sin datos personales:

```text
IDENTITY_LOGIN_NO_ENCONTRADO
IDENTITY_LOGIN_MULTIPLE_USERS
IDENTITY_USER_MULTIPLE_STUDENTS
IDENTITY_USER_MULTIPLE_PROFESSORS
IDENTITY_INVALID_STUDENT_STATUS
IDENTITY_STUDENT_WITHOUT_LEGACY_ROLE
IDENTITY_LEGACY_STUDENT_WITHOUT_RELATION
IDENTITY_PROFESSOR_WITHOUT_LEGACY_ROLE
IDENTITY_LEGACY_PROFESSOR_WITHOUT_RELATION
IDENTITY_RESOLUTION_FAILED
```

### 8. Manejo de excepciones

Se capturan únicamente:

```php
\PDOException
\InvalidArgumentException
```

No se capturan de forma general `Throwable`, `Error`, `TypeError` ni
`Exception`. Las fallas operativas registran exclusivamente
`IDENTITY_RESOLUTION_FAILED` y permiten continuar el flujo histórico.

### 9. Sesiones preservadas

No se modificaron:

```text
login
admin
comite
aceptado
docente
estudiante
capacidades
id_usuario
```

### 10. Permisos preservados

```text
permiso 3:
sin cambios

permiso 4:
sin cambios; no se sincroniza

permiso 5:
sin cambios; compatibilidad transitoria

permiso_login:
sin escrituras ni reconstrucción
```

No existe sincronización de permisos durante el login.

### 11. Capacidades y respuesta

No se modificaron:

```text
- capacidades efectivas;
- respuesta JSON;
- estado HTTP;
- navegación;
- redirección;
- Cursos;
- Calendario;
- Reglamento.
```

### 12. Addendum correctivo

La revisión técnica inicial detectó:

```text
- captura general de Throwable;
- imprecisión en discrepancias WITHOUT_RELATION.
```

Se corrigió:

```text
- sustitución por PDOException e InvalidArgumentException;
- discrepancias WITHOUT_RELATION solo cuando usuario es SINGLE.
```

### 13. Validación técnica

```text
php -l:
Correcto.

git diff --check:
Correcto.

Archivo incluido:
ajax/login.php.

Revisión técnica:
Aprobada después del addendum correctivo.
```

### 14. Validación funcional

Responsable:

```text
Usuario.
```

Estado:

```text
Aprobada.
```

Se confirmó:

```text
- Administrador y Comité sin usuario continúan funcionando;
- estudiante mantiene comportamiento;
- estudiante Eliminado no obtiene acceso nuevo;
- profesor con permiso 4 no cambia;
- profesor sin permiso 4 no es corregido automáticamente;
- credenciales inválidas mantienen respuesta histórica;
- navegación y redirección no cambian;
- Cursos, Calendario y Reglamento no cambian;
- códigos técnicos no se exponen en la interfaz.
```

Codex no realizó la validación funcional.

### 15. Commit

```text
2c94d414c72cf3196d46ed766576881742c66e7c
feat(identity): integrate derived identity into login
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 16. Pendientes

* utilizar identidad derivada como fuente funcional en incrementos posteriores;
* implementar `perfil.ver`;
* migrar perfil y navegación;
* resolver redirección por roles y capacidades;
* validar una cuenta nueva sin permiso 5;
* congelar posteriormente la asignación del permiso 5;
* retirar sesiones históricas cuando no queden consumidores;
* mantener prohibida la sincronización automática durante login.

### 17. Estado final

```text
Task:
Cerrada

Implementación:
Completada

Revisión técnica:
Aprobada después del addendum correctivo

Validación funcional:
Aprobada por el usuario

Commit:
2c94d414c72cf3196d46ed766576881742c66e7c

Push:
Completado
```
