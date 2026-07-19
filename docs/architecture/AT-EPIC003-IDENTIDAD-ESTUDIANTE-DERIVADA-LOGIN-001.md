# AT-EPIC003-IDENTIDAD-ESTUDIANTE-DERIVADA-LOGIN-001

## Identidad de estudiante y profesor derivada durante login

**EPIC:** EPIC-003 — Consolidación del modelo de identidad, roles y participación académica.
**ADR:** ADR-002 — Evolución del modelo de identidad y participación académica.
**Tipo:** [ARQ] [TEC] [GOV] [MET].
**Estado:** En ejecución.

## Objetivo

Separar la resolución de identidad personal y de especializaciones de los
permisos históricos. La transición debe conservar el comportamiento vigente
hasta que Tasks posteriores integren el resolver y migren sus consumidores.

## Fuente de identidad

```text
login.id_login
→ usuario.login

usuario.id_usuario
→ estudiante.usuario

usuario.id_usuario
→ profesor.usuario
```

La relación `usuario → estudiante` determina la especialización de estudiante,
`estudiante.tipo_est` determina su estado académico y la relación
`usuario → profesor` determina la especialización de profesor. Los permisos no
son fuente de identidad.

## Resultado implementado

### TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001

Estado:

```text
Completada.
```

Commit:

```text
814ecb2d703d8df1638a53e660a068c5f29e3b06
feat(identity): add read-only login identity resolver
```

Resultado:

* resolver independiente creado;
* resolución de solo lectura;
* cardinalidades explícitas `NONE`, `SINGLE` y `MULTIPLE`;
* identidad estudiante derivada desde la relación;
* identidad profesor derivada desde la relación;
* sin integración todavía en `ajax/login.php`;
* sin modificación de sesión;
* sin consulta o sincronización de permisos;
* comportamiento observable preservado.

Estado remoto:

```text
Publicado en origin/refactor/fase-0-seguridad.
```

## Evidencia técnica

La revisión técnica y estática aprobó el resolver con observaciones no
bloqueantes. La sintaxis de los tres archivos, el autoload PSR-4 y la
compatibilidad con PHP 8.2.12 fueron confirmados. No se detectaron efectos
colaterales, escrituras, consultas de permisos ni ocultación de multiplicidad.

No hubo validación funcional productiva porque el componente todavía no tiene
un consumidor dentro del flujo de login.

## Observaciones no bloqueantes

* `IdentityResolution` permite la construcción manual de estados
  semánticamente contradictorios, aunque `IdentityResolver` no los produce;
* la versión mínima de PHP no está declarada en Composer; el uso de `readonly`
  requiere PHP 8.1 o superior.

Estas observaciones no constituyen decisiones arquitectónicas nuevas y quedan
pendientes para la integración futura.

## Compatibilidad histórica

* El permiso `5` permanece como compatibilidad transitoria y no ha sido
  retirado.
* `$_SESSION['estudiante']` se mantiene.
* El permiso `4` no es una fuente confiable de identidad docente.
* Un profesor sin permiso `4` no se corrige durante login.
* El permiso `3` continúa pendiente de sustitución.
* La sincronización de permisos durante login está prohibida.

El resolver no sustituye todavía estos mecanismos en el flujo productivo.

## Secuencia de progreso

```text
[✓] TASK-EPIC003-RESOLVER-IDENTIDAD-LOGIN-001
[ ] TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001
```

Solo el paso correspondiente al resolver está completado.

## Próximo incremento

### TASK-EPIC003-INTEGRAR-IDENTIDAD-DERIVADA-LOGIN-001

Objetivo provisional:

```text
Integrar el resolver en paralelo dentro de ajax/login.php, sin retirar
sesiones históricas, sin cambiar redirección, sin modificar permisos y sin
alterar capacidades efectivas.
```

La integración, la observabilidad de discrepancias, `perfil.ver` y la migración
de redirección y navegación pertenecen a Tasks posteriores.
