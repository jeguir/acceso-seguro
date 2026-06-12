<?php
namespace AccesoSeguro\Antispam;

use AccesoSeguro\Infra\Options;
use AccesoSeguro\Infra\RequestContext;

defined('ABSPATH') || exit;

final class RateLimiter {

	public static function checkAndIncrement(RequestContext $ctx): bool {
		$storage = \AccesoSeguro\Infra\Options::get('general.rate_limit_storage', 'transient');
		if ($storage === 'db') {
			return self::checkAndIncrementDb($ctx);
		}

		$enabled = Options::get('general.rate_limit_enabled', true);
		if (!$enabled) {
			return true;
		}

		$action = is_string($ctx->action) ? $ctx->action : '';

		switch ($action) {
			case 'register':
				$window = (int) Options::get('general.rate_limit_register_window_seconds', 900);
				$max    = (int) Options::get('general.rate_limit_register_max_attempts', 4);
				break;

			case 'forgot':
				$window = (int) Options::get('general.rate_limit_forgot_window_seconds', 900);
				$max    = (int) Options::get('general.rate_limit_forgot_max_attempts', 4);
				break;

			case 'login':
			default:
				$window = (int) Options::get('general.rate_limit_login_window_seconds', 300);
				$max    = (int) Options::get('general.rate_limit_login_max_attempts', 8);
				break;
		}

		$window = max(30, $window);
		$max    = max(1, $max);

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip = is_string($ip) ? trim($ip) : '';

        if ($ip === '') {
            // Si no podemos identificar IP, no aplicamos rate limit
            return true;
        }

        // Hash estable (no guardamos IP en claro)
        $ipHash = hash('sha256', $ip . '|' . wp_salt('auth'));

		$key = 'as_rl_' . md5($ctx->action . '|' . $ipHash);

		$now = time();
		$state = self::getState($key);

		if (!is_array($state)) {
			$state = ['start' => $now, 'count' => 0];
		}

		// Si la ventana expiró, reiniciar
		if (($now - (int)$state['start']) > $window) {
			$state = ['start' => $now, 'count' => 0];
		}

		$state['count'] = (int)$state['count'] + 1;

		// Guardar estado (transient o DB)
		$state['expires'] = $state['start'] + $window;
		self::setState($key, $state, $window);

		// Permitimos hasta max
		return ((int)$state['count'] <= $max);
	}

	private static function getState(string $key): ?array {
		$state = get_transient($key);
		return is_array($state) ? $state : null;
	}

	private static function setState(string $key, array $state, int $window): void {
		set_transient($key, $state, $window);
	}

	private static function checkAndIncrementDb(\AccesoSeguro\Infra\RequestContext $ctx): bool {
		// Si rate limit está desactivado, no bloqueamos
		if (!\AccesoSeguro\Infra\Options::get('general.rate_limit_enabled', true)) {
			return true;
		}

		global $wpdb;

		$table = \AccesoSeguro\Infra\Db::tableRateLimit();
		$now = time();

		$window = self::windowSecondsForAction($ctx->action);
		$max    = self::maxAttemptsForAction($ctx->action);

		// clave estable por acción + ip_hash
		$ipHash = \AccesoSeguro\Infra\ClientIp::hash();
		if ($ipHash === '') return true;

		$key = hash('sha256', $ctx->action . '|' . $ipHash);

		$row = $wpdb->get_row(
			$wpdb->prepare("SELECT start_ts, count, expires_ts FROM {$table} WHERE rl_key = %s", $key),
			ARRAY_A
		);

		// ventana expirada o no existe
		if (!$row || (int)$row['expires_ts'] <= $now) {
			$start = $now;
			$count = 1;
			$expires = $now + $window;

			$wpdb->replace($table, [
				'rl_key' => $key,
				'start_ts' => $start,
				'count' => $count,
				'expires_ts' => $expires,
			], ['%s','%d','%d','%d']);

			return true;
		}

		$count = (int)$row['count'] + 1;

		$wpdb->update(
			$table,
			['count' => $count],
			['rl_key' => $key],
			['%d'],
			['%s']
		);

		// si supera max, bloquea
		return ($count <= $max);
	}

	private static function windowSecondsForAction(string $action): int {
		switch ($action) {
			case 'register':
				$window = (int) Options::get('general.rate_limit_register_window_seconds', 900);
				break;
			case 'forgot':
				$window = (int) Options::get('general.rate_limit_forgot_window_seconds', 900);
				break;
			case 'login':
			default:
				$window = (int) Options::get('general.rate_limit_login_window_seconds', 300);
				break;
		}
		return max(30, $window);
	}

	private static function maxAttemptsForAction(string $action): int {
		switch ($action) {
			case 'register':
				$max = (int) Options::get('general.rate_limit_register_max_attempts', 4);
				break;
			case 'forgot':
				$max = (int) Options::get('general.rate_limit_forgot_max_attempts', 4);
				break;
			case 'login':
			default:
				$max = (int) Options::get('general.rate_limit_login_max_attempts', 8);
				break;
		}
		return max(1, $max);
	}
}
