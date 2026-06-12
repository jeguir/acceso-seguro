(function () {
  function qs(sel) {
    return document.querySelector(sel);
  }

  function showMsg(text, ok) {
    const box = qs("#as-auth-msg");
    if (!box) return;
    box.style.display = "block";
    box.textContent = text;
    box.style.borderColor = ok ? "#2c7" : "#d33";
  }

  function hideMsg() {
    const box = qs("#as-auth-msg");
    if (!box) return;
    box.style.display = "none";
    box.textContent = "";
  }

  function openModal() {
    const m = qs("#as-auth-modal");
    if (!m) return;
    m.style.display = "block";
    showForgot(false);
    showRegister(false);
  }

  function closeModal() {
    const m = qs("#as-auth-modal");
    if (!m) return;
    m.style.display = "none";
    hideMsg();
    showForgot(false);
  }

  function showForgot(show) {
    const f = qs("#as-forgot-form");
    const loginForm = qs("#as-login-form");
    const link = qs("#as-open-forgot");

    const openReg = qs("#as-open-register");
    const openLogin = qs("#as-open-login");

    if (!f || !loginForm || !link) return;

    f.style.display = show ? "block" : "none";
    loginForm.style.display = show ? "none" : "block";
    link.style.display = show ? "none" : "inline";

    // En modo "recuperar", ocultamos los enlaces de alternancia
    if (openReg) openReg.style.display = show ? "none" : "inline";
    if (openLogin) openLogin.style.display = show ? "none" : "inline";

    hideMsg();
  }

  function showRegister(show) {
    const loginBlock = qs("#as-login-block");
    const regBlock = qs("#as-register-block");
    const openReg = qs("#as-open-register");
    const openLogin = qs("#as-open-login");
    const forgotLink = qs("#as-open-forgot");

    if (loginBlock) loginBlock.style.display = show ? "none" : "block";
    if (regBlock) regBlock.style.display = show ? "block" : "none";

    // Estos enlaces están dentro de los bloques, pero por si acaso:
    if (openReg) openReg.style.display = show ? "none" : "inline";
    if (openLogin) openLogin.style.display = show ? "inline" : "none";

    // Solo mostramos "olvidé contraseña" en login
    if (forgotLink) forgotLink.style.display = show ? "none" : "inline";

    hideMsg();
  }

  async function post(action, payload) {
    const url =
      window.AS_AUTH && window.AS_AUTH.ajaxUrl
        ? window.AS_AUTH.ajaxUrl
        : "/wp-admin/admin-ajax.php";
    const nonce =
      window.AS_AUTH && window.AS_AUTH.nonce ? window.AS_AUTH.nonce : "";

    const body = new URLSearchParams(
      Object.assign(
        {
          action: action,
          nonce: nonce,
          redirect: window.location.href,
        },
        payload
      )
    );

    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body,
    });

    const json = await res.json();
    return { status: res.status, json };
  }

  document.addEventListener("click", function (e) {
    const t = e.target;
    if (t && t.matches("[data-as-auth-open]")) {
      e.preventDefault();
      openModal();
    }

    if (t && t.id === "as-close-modal") {
      e.preventDefault();
      closeModal();
    }

    if (t && t.id === "as-open-forgot") {
      e.preventDefault();
      showForgot(true);
    }

    if (t && t.id === "as-cancel-forgot") {
      e.preventDefault();
      showForgot(false);
      showRegister(false);
    }

    if (t && t.id === "as-open-register") {
      e.preventDefault();
      showRegister(true);
    }

    if (t && t.id === "as-open-login") {
      e.preventDefault();
      showRegister(false);
    }
  });

  // Cierra modal al clicar fuera
  document.addEventListener("click", function (e) {
    const modal = qs("#as-auth-modal");
    const box = qs("#as-auth-box");
    if (!modal || !box) return;

    if (modal.style.display === "block" && e.target === modal) {
      e.preventDefault();
      modal.style.display = "none";
      hideMsg();
    }
  });

  document.addEventListener("submit", async function (e) {
    const form = e.target;
    if (!form) return;

    if (form.id === "as-login-form") {
      e.preventDefault();
      hideMsg();

      const login = form.querySelector('[name="login"]').value;
      const password = form.querySelector('[name="password"]').value;

      try {
        const { json } = await post("as_login", { login, password });
        if (json.success && json.data && json.data.redirect) {
          window.location.href = json.data.redirect;
          return;
        }
        showMsg(
          json.data && json.data.message
            ? json.data.message
            : "No se ha podido iniciar sesión.",
          false
        );
      } catch (err) {
        showMsg(AS_AUTH_TEXTS.login_error, false);
      }
    }

    if (form.id === "as-register-form") {
      e.preventDefault();
      hideMsg();

      const email = form.querySelector('[name="email"]').value;
      const password = form.querySelector('[name="password"]').value;
      const first_name = form.querySelector('[name="first_name"]')?.value || "";
      const redirect = window.location.href;
      const privacy = form.querySelector('[name="privacy"]')?.checked
        ? "1"
        : "";
      const as_hp = form.querySelector('[name="as_hp"]')?.value || "";

      try {
        const { json } = await post("as_register", {
          email,
          password,
          first_name,
          redirect,
          privacy,
          as_hp,
        });

        if (json.success && json.data && json.data.redirect) {
          window.location.href = json.data.redirect;
          return;
        }

        showMsg(
          json.data && json.data.message
            ? json.data.message
            : AS_AUTH_TEXTS.register_error,
          false
        );
      } catch (err) {
        showMsg(AS_AUTH_TEXTS.network_error, false);
      }
    }

    if (form.id === "as-forgot-form") {
      e.preventDefault();
      hideMsg();

      const email = form.querySelector('[name="email"]').value;

      try {
        const { json } = await post("as_forgot", { email });
        // Siempre mostramos el mensaje genérico (success o error)
        const msg =
          json.data && json.data.message
            ? json.data.message
            : AS_AUTH_TEXTS.forgot_generic;

        const loginInput = qs('#as-login-form [name="login"]');
        if (loginInput) loginInput.value = email;
        // Volvemos al login
        showForgot(false);
        showMsg(msg, true);
      } catch (err) {
        showMsg(AS_AUTH_TEXTS.network_error, false);
      }
    }
  });
})();
