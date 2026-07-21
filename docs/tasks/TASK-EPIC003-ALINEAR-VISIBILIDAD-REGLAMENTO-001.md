# TASK-EPIC003-ALINEAR-VISIBILIDAD-REGLAMENTO-001

## Alineación de la visibilidad frontend de Reglamento

### 1. Identificación

- **EPIC:** EPIC-003.
- **ADR:** ADR-002.
- **Estado:** Cerrada.
- **Clasificación:** [TEC] [SEC] [MET].

### 2. Objetivo

Alinear la visibilidad frontend del enlace Reglamento con la guardia backend
vigente.

### 3. Archivo implementado

```text
form-doc/header.php
```

### 4. Fuente de autorización

```text
form-doc/reglamento.php
```

La guardia backend permanece como autoridad efectiva.

### 5. Estado histórico

El enlace Reglamento era incondicional dentro del wrapper Programa.

### 6. Condición implementada

```php
Authorization::hasCapability('reglamento.ver')
|| isset($_SESSION['admin'])
|| isset($_SESSION['comite'])
|| isset($_SESSION['aceptado'])
```

### 7. Alcance

La Task modifica únicamente la visibilidad del enlace. La condición frontend no
sustituye la autorización backend, no concede permisos y no revoca accesos por
URL directa.

No modifica:

- guardia backend;
- permisos efectivos;
- acceso por URL directa;
- wrapper Programa;
- `$miperfil`;
- Cursos;
- Calendario;
- login;
- sesiones;
- estados;
- capacidades.

Tampoco modifica la URL, el texto, la clase ni la posición del enlace.

### 8. Wrapper Programa preservado

```text
admin
OR comite
OR aceptado
OR perfil.ver
OR docente
```

`reglamento.ver` no fue añadido al wrapper.

### 9. Compatibilidad histórica

```text
permiso 3
→ aceptado
→ Reglamento visible y backend autorizado
```

Este fallback fue preservado sin reinterpretación institucional. La clave
`aceptado` no se documenta como equivalencia inequívoca de un estado académico
institucional.

### 10. Productor de `reglamento.ver`

```text
Estados productores actuales:
1, 2, 3, 4, 5 y 7.

Estado 6:
no produce la capacidad.
```

La Task no creó `reglamento.ver` ni modificó su producción.

### 11. Casos corregidos

```text
perfil.ver sin reglamento.ver
→ enlace oculto

docente sin reglamento.ver
→ enlace oculto

estado 6 + permiso 5
→ enlace oculto
```

La guardia backend ya restringía esos accesos.

### 12. Casos preservados

| Caso | Enlace Reglamento | Acceso backend |
|---|---:|---:|
| Admin | Visible | Permitido |
| Comité | Visible | Permitido |
| Aceptado / permiso 3 | Visible | Permitido |
| `reglamento.ver` con wrapper visible | Visible | Permitido |
| `perfil.ver` sin `reglamento.ver` | Oculto | Restringido |
| Docente sin `reglamento.ver` | Oculto | Restringido |
| Estado 6 + permiso 5 | Oculto | Restringido |
| Estado 6 + permiso 3 | Visible | Permitido por `aceptado` |
| Ninguna señal | Programa oculto | Restringido |

### 13. Limitación consciente

```text
reglamento.ver aislada
→ backend autorizado
→ Programa oculto
→ enlace no visible
```

La Task no modifica el wrapper, no resuelve esta asimetría y no declara paridad
bidireccional completa entre frontend y backend.

### 14. Commit funcional

```text
3d551a0e9aba88f3fb50644f612edd62c88a80cd
refactor(auth): align regulation menu visibility
```

El commit fue publicado en `origin/refactor/fase-0-seguridad`.

### 15. Revisión técnica

```text
Aprobada con observaciones no bloqueantes y aislable.
```

### 16. Validación funcional

```text
Responsable: Usuario
Estado: Aprobada
```

Codex no ejecutó ni declaró aprobada la validación funcional.

### 17. Riesgos y pendientes

- Regla duplicada temporalmente entre frontend y backend.
- Programa continúa siendo un contenedor heterogéneo.
- Permiso 3 conserva compatibilidad histórica.
- Limpieza general de sesiones pendiente.
- Caso `reglamento.ver` aislada pendiente.
- Cambios locales de Cursos pendientes.
- Capacidad de Calendario pendiente.

El próximo incremento recomendado es resolver el trabajo local pendiente de
Cursos antes de intervenir nuevamente su navegación o sus contratos de
autorización. No se crea una Task ni se prioriza automáticamente una capacidad
nueva.

### 18. Estado final

```text
Task: Cerrada
Implementación: Publicada
Revisión técnica: Aprobada
Validación funcional: Aprobada por el usuario
Commit: 3d551a0e9aba88f3fb50644f612edd62c88a80cd
Push: Completado
```
