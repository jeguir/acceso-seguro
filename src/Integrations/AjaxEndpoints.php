<?php
namespace AccesoSeguro\Integrations;

defined('ABSPATH') || exit;

final class AjaxEndpoints {

	public function register(): void {
		// Login
		add_action('wp_ajax_as_login', [$this, 'handleLogin']);
		add_action('wp_ajax_nopriv_as_login', [$this, 'handleLogin']);

		// Register
		add_action('wp_ajax_as_register', [$this, 'handleRegister']);
		add_action('wp_ajax_nopriv_as_register', [$this, 'handleRegister']);

        // Recuperar contraseña
        add_action('wp_ajax_as_forgot', [$this, 'handleForgot']);
        add_action('wp_ajax_nopriv_as_forgot', [$this, 'handleForgot']);
	}

    private function requireNonce(): void {
        $nonce = isset($_POST['nonce']) ? (string) wp_unslash($_POST['nonce']) : '';
        if (!$nonce || !wp_verify_nonce($nonce, 'as_auth')) {
            wp_send_json_error([
                'code' => 'BAD_NONCE',
                'message' => __('Solicitud no válida.', 'acceso-seguro'),
            ], 403);
        }
    }

    private function resolveRedirect(): string {
        $redirect = isset($_POST['redirect']) ? (string) wp_unslash($_POST['redirect']) : '';

        if ($redirect) {
            // Solo permitimos URLs internas (seguras)
            return wp_validate_redirect($redirect, home_url('/'));
        }

        // Fallback: referrer
        $ref = wp_get_referer();
        if ($ref) {
            return wp_validate_redirect($ref, home_url('/'));
        }

        return home_url('/');
    }

    private function enforceMinDuration(float $start, int $minMs = 450): void {
        $elapsedMs = (microtime(true) - $start) * 1000;
        $remaining = $minMs - (int) $elapsedMs;
        if ($remaining > 0) {
            usleep($remaining * 1000);
        }
    }

    public function handleLogin(): void {
        $this->requireNonce();
        $start = microtime(true);

        $login = isset($_POST['login']) ? (string) wp_unslash($_POST['login']) : '';
        $pass  = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';

        $login = trim($login);

        if ($login === '' || $pass === '') {
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'INVALID_INPUT',
                'message' => __('No se ha podido iniciar sesión. Verifica tus datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 400);
        }

        $email = is_email($login) ? $login : null;
        $usernameCandidate = $email ? \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email) : null;

        $ctx = new \AccesoSeguro\Infra\RequestContext('login', $usernameCandidate ?: ($email ? null : $login), $email, 'ajax');
        $result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);

