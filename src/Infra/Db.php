<?php
namespace AccesoSeguro\Infra;

defined('ABSPATH') || exit;

final class Db {

	public static function tableRateLimit(): string {
		global $wpdb;
		return $wpdb->prefix . 'as_rate_limit';
	}

	public static function maybeCreateTables(): void {
		global $wpdb;

		$table = self::tableRateLimit();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			rl_key varchar(64) NOT NULL,
			start_ts int(11) NOT NULL,
			count int(11) NOT NULL,
			expires_ts int(11) NOT NULL,
			PRIMARY KEY  (rl_key),
			KEY expires_ts (expires_ts)
		) {$charset};";

		dbDelta($sql);

		$tableB = self::tableBlocks();

		$sqlB = "CREATE TABLE {$tableB} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			ip_hash varchar(64) NOT NULL,
			action varchar(32) NOT NULL,
			strikes int(11) NOT NULL DEFAULT 0,
			until_ts int(11) NOT NULL DEFAULT 0,
			last_ts int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			UNIQUE KEY ip_action (ip_hash, action),
			KEY until_ts (until_ts)
		) {$charset};";

		dbDelta($sqlB);
	}

	public static function purgeExpiredRateLimits(): void {
		global $wpdb;
		$table = self::tableRateLimit();
		$now = time();
		$wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE expires_ts < %d", $now));
	}

	public static function tableBlocks(): string {
		global $wpdb;
		return $wpdb->prefix . 'as_blocks';
	}
}
