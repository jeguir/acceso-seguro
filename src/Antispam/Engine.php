<?php
namespace AccesoSeguro\Antispam;

use AccesoSeguro\Infra\RequestContext;
use AccesoSeguro\Infra\Options;
use AccesoSeguro\Antispam\Signals\EmailSignal;
use AccesoSeguro\Antispam\Signals\UsernameSignal;

defined('ABSPATH') || exit;

final class Engine {
	public function evaluate(RequestContext $ctx): Result {
		if (!Options::get('general.enabled', true)) {
			return new Result(true, 0, 'ALLOW_DISABLED', $this->publicMessage($ctx));
		}

		// Bloqueo progresivo por reincidencia
		if (\AccesoSeguro\Antispam\ProgressiveBlocker::isBlocked($ctx)) {
			return new Result(false, 999, 'PROGRESSIVE_BLOCK', $this->publicMessage($ctx));
		}

		// Rate limit (bloqueo por demasiados intentos)
		if (!\AccesoSeguro\Antispam\RateLimiter::checkAndIncrement($ctx)) {
			\AccesoSeguro\Antispam\ProgressiveBlocker::registerStrike($ctx);
			return new Result(false, 999, 'RATE_LIMIT', $this->publicMessage($ctx));
		}

		$scoreEnabled = Options::get('general.score_enabled', true);
		$dynThreshold = max(1, (int) Options::get('general.score_threshold', 8));

		$totalScore = 0;
		$scoreHits  = []; // lista de reason_code que suman

		$signals = [];
		$score = 0;
		$denyReason = '';

		if (Options::get('email.enabled', true)) {
			$r = (new EmailSignal())->check($ctx);
			$signals['email'] = $r['signals'] ?? [];
			$score += (int) ($r['points'] ?? 0);

			// NUEVO: sumar hits al score dinámico
			if (!empty($r['hits']) && is_array($r['hits'])) {
				foreach ($r['hits'] as $code => $pts) {
					$totalScore += (int) $pts;
					$scoreHits[] = (string) $code;
				}
			}

			if (!empty($r['deny'])) {
				$denyReason = !empty($r['reason_code']) ? $r['reason_code'] : 'EMAIL_DENY';
			}
		}

			if (Options::get('username.enabled', true)) {
			// Si el login viene por email, el "username candidato" no es fiable para scoring.
			// Evitamos falsos positivos en login por email.
			if (!($ctx->action === 'login' && !empty($ctx->email))) {
				$r = (new UsernameSignal())->check($ctx);
				$signals['username'] = $r['signals'];
				$score += (int) $r['points'];

				if (!empty($r['hits'])) {
					foreach ((array) $r['hits'] as $k => $v) {
						$totalScore += (int) $v;
						$scoreHits[(string)$k] = ($scoreHits[(string)$k] ?? 0) + (int)$v;
					}
				}

				if (!empty($r['deny']) && $denyReason === '') $denyReason = $r['reason_code'] ?: 'USERNAME_DENY';
			}
		}

		$pointsThreshold = (int) Options::get('scoring.deny_threshold', 70);

		if ($denyReason !== '') {
			\AccesoSeguro\Antispam\ProgressiveBlocker::registerStrike($ctx);
			return new Result(false, $score, $denyReason, $this->publicMessage($ctx), $signals);
		}
		if ($score >= $pointsThreshold) {
			\AccesoSeguro\Antispam\ProgressiveBlocker::registerStrike($ctx);
			return new Result(false, $score, 'SCORE_THRESHOLD', $this->publicMessage($ctx), $signals);
		}
		if ($scoreEnabled && $totalScore >= $dynThreshold) {
			// Bloqueo por umbral alcanzado (sin pistas)
			\AccesoSeguro\Antispam\ProgressiveBlocker::registerStrike($ctx);
			return new Result(false, $totalScore, 'SCORE_THRESHOLD', $this->publicMessage($ctx));
		}
		return new Result(true, $score, 'ALLOW', $this->publicMessage($ctx), $signals);
	}

	private function publicMessage(RequestContext $ctx): string {
		if ($ctx->action === 'login') {
			return (string) Options::get('general.public_error_login', 'No se ha podido iniciar sesión.');
		}
		return (string) Options::get('general.public_error_register', 'No se ha podido completar la solicitud.');
	}
}
