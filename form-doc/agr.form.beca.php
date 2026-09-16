<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

$becaLogin = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
    ? (int) $_SESSION['login']
    : 0;
$becaRolGlobal = \App\Security\Authorization::hasAny(['admin', 'comite']);
$becaRolPropio = $becaLogin > 0
    && isset($_SESSION['estudiante'])
    && is_scalar($_SESSION['estudiante'])
    && (int) $_SESSION['estudiante'] === $becaLogin
    && \App\Security\Authorization::hasCapability('perfil.ver');

if (!$becaRolGlobal && !$becaRolPropio) {
    return;
}

if (
    !isset($_SESSION['csrf_beca'])
    || !is_string($_SESSION['csrf_beca'])
    || preg_match('/^[a-f0-9]{64}$/D', $_SESSION['csrf_beca']) !== 1
) {
    $_SESSION['csrf_beca'] = bin2hex(random_bytes(32));
}

$becaTokenCsrf = $_SESSION['csrf_beca'];
?>
<div class="row">
    <div id="form_beca">
        <div
            id="beca_app"
            data-csrf="<?= htmlspecialchars($becaTokenCsrf, ENT_QUOTES, 'UTF-8') ?>"
            data-global="<?= $becaRolGlobal ? '1' : '0' ?>"
        >
            <div class="col-12" id="beca_card"></div>
            <div class="col" id="beca_editor"></div>
            <div class="row justify-content-center d-none" id="mnsj_row_beca">
                <div class="col-lg-8 alert text-center" role="alert" id="mnsj_beca"></div>
            </div>
            <div class="row" id="boton_beca">
                <div class="col d-grid gap-2 mb-3">
                    <button class="btn btn-outline-dark" type="button" id="btn_beca">Agregar Beca</button>
                </div>
            </div>
        </div>
    </div>
</div>
