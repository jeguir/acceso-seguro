<?php
namespace AccesoSeguro\Infra;

defined('ABSPATH') || exit;

final class ClientIp {

	public static function get(): string {
		$ip = '';

		// Cloudflare
		if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = (string) $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		// Proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$parts = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip = trim((string) ($parts[0] ?? ''));
		}
		// Fallback
		elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = (string) $_SERVER['REMOTE_ADDR'];
		}

		$ip = trim($ip);

		// Normalización básica (IPv6 loopback etc.)
		if ($ip === '::1') $ip = '127.0.0.1';

		return $ip;
	}

    public static function hash(): string {
        $ip = self::get();
        if ($ip === '') return '';

        // Debe coincidir con Logger: sha256(ip|wp_salt('auth'))
        return hash('sha256', $ip . '|' . wp_salt('auth'));
    }
}
