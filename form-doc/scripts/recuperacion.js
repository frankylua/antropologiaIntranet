(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const config = JSON.parse($('recovery-config').textContent);
  const fragment = new URLSearchParams(window.location.hash.slice(1));
  let token = fragment.get('token') || '';
  const hasLink = window.location.hash !== '';
  // El token viaja en fragmento: no entra en access logs, query string ni Referer.
  if (hasLink) window.history.replaceState(null, '', window.location.pathname + window.location.search);
  const message = (text, good = false) => {
    const box = $('recovery-message'); box.className = 'alert ' + (good ? 'alert-success' : 'alert-danger'); box.textContent = text;
  };
  const post = async data => {
    const response = await fetch('../ajax/recuperacion.php', { method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ csrf: config.csrf, ...data }) });
    let body; try { body = await response.json(); } catch (_) { throw new Error('No fue posible completar la operación. Intenta nuevamente.'); }
    if (!response.ok || body.ok !== true) throw new Error(body.mensaje || 'No fue posible completar la operación.');
    return body;
  };
  $('recovery-request').addEventListener('submit', async event => {
    event.preventDefault(); const button = event.currentTarget.querySelector('button'); if (button.disabled) return;
    button.disabled = true;
    try { const body = await post({ op: 'solicitar', correo: $('recovery-email').value }); message(body.mensaje, true); }
    catch (error) { message(error.message); } finally { button.disabled = false; }
  });
  $('recovery-reset').addEventListener('submit', async event => {
    event.preventDefault(); const button = event.currentTarget.querySelector('button'); if (button.disabled) return;
    const pass = $('recovery-password').value, pass2 = $('recovery-confirm').value;
    if (pass !== pass2) { message('Las contraseñas deben ser iguales.'); $('recovery-confirm').focus(); return; }
    button.disabled = true;
    try { await post({ op: 'restablecer', token, pass, pass2 }); token = ''; window.location.assign('login.php?reset=ok'); }
    catch (error) { message(error.message); button.disabled = false; }
  });
  if (hasLink) {
    $('recovery-request').hidden = true; $('recovery-again').hidden = false;
    $('recovery-title').textContent = 'Nueva contraseña'; $('recovery-help').textContent = 'Validando el enlace…';
    if (!/^[a-f0-9]{64}$/.test(token)) { message('El enlace no es válido. Solicita uno nuevo.'); $('recovery-help').textContent = ''; return; }
    post({ op: 'verificar', token }).then(() => {
      $('recovery-reset').hidden = false; $('recovery-help').textContent = 'Ingresa la nueva contraseña dos veces.'; $('recovery-password').focus();
    }).catch(error => { message(error.message); $('recovery-help').textContent = ''; });
  }
})();
