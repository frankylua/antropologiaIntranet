(function () {
  'use strict';
  const element = document.getElementById('crud-csrf-config');
  if (!element) return;
  const config = JSON.parse(element.textContent);
  if (!/^[a-f0-9]{64}$/.test(config.token)) return;
  const paths = new Set(config.endpoints.map(function (url) {
    return new URL(url, window.location.href).pathname;
  }));
  function applies(options) {
    if (String(options.type || options.method || 'GET').toUpperCase() !== 'POST') return false;
    try {
      const url = new URL(options.url, window.location.href);
      return url.origin === window.location.origin && paths.has(url.pathname);
    } catch (_) {
      return false;
    }
  }
  $.ajaxPrefilter(function (options, originalOptions, xhr) {
    if (applies(options)) xhr.setRequestHeader('X-CRUD-CSRF-Token', config.token);
  });
  $(document).ajaxError(function (event, xhr, options) {
    if (applies(options) && xhr.responseJSON && xhr.responseJSON.error === 'CSRF_INVALIDO') {
      window.alert(xhr.responseJSON.mensaje || 'Recargue la página y vuelva a intentar.');
    }
  });
}());
