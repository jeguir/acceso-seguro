<?php
namespace AccesoSeguro\Integrations;

use AccesoSeguro\Antispam\Engine;
use AccesoSeguro\Infra\RequestContext;
use AccesoSeguro\Infra\Logger;

defined('ABSPATH') || exit;

final class WooCommerceHooks {

	public function register(): void {
		add_filter('woocommerce_process_registration_errors', [$this, 'filterWcRegistrationErrors'], 10, 4);
		add_filter('woocommerce_process_login_errors', [$this, 'filterWcLoginErrors'], 10, 3);
	}

	public function filterWcRegistrationErrors($errors, $username, $password, $email) {
		if (is_wp_error($errors) && $errors->has_errors()) {
			return $errors;
		}

		$u = is_string($username) ? trim((string)$username) : '';
		$e = is_string($email) ? trim((string)$email) : '';

		// ✅ Fallback recomendado: si no hay username, sacarlo del email
		if ($u === '' && $e !== '') {
			$u = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($e) ?? '';
		}

		$ctx = new \AccesoSeguro\Infra\RequestContext('register', $u !== '' ? $u : null, $e !== '' ? $e : null, 'woocommerce');
		$result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);

		if (!$result->allowed) {
			\AccesoSeguro\Infra\Logger::logDenied($ctx, $result);
			if (!is_wp_error($errors)) $errors = new \WP_Error();
			$errors->add('as_blocked', $result->publicMessage);
		}

		return $errors;
	}

	public function filterWcLoginErrors($errors, $username, $password) {
		if (is_wp_error($errors) && $errors->has_errors()) {
			return $errors;
		}

		$u = is_string($username) ? trim((string)$username) : '';
		$email = (is_email($u)) ? $u : null;

		// ✅ Si el login es por email, usamos candidato del email para heurísticas de username
		$unameCandidate = null;
		if ($email) {
			$unameCandidate = \AccesoSeguro\Infra\RequestContext::usernameCandidateFromEmail($email);
		}

		$ctx = new \AccesoSeguro\Infra\RequestContext('login', $unameCandidate, $email, 'woocommerce');
		$result = (new \AccesoSeguro\Antispam\Engine())->evaluate($ctx);

		if (!$result->allowed) {
			\AccesoSeguro\Infra\Logger::logDenied($ctx, $result);
			if (!is_wp_error($errors)) $errors = new \WP_Error();
			$errors->add('as_blocked', $result->publicMessage);
		}

		return $errors;
	}

}
