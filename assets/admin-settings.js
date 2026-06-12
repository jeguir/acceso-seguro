(function ($) {
  $(function () {
    const $tld = $(".as-tld-input");
    const $warn = $(".as-warning-tld");

    if (!$tld.length || !$warn.length) return;

    function hasDotPrefixedTld(val) {
      if (!val) return false;

      // Normaliza saltos de línea
      val = String(val).replace(/\r\n/g, "\n").replace(/\r/g, "\n");

      // Separa por líneas / comas / espacios
      const parts = val.split(/[\n, ]+/).filter(Boolean);

      // Detecta tokens tipo ".xyz"
      return parts.some((p) => /^\.[a-z0-9]+$/i.test(p.trim()));
    }

    function updateWarning() {
      const v = $tld.val();
      if (hasDotPrefixedTld(v)) {
        $warn.show();
      } else {
        $warn.hide();
      }
    }

    // Al cargar y al escribir
    updateWarning();
    $tld.on("input change", updateWarning);
  });
})(jQuery);
