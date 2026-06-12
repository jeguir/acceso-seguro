<?php
namespace AccesoSeguro\Infra;

use AccesoSeguro\Antispam\Result;

defined('ABSPATH') || exit;

final class Logger {

	public static function logDenied(RequestContext $ctx, Result $result): void {
		// Si aún no hay opciones, no rompemos
		if (class_exists('\AccesoSeguro\Infra\Options') && !Options::get('logging.enabled', true)) {
			return;
		}

		$hashIp = true;
		$maxUa  = 190;
		$maxId  = 190;

		if (class_exists('\AccesoSeguro\Infra\Options')) {
			$hashIp = (bool) Options::get('logging.hash_ip', true);
			$maxUa  = (int) Options::get('logging.max_ua_len', 190);
			$maxId  = (int) Options::get('logging.max_identifier_len', 190);
		}

		$ip = $ctx->ip;
		$ipHash = $hashIp ? hash('sha256', $ip . '|' . wp_salt('auth')) : $ip;

		$ua = mb_substr($ctx->userAgent, 0, $maxUa);

		$identifier = self::identifierHash($ctx);
		$identifier = mb_substr($identifier, 0, $maxId);

		if (!class_exists('\AccesoSeguro\Infra\LogRepository')) {
			return;
		}

		LogRepository::insert([
			'created_at'  => gmdate('Y-m-d H:i:s'),
			'action'      => $ctx->action,
			'source'      => $ctx->source,
			'ip_hash'     => $ipHash,
			'user_agent'  => $ua,
			'identifier'  => $identifier,
			'reason_code' => $result->reasonCode,
			'score'       => $result->score,
			'meta_json'   => wp_json_encode(['signals' => $result->signals], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
		]);
	}

	private static function identifierHash(RequestContext $ctx): string {
		$u = $ctx->username ? strtolower($ctx->username) : '';
		$e = $ctx->email ? strtolower($ctx->email) : '';
		$raw = trim($u . '|' . $e);
		if ($raw === '|') $raw = 'unknown';
		return hash('sha256', $raw . '|' . wp_salt('secure_auth'));
	}
}
