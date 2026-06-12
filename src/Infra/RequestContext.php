<?php
namespace AccesoSeguro\Infra;
use AccesoSeguro\Infra\ClientIp;

defined('ABSPATH') || exit;

final class RequestContext {
	public string $action;   // register|login
	public ?string $username;
	public ?string $email;
	public string $ip;
	public string $userAgent;
	public string $source;   // wp|woocommerce|ajax|rest|unknown
	public int $ts;
	public array $extras;

	public function __construct(string $action, ?string $username, ?string $email, string $source = 'unknown', array $extras = []) {
		$this->action = $action;
		$this->username = $username !== null ? trim($username) : null;
		$this->email = $email !== null ? trim($email) : null;
		$this->source = $source;
		$this->extras = $extras;
		$this->ts = time();
		$this->ip = ClientIp::get();
		$this->userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
	}

    public static function usernameCandidateFromEmail(?string $email): ?string {
		$email = $email ? trim(strtolower($email)) : '';
		if ($email === '' || !is_email($email)) {
			return null;
		}

		$local = strstr($email, '@', true);
		if ($local === false || $local === '') {
			return null;
		}

		// Normalizamos para que encaje con las reglas de WP (solo “user safe”)
		$candidate = sanitize_user($local, true);
		$candidate = trim($candidate);

		return $candidate !== '' ? $candidate : null;
	}

}
