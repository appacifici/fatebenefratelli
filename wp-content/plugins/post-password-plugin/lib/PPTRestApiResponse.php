<?php

namespace PPT;

defined('ABSPATH') || exit;

use JsonSerializable;


class PPTRestApiResponse implements JsonSerializable {
	public string $shortUrl;
	public string $prettyUrl;

	public function __construct(string $shortUrl, string $prettyUrl) {
		$this->shortUrl = $shortUrl;
		$this->prettyUrl = $prettyUrl;
	}
	
	public function jsonSerialize() {
		return (object) [
			'shortUrl' => $this->shortUrl,
			'prettyUrl' => $this->prettyUrl
		];
	}
}