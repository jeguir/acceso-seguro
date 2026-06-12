<?php
namespace AccesoSeguro\Antispam\Signals;

use AccesoSeguro\Infra\RequestContext;
use AccesoSeguro\Infra\Options;

defined('ABSPATH') || exit;

final class EmailSignal {
	public function check(RequestContext $ctx): array {
		$email = $ctx->email ? strtolower(trim($ctx->email)) : '';
		$signals = ['has_email' => ($email !== '')];

		if ($email === '' || !is_email($email)) {
			return ['deny'=>false,'reason_code'=>'','points'=>0,'signals'=>$signals + ['valid'=>false]];
		}
		$signals['valid'] = true;

		$domain = substr(strrchr($email, "@"), 1) ?: '';
		$domain = trim($domain, ". \t\n\r\0\x0B");
		$signals['domain'] = $domain;

		$tld = '';
		if (strpos($domain, '.') !== false) {
			$parts = explode('.', $domain);
			$tld = strtolower(end($parts));
		}
		$signals['tld'] = $tld;

		$blockTlds = array_map('strtolower', (array) Options::get('email.block_tlds', []));
		if ($tld !== '' && in_array($tld, $blockTlds, true)) {
			$scoreEnabled = \AccesoSeguro\Infra\Options::get('general.score_enabled', true);

			if ($scoreEnabled) {
				return [
					'deny' => false,
					'reason_code' => '',
					'points' => 10,
					'hits' => ['EMAIL_TLD_BLOCKED' => 10],
					'signals' => $signals,
				];
			}

			return [
				'deny' => true,
				'reason_code' => 'EMAIL_TLD_BLOCKED',
				'points' => 10,
				'signals' => $signals,
			];
		}

		$blockDomains = array_map('strtolower', (array) Options::get('email.block_domains', []));
		if ($domain !== '' && in_array($domain, $blockDomains, true)) {
			return ['deny'=>true,'reason_code'=>'EMAIL_DOMAIN_BLOCKED','points'=>999,'signals'=>$signals + ['blocked_domain'=>true]];
		}

		$points = 0;
		if ((bool) Options::get('email.check_mx', true) && $domain !== '' && function_exists('checkdnsrr')) {
			$hasMx = checkdnsrr($domain, 'MX');
			$signals['mx'] = $hasMx ? 'ok' : 'missing';
			if (!$hasMx) $points += (int) Options::get('email.mx_no_record_points', 45);
		}

		return ['deny'=>false,'reason_code'=>'','points'=>$points,'signals'=>$signals];
	}
}
