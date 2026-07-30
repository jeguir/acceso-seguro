<?php

namespace AccesoSeguro\Infra;



defined('ABSPATH') || exit;



final class Options {

	public const KEY = 'as_settings';



	public static function getAll(): array {

		$opts = get_option(self::KEY, []);

		return is_array($opts) ? $opts : [];

	}



	public static function get(string $path, $default = null) {

		$opts = self::getAll();

		$parts = explode('.', $path);

		$cur = $opts;



		foreach ($parts as $p) {

			if (!is_array($cur) || !array_key_exists($p, $cur)) {

				return $default;

			}

			$cur = $cur[$p];

		}

		return $cur;

	}



	public static function update(array $new): void {

		update_option(self::KEY, $new, false);

	}



	public static function setDefaultsIfEmpty(): void {

		$existing = self::getAll();



		$defaults = [

			'general' => [

				'enabled' => true,

				'public_error_register' => __('No se ha podido completar la solicitud. Revisa los datos e inténtalo de nuevo.', 'acceso-seguro'),

				'public_error_login'    => __('No se ha podido iniciar sesión. Verifica tus datos e inténtalo de nuevo.', 'acceso-seguro'),

				'popup_auto' => false,

				'allow_register' => true,

				'privacy_page_id' => 0, // 0 = usar la página de privacidad de WordPress (Ajustes → Privacidad)



				'rate_limit_enabled' => true,

				'rate_limit_login_window_seconds' => 300,

				'rate_limit_login_max_attempts' => 8,

				'rate_limit_register_window_seconds' => 900,

				'rate_limit_register_max_attempts' => 4,

				'rate_limit_forgot_window_seconds' => 900,

				'rate_limit_forgot_max_attempts' => 4,

				'rate_limit_storage' => 'transient', // transient | db

				'rate_limit_db_table' => 'as_rate_limit', // nombre base (sin prefijo)



				'score_enabled' => true,

				'score_threshold' => 8,



				'progressive_enabled' => true,

				'progressive_steps_minutes' => '5,30,120,1440', // minutos: 5m, 30m, 2h, 24h

			],

			'scoring' => [

				'deny_threshold' => 70,

			],

			'email' => [

				'enabled' => true,

				'block_tlds' => ['xyz','top','zip','mov'],

				'block_domains' => [],

				'check_mx' => true,

				'mx_no_record_points' => 45,

			],

			'username' => [

				'enabled' => true,

				'min_length' => 6,

				'min_vowel_ratio' => 0.25,

				'max_consonant_run' => 6,

				'name_dot_surname_points' => 15,

				'short_points' => 40,

				'low_vowel_points' => 30,

				'consonant_run_points' => 30,



				'suspicious_fragments_enabled' => true,

				'suspicious_fragments_points' => 30,

				'suspicious_fragments' => [

					'www.',

					'http://',

					'https://',

					'blogspot.',

					'TRADE',

					'FOREX',

					'USD',

					'EUR',

					'BTC',

					'ETH',

					'CRYPTO',

					'CASINO',

					'BET',

				],

			],

			'logging' => [

				'enabled' => true,

				'hash_ip' => true,

				'retention_days' => 60,

				'max_ua_len' => 190,

				'max_identifier_len' => 190,

			],

			'ui' => [

				'modal_max_width' => 420,      // px

				'modal_padding'   => 18, 	   // px

				'border_radius'   => 14,       // px

				'overlay_opacity' => 0.55,     // 0-1

				'accent_color'    => '#111111',

				'bg_color'        => '#ffffff',

				'text_color'      => '#111111',

			],

		];



		$merged = self::mergeDefaults($defaults, $existing);

		if ($merged !== $existing) {
			self::update($merged);
		}

	}

	private static function mergeDefaults(array $defaults, array $current): array
	{
		foreach ($defaults as $key => $value) {

			if (!array_key_exists($key, $current)) {
				$current[$key] = $value;
				continue;
			}

			if (is_array($value) && is_array($current[$key])) {
				$current[$key] = self::mergeDefaults($value, $current[$key]);
			}
		}

		return $current;
	}

}
