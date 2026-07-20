# TASK-EPIC003-CAPACIDAD-PERFIL-VER-001

## Introducción transitoria de la capacidad `perfil.ver`

### 1. Identificación

- **EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
- **ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
- **AT fuente:** AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001.
- **Task antecedente:** TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001.
- **Clasificación:** [ARQ] [TEC] [SEC] [GOV] [MET].
- **Estado:** Cerrada.

### 2. Objetivo

Introducir `perfil.ver` como capacidad explícita de compatibilidad, producida
desde el permiso histórico 5, y utilizarla para proteger la lectura del perfil
estudiantil propio.

### 3. Archivos implementados

```text
ajax/login.php
ajax/estudiante.php
```

### 4. Productor transitorio

Regla:

```text
id_permiso == 5
→ perfil.ver
```

La capacidad se agrega a:

```php
$_SESSION['capacidades']
```

con deduplicación estricta. Su inicialización durante el login se mantiene y la
regla previa de `reglamento.ver` no cambia.

No se deriva desde:

```text
tipo_est
IdentityResolution
permiso 3
permiso 4
```

No se consulta nuevamente la base, no se modifica `permiso_login` y no se
reemplaza `$_SESSION['estudiante']`.

### 5. Consumidor piloto

Operación:

```text
ajax/estudiante.php
→ read_est_perfil
```

Regla de acceso:

```text
perfil.ver
OR admin
OR comite
```

La autorización ocurre antes de consultar el modelo y antes de
`mostrarEstId()`.

### 6. Componente de autorización

Se utiliza:

```php
Authorization::hasCapability('perfil.ver')
```

Para compatibilidad histórica:

```php
Authorization::hasAny(['admin', 'comite'])
```

`src/Security/Authorization.php` no fue modificado.

### 7. Titularidad

El perfil propio continúa utilizando:

```php
$_SESSION['id_usuario']
```

No acepta identificadores de titular desde el cliente. `read_est_id` y el
perfil ajeno permanecen fuera de alcance. Un administrador o integrante de
Comité autorizado sin `id_usuario` recibe `[]` sin consulta con identificador
`0`.

### 8. Denegación

Una solicitud no autorizada recibe:

```text
HTTP 403
```

```json
{"error":"FORBIDDEN"}
```

La denegación ocurre antes de la consulta: no expone datos personales ni
detalles técnicos, no imprime HTML y no redirige desde AJAX. El callback
exitoso del frontend no interpreta esta respuesta como un arreglo.

### 9. Contrato exitoso

Se conserva:

```php
$resp = $est->mostrarEstId($id_est);
echo json_encode($resp, JSON_UNESCAPED_UNICODE);
```

No se modificó la estructura exitosa del endpoint.

### 10. Sesiones y permisos preservados

No se modificaron:

```text
login
admin
comite
aceptado
docente
estudiante
id_usuario
```

No se modificaron:

```text
permiso_login
permisos 1, 2, 3, 4 o 5
base de datos
modelos
```

La única capacidad nueva es:

```text
perfil.ver
```

### 11. Estado académico

`tipo_est` no produce todavía `perfil.ver`.

Por compatibilidad:

```text
estado 6 + permiso 5
→ perfil.ver
```

Esta regla es transitoria, preserva el comportamiento histórico y no reemplaza
la matriz institucional ni constituye una aprobación institucional definitiva.
No se afirma que el estado 6 haya sido excluido.

### 12. Identidad derivada

`IdentityResolution` no concede esta capacidad. Una identidad estudiante sin
permiso 5 no recibe acceso nuevo.

La separación vigente es:

```text
identidad: login → usuario → especializaciones
autorización transitoria: permiso 5 → perfil.ver
```

Identidad y autorización todavía no han sido unificadas.

### 13. Alcance excluido

No se modificaron:

```text
perfil docente
perfil administrativo
perfil ajeno
edición personal
edición del programa
eliminación
ficha académica
grados
publicaciones
congresos
proyectos
pasantías
becas
tesis
navegación
redirección
```

### 14. Revisión técnica

Estado:

```text
Aprobada con observaciones no bloqueantes.
```

Observaciones:

```text
- una forma corrupta de $_SESSION['id_usuario'] puede producir warnings preexistentes antes del switch;
- el frontend no maneja explícitamente HTTP 403;
- el frontend espera un arreglo y no maneja [] antes de usar usu[0];
- perfil.ver continúa dependiendo transitoriamente del permiso 5;
- el estado 6 conserva acceso mientras tenga permiso 5.
```

No se identificaron hallazgos bloqueantes ni mayores atribuibles a la
implementación.

### 15. Validación funcional

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
- estudiante con permiso 5 mantiene acceso a su perfil;
- estudiante eliminado con permiso 5 continúa visualizando su perfil;
- profesor con permisos 4 y 3 conserva acceso a su perfil docente;
- profesor sin permiso 5 no obtiene perfil.ver;
- el perfil docente no fue afectado;
- el comportamiento del estado 6 es transitorio y esperado;
- no se alteraron navegación ni redirección;
- no se modificó el contrato exitoso del perfil estudiantil;
- no se modificaron permisos ni datos.
```

El acceso del profesor a su perfil docente no constituye acceso al perfil
estudiantil ni una falla de `perfil.ver`.

Codex no realizó la validación funcional.

### 16. Commit

```text
58d7745684aa1156e1feec0e1c80900cb1dce998
feat(auth): introduce perfil.ver capability
```

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

### 17. Pendientes

* migrar el productor desde permiso 5 hacia una regla institucional aprobada;
* resolver el acceso del estado 6;
* proteger otros consumidores de perfil;
* separar capacidades de lectura y escritura;
* definir perfil ajeno y administración;
* mejorar el manejo frontend de 403;
* robustecer la ausencia o corrupción de `id_usuario`;
* retirar posteriormente dependencias de sesión histórica;
* inspeccionar el siguiente consumidor de perfil o navegación para una
  migración incremental, sin asignar una Task hasta que exista una secuencia
  aprobada.

### 18. Estado final

```text
Task:
Cerrada

Implementación:
Completada

Revisión técnica:
Aprobada con observaciones no bloqueantes

Validación funcional:
Aprobada por el usuario

Commit:
58d7745684aa1156e1feec0e1c80900cb1dce998

Push:
Completado
```
