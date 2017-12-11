<?php

declare(strict_types=1);

namespace App\Services\DataProvider;

use App\Services\MupiClient;

class TopList {

	protected $mupiClient;

	public function __construct(MupiClient $mupiClient) {
		$this->mupiClient = $mupiClient;
	}

	// TODO: test method
	public function getCharacter(array $params = []) {
		return $this->mupiClient->getCharacter($params);
	}
}
