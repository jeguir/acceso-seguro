(function ($) {
  $(function () {
    // Inicializa color picker (si está disponible)
    if ($.fn.wpColorPicker) {
      $(".as-color").wpColorPicker();
    }

    // Botón: Restablecer colores del modal
    $(document).on("click", "#as-ui-reset-colors", function (e) {
      e.preventDefault();

      const defaults = {
        title_color: "#111111",
        input_bg: "#ffffff",
        input_border_color: "#dddddd",
        input_border_width: "1",
      };

      const $title = $('input[name="as_settings[ui][title_color]"]');
      const $bg    = $('input[name="as_settings[ui][input_bg]"]');
      const $bd    = $('input[name="as_settings[ui][input_border_color]"]');
      const $bdw   = $('input[name="as_settings[ui][input_border_width]"]');

      if ($.fn.wpColorPicker) {
        if ($title.length) $title.wpColorPicker("color", defaults.title_color);
        if ($bg.length)    $bg.wpColorPicker("color", defaults.input_bg);
        if ($bd.length)    $bd.wpColorPicker("color", defaults.input_border_color);
      } else {
        if ($title.length) $title.val(defaults.title_color);
        if ($bg.length)    $bg.val(defaults.input_bg);
        if ($bd.length)    $bd.val(defaults.input_border_color);
      }

      if ($bdw.length) $bdw.val(defaults.input_border_width);

      // Marca el formulario como “cambiado” (por si algún navegador/tema lo requiere)
      $title.trigger("change");
      $bg.trigger("change");
      $bd.trigger("change");
      $bdw.trigger("change");
    });
  });
})(jQuery);
