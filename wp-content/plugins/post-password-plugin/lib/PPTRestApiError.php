<?php

namespace PPT;

defined('ABSPATH') || exit;

use JsonSerializable;


class PPTRestApiError implements JsonSerializable {
	public int $code;
	public string $message;
	
	public function __construct(string $message, int $code) {
		$this->code = $code;
		$this->message = $message;
	}
	
	public function jsonSerialize() {
		return (object) [
			'error' => $this->code,
			'message' => $this->message
		];
	}
}