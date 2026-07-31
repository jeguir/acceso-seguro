<?php

/**

 * Plugin Name: Acceso Seguro – Control de acceso y registro avanzado

 * Description: Sistema avanzado de acceso y registro para WordPress y WooCommerce, con protección antispam basada en heurísticas reales. Compatible con áreas privadas y contenidos premium.

 * Version: 1.1.0

 * Author: Jesús Guirao

 * Author URI: https://jesusguirao.com/

 * Text Domain: acceso-seguro

 * Domain Path: /languages

 * Requires at least: 6.0

 * Requires PHP: 8.0

 */



defined('ABSPATH') || exit;



define('AS_VERSION', '1.1.0');

define('AS_PLUGIN_FILE', __FILE__);

define('AS_PLUGIN_DIR', plugin_dir_path(__FILE__));

define('AS_PLUGIN_URL', plugin_dir_url(__FILE__));



require_once AS_PLUGIN_DIR . 'src/Core/Autoloader.php';



add_action('init', function () {

	load_plugin_textdomain(

		'acceso-seguro',

		false,

		dirname(plugin_basename(__FILE__)) . '/languages'

	);

});



add_action('plugins_loaded', function () {

	\AccesoSeguro\Core\Autoloader::register(AS_PLUGIN_DIR . 'src');

	\AccesoSeguro\Core\Plugin::instance()->boot();

});



register_activation_hook(__FILE__, function () {

	require_once AS_PLUGIN_DIR . 'src/Core/Activator.php';

	\AccesoSeguro\Core\Activator::activate();



	// Cargar Db en activación (no dependemos del autoload)

	$dbFile = AS_PLUGIN_DIR . 'src/Infra/Db.php';

	if (file_exists($dbFile)) {

		require_once $dbFile;

	}



	if (class_exists('\AccesoSeguro\Infra\Db')) {

		\AccesoSeguro\Infra\Db::maybeCreateTables();

	}

});



add_action('wp_head', function () {

	?>

	<script>

		window.AS_AUTH = window.AS_AUTH || {};

		window.AS_AUTH.nonce = <?php echo wp_json_encode(wp_create_nonce('as_auth')); ?>;

		window.AS_AUTH.ajaxUrl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;

	</script>

	<?php

}, 1);



add_action('wp_enqueue_scripts', function () {

	if (is_user_logged_in()) {

		return;

	}



	$shouldLoad = (defined('AS_POPUP_ENABLED') && AS_POPUP_ENABLED);



	// También cargar si está activada la auto-inyección desde Ajustes

	if (class_exists('\AccesoSeguro\Infra\Options')) {

		$shouldLoad = $shouldLoad || \AccesoSeguro\Infra\Options::get('general.popup_auto', false);

	}



	if ($shouldLoad) {

		wp_enqueue_style(

			'as-auth',

			plugins_url('assets/auth.css', __FILE__),

			[],

			AS_VERSION

		);



		wp_enqueue_script(

			'as-auth',

			plugins_url('assets/auth.js', __FILE__),

			[],

			AS_VERSION,

			true

		);



		// Variables CSS del modal (configurables desde Ajustes)

		if (class_exists('\AccesoSeguro\Infra\Options')) {

			$opts = \AccesoSeguro\Infra\Options::getAll();

			$ui   = $opts['ui'] ?? [];



			$maxW = (int)($ui['modal_max_width'] ?? 420);

			$pad  = (int)($ui['modal_padding'] ?? 18);

			$rad  = (int)($ui['border_radius'] ?? 14);

			$op   = (float)($ui['overlay_opacity'] ?? 0.55);



			$accent = (string)($ui['accent_color'] ?? '#111111');

			$bg     = (string)($ui['bg_color'] ?? '#ffffff');

			$text   = (string)($ui['text_color'] ?? '#111111');

			$titleColor = (string)($ui['title_color'] ?? '#111111');

			$inputBg    = (string)($ui['input_bg'] ?? '#ffffff');

			$inputBdCol = (string)($ui['input_border_color'] ?? '#dddddd');

			$inputBdW   = (int)($ui['input_border_width'] ?? 1);



			$cssVars = ":root{

				--as-modal-max-width: {$maxW}px;

				--as-modal-padding: {$pad}px;

				--as-modal-radius: {$rad}px;

				--as-overlay-opacity: {$op};

				--as-accent: {$accent};

				--as-bg: {$bg};

				--as-text: {$text};

				--as-title-color: {$titleColor};

				--as-input-bg: {$inputBg};

				--as-input-border-color: {$inputBdCol};

				--as-input-border-width: {$inputBdW}px;

			}";



			wp_add_inline_style('as-auth', $cssVars);

		}



		wp_localize_script(

			'as-auth',

			'AS_AUTH_TEXTS',

			[

				'login_error'    => __('No se ha podido iniciar sesión.', 'acceso-seguro'),

				'register_error' => __('No se ha podido completar la solicitud.', 'acceso-seguro'),

				'network_error'  => __('Error de red. Inténtalo de nuevo.', 'acceso-seguro'),

				'forgot_generic' => __('Si existe una cuenta con ese email, recibirás un enlace para restablecer la contraseña.', 'acceso-seguro'),

			]

		);

	}

});



add_action('wp_footer', function () {

	if (!class_exists('\AccesoSeguro\Infra\Options')) {

		return;

	}



	$auto = \AccesoSeguro\Infra\Options::get('general.popup_auto', false);

	if (!$auto) {

		return;

	}



	if (is_user_logged_in()) {

		return;

	}



	// Marcamos que el popup se usa para cargar assets

	if (!defined('AS_POPUP_ENABLED')) {

		define('AS_POPUP_ENABLED', true);

	}



	// Renderizamos el HTML del popup sin necesidad de shortcode

	if (class_exists('\AccesoSeguro\Frontend\Shortcodes')) {

		echo (new \AccesoSeguro\Frontend\Shortcodes())->renderPopup();

	}

}, 99);