        if (!$result->allowed) {
            \AccesoSeguro\Infra\Logger::logDenied($ctx, $result);
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'BLOCKED',
                'message' => $result->publicMessage,
            ], 403);
        }

        $user = wp_signon([
            'user_login'    => $login,
            'user_password' => $pass,
            'remember'      => true,
        ], is_ssl());

        if (is_wp_error($user)) {
            // Mensaje genérico (sin pistas)
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'LOGIN_FAILED',
                'message' => __('No se ha podido iniciar sesión. Verifica tus datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 403);
        }

        $this->enforceMinDuration($start);
        wp_send_json_success([
            'ok' => true,
            'logged_in' => true,
            'user_id' => (int) $user->ID,
            'redirect' => $this->resolveRedirect(),
        ], 200);
    }

	public function handleRegister(): void {
        $this->requireNonce();
        $start = microtime(true);

        if (class_exists('\AccesoSeguro\Infra\Options') && !\AccesoSeguro\Infra\Options::get('general.allow_register', true)) {
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'REGISTER_DISABLED',
                'message' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 403);
        }

        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $pass  = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';
        $firstName = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';

        if (!$email || !is_email($email) || $pass === '') {
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'INVALID_INPUT',
                'message' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 400);
        }

        $privacy = !empty($_POST['privacy']);
        if (!$privacy) {
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'PRIVACY_REQUIRED',
                'message' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 400);
        }

        $hp = isset($_POST['as_hp']) ? (string) wp_unslash($_POST['as_hp']) : '';
        $hp = trim($hp);

        if ($hp !== '') {
            // Bloqueo silencioso (bot)
            $ctx = new \AccesoSeguro\Infra\RequestContext(
                'register',
                \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email),
                $email,
                'ajax'
            );

            $result = new \AccesoSeguro\Antispam\Result(false, 10, 'HONEYPOT', $this->resolveRedirect(), []);
            \AccesoSeguro\Infra\Logger::logDenied($ctx, $result);

            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'BLOCKED',
                'message' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 403);
        }

        // Username candidato desde email (WooCommerce suele hacer esto)
        $username = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
        $ctx = new \AccesoSeguro\Infra\RequestContext('register', $username, $email, 'ajax');

        $result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);
        if (!$result->allowed) {
            \AccesoSeguro\Infra\Logger::logDenied($ctx, $result);
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'BLOCKED',
                'message' => $result->publicMessage,
            ], 403);
        }

        // Crear usuario (WooCommerce si está disponible)
        if (class_exists('\WooCommerce') && function_exists('wc_create_new_customer')) {
            $user_id = wc_create_new_customer($email, $username ?: '', $pass);
        } else {
            $user_id = wp_create_user($username ?: $email, $pass, $email);
        }

        if (is_wp_error($user_id)) {
            // Mensaje genérico para no dar pistas
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'CREATE_FAILED',
                'message' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),
            ], 400);
        }

        if ($firstName !== '') {
            update_user_meta($user_id, 'first_name', $firstName);
            update_user_meta($user_id, 'billing_first_name', $firstName);
            update_user_meta($user_id, 'shipping_first_name', $firstName);
        }
        
        // Autologin
        $creds = [
            'user_login'    => $email, // WP aceptará email si está permitido por plugins; si no, usamos username
            'user_password' => $pass,
            'remember'      => true,
        ];

        // Mejor: usar el login real
        $login = $username ?: $email;
        $creds['user_login'] = $login;

        // Evitar antispam en el autologin inmediato tras registro
        add_filter('as_skip_antispam', '__return_true', 99);
        $user = wp_signon($creds, is_ssl());
        remove_filter('as_skip_antispam', '__return_true', 99);

        if (is_wp_error($user)) {
            $this->enforceMinDuration($start);
            wp_send_json_success([
                'ok' => true,
                'logged_in' => false,
                'message' => __('Cuenta creada. Inicia sesión para continuar.', 'acceso-seguro'),
            ], 200);
        }

        $this->enforceMinDuration($start);
        wp_send_json_success([
            'ok' => true,
            'logged_in' => true,
            'user_id' => (int) $user_id,
            'redirect' => $this->resolveRedirect(),
        ], 200);
    }

    public function handleForgot(): void {
        $this->requireNonce();
        $start = microtime(true);

        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        if (!$email || !is_email($email)) {
            $this->enforceMinDuration($start);
            wp_send_json_error([
                'code' => 'INVALID_INPUT',
                'message' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),
            ], 200);
        }

        // Antispam (misma idea: sin pistas)
        $username = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
        $ctx = new \AccesoSeguro\Infra\RequestContext('forgot', $username, $email, 'ajax');

        $result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);
        if (!$result->allowed) {
            \AccesoSeguro\Infra\Logger::logDenied($ctx, $result);
            $this->enforceMinDuration($start);
            wp_send_json_success([
                'ok' => true,
                'message' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),
            ], 200);
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            // Siempre el mismo mensaje
            $this->enforceMinDuration($start);
            wp_send_json_success([
                'ok' => true,
                'message' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),
            ], 200);
        }

        // Genera clave y envía email (core)
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            $this->enforceMinDuration($start);
            wp_send_json_success([
                'ok' => true,
                'message' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),
            ], 200);
        }

        $reset_url = network_site_url("wp-login.php?action=rp&key={$key}&login=" . rawurlencode($user->user_login), 'login');

        $subject = sprintf('[%s] Restablecer contraseña', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
        $message = "Para restablecer tu contraseña, abre este enlace:\n\n" . $reset_url . "\n\nSi no lo solicitaste, ignora este mensaje.";

        wp_mail($email, $subject, $message);

        $this->enforceMinDuration($start);
        wp_send_json_success([
            'ok' => true,
            'message' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),
        ], 200);
    }
}
