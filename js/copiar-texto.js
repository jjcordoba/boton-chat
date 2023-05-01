jQuery(document).ready(function($) {
  $('.texto-autocopiable').on('click', function() {
      var texto = $(this).text();
      var textarea = $('<textarea>').val(texto).appendTo('body').select();
      document.execCommand('copy');
      textarea.remove();
      alert('El texto se ha copiado al portapapeles.');
  });
});
alert('El archivo "copiar-texto.js" se está ejecutando correctamente en el dashboard de WordPress.');
