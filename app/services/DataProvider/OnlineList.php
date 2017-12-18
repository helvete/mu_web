<?php

declare(strict_types=1);

namespace App\Services\DataProvider;

use App\Services\MupiClient;
use App\Definition\Resource\Character;
use App\Definition\Resource\AccountCharacter;
use App\Definition\Resource\AccountProperties;

class OnlineList extends BaseDataProvider {

	const TOP = -1;
	const BOTTOM = 1;

	public function getData(array $params, $limit = null, $offset = 0): array {
		$params[MupiClient::KEY_FILTER] = [AccountProperties::IS_ONLINE => 1];
		$on = parent::getAccountProperties(AccountProperties::class, $params);
		if (!count($on)) {
			return [];
		}
		unset($params);
		foreach ($on as $accData) {
			$params[MupiClient::KEY_FILTER][AccountCharacter::ACCOUNT][]
				= $accData[AccountProperties::ACCOUNT];
		}
		$char = parent::getAccountCharacter(AccountCharacter::class, $params);
		unset($params);
		foreach ($char as $charData) {
			$params[MupiClient::KEY_FILTER][Character::NAME][]
				= $charData[AccountCharacter::CHARACTER_LAST_ONLINE];
		}
		$data = parent::getCharacter(Character::class, $params);
		$data = $this->order($data, Character::RESET);//TODO: debug

		// temporarily handle limit and offset manually
		if (!is_null($limit) && (int)$limit > 0) {
			return array_slice($data, $offset, $limit);
		}
		return $data;
	}

	static public function resultColumns() {
		return [
			AccountProperties::ACCOUNT,
			AccountCharacter::ACCOUNT,
			AccountCharacter::CHARACTER_LAST_ONLINE,
			Character::NAME,
			Character::LEVEL,
			Character::RESET,
			Character::ID_CLASS,
			Character::ID_MAP,
		];
	}
}
