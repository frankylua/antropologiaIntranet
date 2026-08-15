# TASK-EPIC003-INVENTARIO-CONSISTENCIA-ROL-ESTUDIANTE-005-001

## Inventario técnico de consistencia del rol histórico Estudiante (permiso 5)

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**AT de origen:** [AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-005-001](../architecture/AT-EPIC003-RESOLUCION-PERMISO-HISTORICO-005-001.md).
**Tipo:** [TEC] [ARQ] [DAT] Inventario de solo lectura.
**Estado:** Ejecutado sobre código y SQL local; pendiente contraste con base activa controlada.

## Alcance y exclusiones

Se inspeccionaron referencias estáticas al permiso `5`, la relación `login → usuario → estudiante`, el catálogo SQL local y los consumidores de la sesión de estudiante. No se modificaron código, datos, sesiones, permisos, migraciones ni configuración.

El archivo `c1441353_antr_db.sql` no está versionado; por ello sus resultados son evidencia local de confianza media y no acreditan el contenido de una base activa.

## Hallazgos

| Evidencia | Hallazgo | Implicación |
| --- | --- | --- |
| `c1441353_antr_db.sql:859-864` | El catálogo local denomina `5` como `estudiante`. | Confirma provisionalmente el nombre histórico. |
| `form-doc/scripts/estudiante.js:123-128` | Toda alta construye `permisos = [5]`; para estados `2` o `3` agrega también `3`. | El alta es el productor vigente de permiso 5. |
| `ajax/estudiante.php:72-91` | El endpoint valida `tipo_est` y persiste el arreglo de permisos recibido. | No existe aún una defensa backend que impida asignar 5 en altas nuevas. |
| `ajax/login.php:34-36` | `id_permiso == 5` crea `$_SESSION['estudiante']` con `id_login`. | El permiso es una fuente actual de identidad de sesión. |
| `ajax/login.php:50-53` | `id_usuario` se carga solo si existe sesión `docente` o `estudiante`. | La relación `login → usuario` no se resuelve todavía de forma independiente. |
| `index.php:13-14` | La redirección de estudiante depende de `$_SESSION['estudiante']`. | Consumidor funcional directo. |
| `form-doc/info.estudiante.php:6-10` | El backend del perfil permite estudiante por esa sesión y usa la misma clave para elegir el perfil propio. | Consumidor funcional y de autorización. |
| `form-doc/header.php:13,77` | Mi perfil y navegación básica dependen, entre otras señales, de `$_SESSION['estudiante']`. | Consumidor de UI; no sustituye la protección backend. |
| `src/Model/Login.php:30-40` | Existen consultas independientes para `retornarIdUsu()` y para `obtenerEstadosAcademicosPorLogin()`. | La estructura permite derivar usuario y estado desde las relaciones, aunque login aún los condiciona al rol heredado. |
| `ajax/estudiante.php:171-183` y `src/Model/Estudiante.php:87-90` | El cambio de estado solo actualiza `estudiante.tipo_est`. | No crea ni retira permiso 5; coincide con la preservación transitoria aprobada. |

## Consistencia de datos que queda por medir

El repositorio no contiene una conexión de solo lectura aprobada a una base activa. Antes de una decisión de migración se deben ejecutar, en una base controlada y sin escrituras, consultas equivalentes a:

```sql
-- Cuentas con permiso 5 sin relación estudiante.
SELECT pl.id_login
FROM permiso_login pl
LEFT JOIN usuario u ON u.login = pl.id_login
LEFT JOIN estudiante e ON e.usuario = u.id_usuario
WHERE pl.id_permiso = 5 AND e.usuario IS NULL;

-- Relaciones estudiante sin permiso 5.
SELECT e.usuario, u.login
FROM estudiante e
JOIN usuario u ON u.id_usuario = e.usuario
LEFT JOIN permiso_login pl
  ON pl.id_login = u.login AND pl.id_permiso = 5
WHERE pl.id_login IS NULL;

-- Cuentas con permiso 5 y más de una especialización o rol explícito.
SELECT pl.id_login, GROUP_CONCAT(DISTINCT pl2.id_permiso) AS permisos
FROM permiso_login pl
JOIN permiso_login pl2 ON pl2.id_login = pl.id_login
WHERE pl.id_permiso = 5
GROUP BY pl.id_login
HAVING COUNT(DISTINCT pl2.id_permiso) > 1;
```

También debe revisarse si una misma cuenta puede tener más de una fila `estudiante` y el tratamiento institucional de `tipo_est = 6` (Eliminado). La sola existencia de esa fila debe conservar identidad histórica, no conceder capacidades.

## Dictamen del inventario

La evidencia estática respalda la decisión del AT: hoy coexisten la relación de dominio `usuario → estudiante` y el permiso histórico 5 como fuentes de identificación. El permiso 5 no es estado académico y no participa en la capacidad derivada actual `reglamento.ver`, que se obtiene desde `estudiante.tipo_est` y excluye el estado 6.

No es seguro congelar su asignación ni retirar su sesión todavía: login, redirección, perfil y navegación mantienen consumidores funcionales. No se identificaron nuevos consumidores que deban añadirse.

## Siguiente paso

Definir el contrato de identidad derivada durante login en `AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001`: resolver `id_usuario` desde `login → usuario`, obtener la especialización desde `usuario → estudiante` y comparar el resultado con el legado sin modificar sesiones ni permisos durante esa fase.
