<?php

namespace PPT;

defined('ABSPATH') || exit;


class PPTToken {
	protected string $hashAlgorithm;
	protected string $salt;

	protected string $value;

	public function __construct(string $hashAlgorithm, string $salt, string $value) {	
		$this->hashAlgorithm = $hashAlgorithm;
		$this->salt = $salt;
		$this->value = $value;
	}

	public function hash(string $content): string {
		return hash($this->hashAlgorithm, $this->salt . $content);
	}

	public function get(): string {
		return $this->hash($this->value);
	}

	public function __toString() {
		return $this->get();	
	}
}