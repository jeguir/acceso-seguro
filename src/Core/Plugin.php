<?php

namespace AccesoSeguro\Core;



defined('ABSPATH') || exit;



final class Plugin {

	private static ?self $instance = null;



	public static function instance(): self {
syncDefaults(
		return self::$instance ??= new self();

	}



	public function boot(): void {

		load_plugin_textdomain('acceso-seguro', false, basename(dirname(AS_PLUGIN_FILE)) . '/languages');



		// Autorreparación: asegura defaults y tabla de logs aunque no se haya pasado por activate()

		if (class_exists('\AccesoSeguro\Infra\Options')) {

			\AccesoSeguro\Infra\Options::setDefaultsIfEmpty();

		}

		if (class_exists('\AccesoSeguro\Infra\LogRepository')) {

			\AccesoSeguro\Infra\LogRepository::maybeCreateTable();

		}



		if (class_exists('\AccesoSeguro\Integrations\WordPressHooks')) {

			(new \AccesoSeguro\Integrations\WordPressHooks())->register();

		}



		if (class_exists('\WooCommerce') && class_exists('\AccesoSeguro\Integrations\WooCommerceHooks')) {

			(new \AccesoSeguro\Integrations\WooCommerceHooks())->register();

		}



		if (class_exists('\AccesoSeguro\Infra\Cron')) {

			(new \AccesoSeguro\Infra\Cron())->register();

		}



		if (is_admin() && class_exists('\AccesoSeguro\Admin\SettingsPage')) {

			(new \AccesoSeguro\Admin\SettingsPage())->register();

		}



		if (class_exists('\AccesoSeguro\Integrations\AjaxEndpoints')) {

			(new \AccesoSeguro\Integrations\AjaxEndpoints())->register();

		}



		if (class_exists('\AccesoSeguro\Frontend\Shortcodes')) {

			(new \AccesoSeguro\Frontend\Shortcodes())->register();

		}

	}

}

