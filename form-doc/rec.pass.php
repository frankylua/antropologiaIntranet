<?php
declare(strict_types=1);
require_once __DIR__.'/../src/bootstrap/session.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
header('Cache-Control: no-store');header('Referrer-Policy: no-referrer');header('X-Frame-Options: DENY');header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
if(!isset($_SESSION['csrf_reset'])||!is_string($_SESSION['csrf_reset'])||strlen($_SESSION['csrf_reset'])!==64)$_SESSION['csrf_reset']=bin2hex(random_bytes(32));
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Recuperar contraseña</title><link rel="stylesheet" href="../dist/css/bootstrap.min.css"></head>
<body class="bg-light"><main class="container py-5"><div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3" id="recovery-title">Recuperar contraseña</h1>
<p id="recovery-help">Ingresa el correo de tu cuenta. Te enviaremos un enlace para elegir una nueva contraseña.</p>
<div id="recovery-message" class="alert d-none" role="status" aria-live="polite"></div>
<form id="recovery-request" method="post">
<label class="form-label" for="recovery-email">Correo electrónico</label>
<input class="form-control mb-3" id="recovery-email" name="correo" type="email" autocomplete="email" maxlength="254" required>
<button class="btn btn-dark w-100" type="submit">Enviar enlace</button>
</form>
<form id="recovery-reset" method="post" hidden>
<label class="form-label" for="recovery-password">Nueva contraseña</label>
<input class="form-control mb-3" id="recovery-password" name="pass" type="password" autocomplete="new-password" minlength="1" maxlength="200" required>
<label class="form-label" for="recovery-confirm">Confirmar nueva contraseña</label>
<input class="form-control mb-3" id="recovery-confirm" name="pass2" type="password" autocomplete="new-password" minlength="1" maxlength="200" required>
<button class="btn btn-dark w-100" type="submit">Guardar nueva contraseña</button>
</form>
<a class="d-block text-center mt-3" href="rec.pass.php" id="recovery-again" hidden>Solicitar otro enlace</a>
<a class="d-block text-center mt-3" href="login.php">Volver a iniciar sesión</a>
<noscript>Activa JavaScript para recuperar tu contraseña.</noscript>
</div></div></div></div></main>
<script type="application/json" id="recovery-config"><?=json_encode(['csrf'=>$_SESSION['csrf_reset']],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR)?></script>
<script src="scripts/recuperacion.js?v=<?=filemtime(__DIR__.'/scripts/recuperacion.js')?>"></script>
</body></html>
