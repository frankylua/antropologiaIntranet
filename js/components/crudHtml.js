(function (global) {
  'use strict';

  function primitive(value) {
    return value == null || !['string', 'number', 'boolean'].includes(typeof value) ? '' : String(value);
  }

  // Igual que el contrato histórico double_encode=false: conservar entidades completas.
  // Solo para texto HTML y atributos ENTRE COMILLAS; no para URLs, JS ni CSS.
  function text(value) {
    return primitive(value)
      .replace(/&(?!(?:[A-Za-z][A-Za-z0-9]*|#[0-9]+|#x[0-9a-fA-F]+);)/g, '&amp;')
      .replace(/[<>"']/g, character => ({ '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]));
  }

  class Fragment {
    constructor(value) {
      this.value = value;
      Object.freeze(this);
    }
  }

  function html(strings, ...values) {
    if (!Array.isArray(strings) || !Object.prototype.hasOwnProperty.call(strings, 'raw')) {
      throw new TypeError('Use una plantilla literal para generar HTML.');
    }
    let result = strings[0];
    values.forEach(function (value, index) {
      result += (value instanceof Fragment ? value.value : text(value)) + strings[index + 1];
    });
    return result;
  }

  function fragment(strings, ...values) {
    return new Fragment(html(strings, ...values));
  }

  function formatted(value, formatter) {
    const clean = primitive(value).trim().replace(/\s+/g, ' ');
    return clean ? formatter(clean) : '';
  }

  global.CrudHtml = Object.freeze({
    text, html, fragment,
    title: value => formatted(value, cadenaMay),
    firstUpper: value => formatted(value, letraMay)
  });
}(globalThis));
