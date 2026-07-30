<?php

namespace AccesoSeguro\Antispam\Signals;



use AccesoSeguro\Infra\RequestContext;

use AccesoSeguro\Infra\Options;



defined('ABSPATH') || exit;



final class UsernameSignal {

	public function check(RequestContext $ctx): array {

		$username = $ctx->username ? strtolower(trim($ctx->username)) : '';

		$signals = ['has_username' => ($username !== ''), 'username' => $username];



		if ($username === '') {

			return ['deny'=>false,'reason_code'=>'','points'=>0,'hits'=>[],'signals'=>$signals];

		}



		$points = 0;



		$hits = [];

		$scoreEnabled = Options::get('general.score_enabled', true);



		$len = mb_strlen($username);

		$signals['length'] = $len;

		$minLen = (int) Options::get('username.min_length', 6);

		if ($len < $minLen) {

			$pts = (int) Options::get('username.short_points', 40);

			$points += $pts;

			$signals['too_short'] = true;



			if ($scoreEnabled) {

				$hits['USERNAME_TOO_SHORT'] = ($hits['USERNAME_TOO_SHORT'] ?? 0) + $pts;

			}

		}



		$letters = preg_replace('/[^a-z]/', '', $username);

		$lettersLen = strlen($letters);

		if ($lettersLen > 0) {

			preg_match_all('/[aeiou]/', $letters, $m);

			$vowels = count($m[0]);

			$ratio = $vowels / $lettersLen;

			$signals['vowel_ratio'] = round($ratio, 3);



			$minRatio = (float) Options::get('username.min_vowel_ratio', 0.25);

			if ($ratio < $minRatio) {

				$pts = (int) Options::get('username.low_vowel_points', 30);

				$points += $pts;

				$signals['low_vowel_ratio'] = true;



				if ($scoreEnabled) {

					$hits['USERNAME_LOW_VOWEL_RATIO'] = ($hits['USERNAME_LOW_VOWEL_RATIO'] ?? 0) + $pts;

				}

			}

		}



		preg_match_all('/[bcdfghjklmnpqrstvwxyz]+/', $username, $runs);

		$maxRun = 0;

		foreach ($runs[0] as $run) $maxRun = max($maxRun, strlen($run));

		$signals['max_consonant_run'] = $maxRun;



		$maxAllowed = (int) Options::get('username.max_consonant_run', 6);

		if ($maxRun >= $maxAllowed) {

			$pts = (int) Options::get('username.consonant_run_points', 30);

			$points += $pts;

			$signals['long_consonant_run'] = true;



			if ($scoreEnabled) {

				$hits['USERNAME_LONG_CONSONANT_RUN'] = ($hits['USERNAME_LONG_CONSONANT_RUN'] ?? 0) + $pts;

			}

		}



		if (preg_match('/^[a-z]{3,}\.[a-z]{3,}(\d{0,3})?$/', $username)) {

			$pts = (int) Options::get('username.name_dot_surname_points', 15);

			$points += $pts;

			$signals['name_dot_surname'] = true;



			if ($scoreEnabled) {

				$hits['USERNAME_NAME_DOT_SURNAME'] = ($hits['USERNAME_NAME_DOT_SURNAME'] ?? 0) + $pts;

			}

		}

		// Fragmentos sospechosos
		if (Options::get('username.suspicious_fragments_enabled', true)) {

			$fragments = (array) Options::get('username.suspicious_fragments', []);

			foreach ($fragments as $fragment) {

				$fragment = trim((string) $fragment);

				if ($fragment === '') {
					continue;
				}

				if (stripos($username, $fragment) !== false) {

					$pts = (int) Options::get('username.suspicious_fragments_points', 30);

					$points += $pts;
					$signals['suspicious_fragment'] = $fragment;

					if ($scoreEnabled) {
						$hits['USERNAME_SUSPICIOUS_FRAGMENT'] =
							($hits['USERNAME_SUSPICIOUS_FRAGMENT'] ?? 0) + $pts;
					}

					// Solo puntúa una vez aunque haya varias coincidencias.
					break;
				}
			}
		}



		return ['deny'=>false,'reason_code'=>'','points'=>$points,'hits'=>$hits,'signals'=>$signals];

	}

}

