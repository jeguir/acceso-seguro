<?php
namespace AccesoSeguro\Core;

defined('ABSPATH') || exit;

final class Autoloader {
	private static string $baseDir;

	public static function register(string $baseDir): void {
		self::$baseDir = rtrim($baseDir, '/\\');

		spl_autoload_register(function ($class) {
			$prefix = 'AccesoSeguro\\';
			if (strpos($class, $prefix) !== 0) {
				return;
			}
			$relative = substr($class, strlen($prefix));
			$relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
			$file = self::$baseDir . DIRECTORY_SEPARATOR . $relative;

			if (is_readable($file)) {
				require_once $file;
			}
		});
	}
}
