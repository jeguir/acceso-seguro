<?php
namespace AccesoSeguro\Infra;

defined('ABSPATH') || exit;

final class LogRepository {
	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'as_logs';
	}

	public static function maybeCreateTable(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table = self::table();
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			created_at DATETIME NOT NULL,
			action VARCHAR(20) NOT NULL,
			source VARCHAR(30) NOT NULL,
			ip_hash VARCHAR(64) NOT NULL,
			user_agent VARCHAR(200) NOT NULL,
			identifier VARCHAR(200) NOT NULL,
			reason_code VARCHAR(80) NOT NULL,
			score SMALLINT NOT NULL DEFAULT 0,
			meta_json LONGTEXT NULL,
			PRIMARY KEY (id),
			KEY created_at (created_at),
			KEY reason_code (reason_code),
			KEY ip_hash (ip_hash)
		) {$charset};";

		dbDelta($sql);
	}

	public static function insert(array $row): void {
		global $wpdb;

		$ok = $wpdb->insert(self::table(), $row, ['%s','%s','%s','%s','%s','%s','%s','%d','%s']);

		if ($ok === false) {
			error_log('[AccesoSeguro] ERROR insert log: ' . $wpdb->last_error);
			error_log('[AccesoSeguro] Last query: ' . $wpdb->last_query);
		}
	}

	public static function purgeOlderThanDays(int $days): void {
		global $wpdb;
		$days = max(1, $days);
		$table = self::table();
		$wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE created_at < (NOW() - INTERVAL %d DAY)", $days));
	}
}
