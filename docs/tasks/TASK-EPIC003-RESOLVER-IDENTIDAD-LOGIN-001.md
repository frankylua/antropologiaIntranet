# TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001

## Resolver de identidad de solo lectura

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **AT fuente:** AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001.
- **Clasificación:** [TEC] [ARQ] [GOV] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Introducir un componente independiente capaz de resolver, desde
`login.id_login`, la identidad personal y las especializaciones de
estudiante y profesor sin utilizar permisos heredados como fuente de
verdad.

### 3. Archivos implementados

```text
src/Identity/Cardinality.php
src/Identity/IdentityResolution.php
src/Identity/IdentityResolver.php
```

### 4. Contrato

```php
resolveByLoginId(mixed $loginId): IdentityResolution
```

La entrada válida es un entero PHP positivo.

Los identificadores inválidos se rechazan antes de ejecutar consultas.

### 5. Cardinalidades

El componente distingue:

```text
NONE
SINGLE
MULTIPLE
```

No selecciona arbitrariamente una fila cuando existe multiplicidad.

### 6. Resolución de identidad

```text
login
→ usuario
→ estudiante
→ profesor
```

Reglas:

* login inexistente se distingue de login sin usuario;
* un login sin usuario es un resultado válido;
* estudiante se deriva desde `estudiante.usuario`;
* profesor se deriva desde `profesor.usuario`;
* `tipo_est` se obtiene exclusivamente desde `estudiante.tipo_est`;
* estudiante y profesor pueden coexistir;
* no se consultan permisos para determinar especializaciones.

### 7. Inconsistencias

El resultado puede informar:

```text
LOGIN_NO_ENCONTRADO
LOGIN_CON_MULTIPLES_USUARIOS
USUARIO_CON_MULTIPLES_ESTUDIANTES
USUARIO_CON_MULTIPLES_PROFESORES
TIPO_EST_INVALIDO
```

Las inconsistencias no son corregidas automáticamente.

### 8. Restricciones aplicadas

El resolver:

* utiliza únicamente consultas `SELECT`;
* utiliza consultas parametrizadas;
* no concatena identificadores;
* no consulta `permiso_login`;
* no consulta el catálogo `permiso`;
* no modifica datos;
* no inicia, lee ni escribe sesión;
* no calcula capacidades;
* no decide autorización;
* no decide redirección;
* no modifica navegación;
* no autentica credenciales.

### 9. Casos aprobados

```text
[✓] Login inexistente.
[✓] Login administrativo sin usuario.
[✓] Usuario sin especializaciones.
[✓] Estudiante válido.
[✓] Profesor sin permiso 4.
[✓] Estudiante y profesor simultáneos.
[✓] Múltiples usuarios.
[✓] Múltiples estudiantes.
[✓] Múltiples profesores.
[✓] tipo_est inválido.
```

### 10. Validación técnica

```text
php -l:
Correcto en los tres archivos.

Autoload PSR-4:
Correcto.

PHP activo:
8.2.12.

git diff --check:
Correcto.

Efectos colaterales:
No detectados.
```

Dictamen:

```text
Aprobada con observaciones no bloqueantes.
```

### 11. Observaciones no bloqueantes

#### IdentityResolution

El constructor público permite teóricamente crear combinaciones
semánticamente contradictorias. `IdentityResolver` no produce esos
estados, pero futuros consumidores deberán respetar el contrato.

#### Versión de PHP

El código utiliza `readonly`, por lo que requiere PHP 8.1 o superior.
El entorno activo es compatible, pero `composer.json` no declara una
versión mínima.

Estas observaciones quedan fuera del alcance de esta Task.

### 12. Comportamiento observable

```text
No modificado.
```

El resolver aún no tiene consumidor productivo y no está integrado en:

```text
ajax/login.php
```

### 13. Validación

```text
Revisión técnica:
Aprobada.

Aprobación del usuario:
Confirmada.

Validación funcional productiva:
No aplica todavía.
```

### 14. Commit

```text
814ecb2d703d8df1638a53e660a068c5f29e3b06
feat(identity): add read-only login identity resolver
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 15. Pendientes

* integrar el resolver en paralelo dentro del login;
* mantener temporalmente permisos y sesiones históricas;
* incorporar observabilidad de discrepancias;
* no sincronizar permisos durante login;
* definir el tratamiento del DTO antes de ampliar consumidores;
* evaluar una declaración explícita de versión mínima de PHP;
* implementar `perfil.ver`;
* migrar redirección y navegación en Tasks posteriores.

### 16. Estado final

```text
Task:
Cerrada.

Implementación:
Completada.

Revisión técnica:
Aprobada con observaciones no bloqueantes.

Aprobación del usuario:
Confirmada.

Validación funcional productiva:
No aplica.

Commit:
814ecb2d703d8df1638a53e660a068c5f29e3b06

Push:
Completado.
```
