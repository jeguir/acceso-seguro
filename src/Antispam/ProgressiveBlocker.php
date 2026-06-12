<?php
namespace AccesoSeguro\Antispam;

use AccesoSeguro\Infra\Options;
use AccesoSeguro\Infra\RequestContext;
use AccesoSeguro\Infra\Db;
use AccesoSeguro\Infra\ClientIp;

defined('ABSPATH') || exit;

final class ProgressiveBlocker {

	public static function isEnabled(): bool {
		return (bool) Options::get('general.progressive_enabled', true);
	}

	public static function parseStepsMinutes(): array {
		$val = Options::get('general.progressive_steps_minutes', [5,30,120,1440]);

		// Si ya viene como array (nuevo), lo normalizamos
		if (is_array($val)) {
			$mins = array_map('intval', $val);
			$mins = array_values(array_filter($mins, fn($m) => $m > 0));
			return $mins ?: [5,30,120,1440];
		}

		// Compatibilidad: si viene como string (antiguo)
		$raw = (string) $val;
		$parts = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
		$mins = array_map('intval', $parts);
		$mins = array_values(array_filter($mins, fn($m) => $m > 0));

		return $mins ?: [5,30,120,1440];
	}

	public static function isBlocked(RequestContext $ctx): bool {
		if (!self::isEnabled()) return false;

		$ip = self::ctxIpHash($ctx);
		if ($ip === '') return false;

		global $wpdb;
		$table = Db::tableBlocks();
		$now = time();

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT until_ts FROM {$table} WHERE ip_hash = %s AND action = %s",
				$ip,
				(string) $ctx->action
			),
			ARRAY_A
		);

		if (!$row) return false;
		return ((int) $row['until_ts'] > $now);
	}

	public static function registerStrike(RequestContext $ctx): void {
		if (!self::isEnabled()) return;

		$ip = self::ctxIpHash($ctx);
		if ($ip === '') return;

		global $wpdb;
		$table = Db::tableBlocks();
		$now = time();
		$steps = self::parseStepsMinutes();

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT strikes, until_ts FROM {$table} WHERE ip_hash = %s AND action = %s",
				$ip,
				(string) $ctx->action
			),
			ARRAY_A
		);

		$strikes = $row ? (int) $row['strikes'] : 0;
		$strikes++;

		$idx = min($strikes - 1, count($steps) - 1);
		$blockSeconds = (int) $steps[$idx] * 60;

		$until = $now + $blockSeconds;

		$wpdb->replace(
			$table,
			[
				'ip_hash'  => $ip,
				'action'   => (string) $ctx->action,
				'strikes'  => $strikes,
				'until_ts' => $until,
				'last_ts'  => $now,
			],
			['%s','%s','%d','%d','%d']
		);
	}

	private static function ctxIpHash(RequestContext $ctx): string {
		// Fuente única para todo el plugin
		return ClientIp::hash();
	}
}
