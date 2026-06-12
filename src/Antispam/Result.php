<?php
namespace AccesoSeguro\Antispam;

defined('ABSPATH') || exit;

final class Result {
	public bool $allowed;
	public int $score;
	public string $reasonCode;
	public string $publicMessage;
	public array $signals;

	public function __construct(bool $allowed, int $score, string $reasonCode, string $publicMessage, array $signals = []) {
		$this->allowed = $allowed;
		$this->score = $score;
		$this->reasonCode = $reasonCode;
		$this->publicMessage = $publicMessage;
		$this->signals = $signals;
	}
}
