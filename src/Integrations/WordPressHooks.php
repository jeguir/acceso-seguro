<?php
namespace AccesoSeguro\Integrations;

use AccesoSeguro\Antispam\Engine;
use AccesoSeguro\Infra\RequestContext;
use AccesoSeguro\Infra\Logger;

defined('ABSPATH') || exit;

final class WordPressHooks {

	public function register(): void {
		// Registro clásico WP: valida antes de crear el usuario
		add_filter('registration_errors', [$this, 'filterRegistrationErrors'], 10, 3);

		// Bloqueo global de login (incluye wp_signon, wp-login.php, etc.)
		add_filter('authenticate', [$this, 'filterAuthenticate'], 5, 3);

		add_filter('wp_insert_user_empty_data', [$this, 'filterInsertUserEmptyData'], 10, 2);
		add_action('user_profile_update_errors', [$this, 'actionUserProfileUpdateErrors'], 10, 3);
	}

	public function filterRegistrationErrors($errors, $sanitized_user_login, $user_email) {
		if (is_wp_error($errors) && $errors->has_errors()) {
			// Si WP ya detectó errores, no interferimos.
			return $errors;
		}

		$u = is_string($sanitized_user_login) ? trim((string)$sanitized_user_login) : '';
		$e = is_string($user_email) ? trim((string)$user_email) : '';

		if ($u === '' && $e !== '') {
			$u = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($e) ?? '';
		}

		$ctx = new RequestContext('register', $u !== '' ? $u : null, $e !== '' ? $e : null, 'wp');

		$ctx = new RequestContext('register', (string) $sanitized_user_login, (string) $user_email, 'wp');
		$engine = new Engine();
		$result = $engine->evaluate($ctx);

		if (!$result->allowed) {
			Logger::logDenied($ctx, $result);

			// Mensaje genérico (sin pistas)
			if (!is_wp_error($errors)) {
				$errors = new \WP_Error();
			}
			$errors->add('as_blocked', $result->publicMessage);
		}

		return $errors;
	}

	public function filterAuthenticate($user, $username, $password) {
		// Si ya viene error de otro plugin/WP, no lo pisamos
		if ($user instanceof \WP_Error) {
			return $user;
		}

		// Saltar antispam si viene marcado (ej. autologin tras registro)
		if (apply_filters('as_skip_antispam', false)) {
			return $user;
		}

		// En login, a veces $username puede ser email
		$u = is_string($username) ? trim($username) : '';
		$email = (is_email($u)) ? $u : null;

		// Fallback recomendado: si el login es por email,
		// usamos el candidato derivado del email para heurísticas de username
		$unameCandidate = null;
		if ($email) {
			$unameCandidate = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
		}

		$ctx = new RequestContext(
			'login',
			$unameCandidate ?: ($email ? null : $u),
			$email,
			'wp'
		);

		$engine = new Engine();
		$result = $engine->evaluate($ctx);

		if (!$result->allowed) {
			Logger::logDenied($ctx, $result);
			return new \WP_Error('as_blocked', $result->publicMessage);
		}

		return $user;
	}

	public function actionUserProfileUpdateErrors($errors, $update, $user): void {
		// Solo en creación (no updates)
		if (!empty($update)) {
			return;
		}

		// En el alta desde admin, los datos vienen de POST
		$username = isset($_POST['user_login']) ? sanitize_user(wp_unslash($_POST['user_login']), true) : '';
		$email    = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

		// Fallback: si no hay username, derivarlo del email
		if ($username === '' && $email !== '') {
			$candidate = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
			if ($candidate) {
				$username = $candidate;
			}
		}

		$ctx = new \AccesoSeguro\Infra\RequestContext('register', $username !== '' ? $username : null, $email !== '' ? $email : null, 'wp_admin');
		$result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);

		if (!$result->allowed) {
			\AccesoSeguro\Infra\Logger::logDenied($ctx, $result);

			// ✅ Esto es lo que evita el “Usuario creado”
			$errors->add('as_blocked', $result->publicMessage);
		}
	}

	public function filterInsertUserEmptyData($maybe_empty, $userdata) {
		// Solo en creación (no updates)
		if (!empty($userdata['ID'])) {
			return $maybe_empty;
		}

		$username = isset($userdata['user_login']) ? (string) $userdata['user_login'] : '';
		$email    = isset($userdata['user_email']) ? (string) $userdata['user_email'] : '';

		$username = sanitize_user($username, true);
		$email    = sanitize_email($email);

		if ($username === '' && $email !== '') {
			$candidate = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
			if ($candidate) {
				$username = $candidate;
			}
		}

		$ctx = new \AccesoSeguro\Infra\RequestContext('register', $username !== '' ? $username : null, $email !== '' ? $email : null, 'wp_insert_user');
		$result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);

		if (!$result->allowed) {
			\AccesoSeguro\Infra\Logger::logDenied($ctx, $result);

			// ✅ WP maneja correctamente WP_Error aquí (aborta el insert sin warnings)
			return new \WP_Error('as_blocked', $result->publicMessage);
		}

		return $maybe_empty;
	}
}
