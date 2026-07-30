<?php

namespace AccesoSeguro\Core;



defined('ABSPATH') || exit;



final class Activator {

	public static function activate(): void {


syncDefaults(
		// Si aún no existen estas clases (porque estamos construyendo por fases),

		// no reventamos la activación.

		if (class_exists('\AccesoSeguro\Infra\LogRepository')) {

			\AccesoSeguro\Infra\LogRepository::maybeCreateTable();

		}



		if (class_exists('\AccesoSeguro\Infra\Options')) {

			\AccesoSeguro\Infra\Options::setDefaultsIfEmpty();

		}

	}

}

