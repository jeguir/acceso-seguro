<?php
namespace AccesoSeguro\Infra;

defined('ABSPATH') || exit;

final class Cron {
	private const HOOK = 'as_cron_purge_logs';

	public function register(): void {
		add_action(self::HOOK, [self::class, 'purge']);

		if (!wp_next_scheduled(self::HOOK)) {
			wp_schedule_event(time() + 300, 'daily', self::HOOK);
		}
	}

	public static function purge(): void {
		$days = (int) Options::get('logging.retention_days', 60);
		LogRepository::purgeOlderThanDays($days);
	}
}
