<?php
$acuerdosMuestra = require __DIR__ . '/../src/Config/autoalta_acuerdos_muestra.php';
$escaparAcuerdosMuestra = static fn(string $texto): string => htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div class="row mt-3 mb-3">
  <div class="col-12">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="acuerdos-autoalta-check" name="acuerdos_muestra" value="1" required
        aria-describedby="acuerdos-autoalta-nota">
      <label class="form-check-label" for="acuerdos-autoalta-check">
        He leído y acepto los
        <a class="link-dark" id="abrir-acuerdos-autoalta" href="#acuerdos-autoalta-modal"
          data-bs-toggle="modal" data-bs-target="#acuerdos-autoalta-modal" aria-haspopup="dialog">
          <?php echo $escaparAcuerdosMuestra($acuerdosMuestra['titulo']); ?>
        </a>
      </label>
    </div>
    <p class="small text-muted mt-2 mb-0" id="acuerdos-autoalta-nota">Vista de prueba para la muestra interna.</p>
  </div>
</div>

<div class="modal fade" id="acuerdos-autoalta-modal" tabindex="-1"
  aria-labelledby="acuerdos-autoalta-titulo" aria-describedby="acuerdos-autoalta-aviso" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5" id="acuerdos-autoalta-titulo"><?php echo $escaparAcuerdosMuestra($acuerdosMuestra['titulo']); ?></h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar acuerdos y condiciones"></button>
      </div>
      <div class="modal-body">
        <p class="fw-bold mb-1"><?php echo $escaparAcuerdosMuestra($acuerdosMuestra['programa']); ?></p>
        <p class="small text-muted"><?php echo $escaparAcuerdosMuestra($acuerdosMuestra['version']); ?></p>
        <div class="alert alert-warning" id="acuerdos-autoalta-aviso">
          <?php echo $escaparAcuerdosMuestra($acuerdosMuestra['aviso']); ?>
        </div>
        <?php foreach ($acuerdosMuestra['apartados'] as $apartado): ?>
          <h3 class="h6 mt-3"><?php echo $escaparAcuerdosMuestra($apartado['titulo']); ?></h3>
          <p><?php echo $escaparAcuerdosMuestra($apartado['texto']); ?></p>
        <?php endforeach; ?>
        <p class="small text-muted mb-0"><?php echo $escaparAcuerdosMuestra($acuerdosMuestra['nota_cierre']); ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cerrar y volver al formulario</button>
      </div>
    </div>
  </div>
</div>
