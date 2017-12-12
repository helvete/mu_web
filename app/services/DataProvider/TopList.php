<?php

declare(strict_types=1);

namespace App\Services\DataProvider;

use App\Services\MupiClient;
use App\Definition\Resource\Character;

class TopList extends BaseDataProvider {

	public function getData(array $params, $limit = null, $offset = 0) {
		$data = parent::getCharacter(Character::class, $params);
		$ordered = $this->order($data);

		// temporarily handle limit and offset manually
		if (!is_null($limit) && (int)$limit > 0) {
			return array_slice($data, $offset, $limit);
		}
		return $readyToPrint;
	}

	// override for toplist to sort by reset, level
	protected function order(array $itemList, $column = null) {
		usort($itemList, function($a, $b) {
			foreach (['a', 'b'] as $char) {
				$varName = "{$char}AbsExp";
				$$varName = ((int)$$char[Character::RESET]) * 350;
				$$varName += (int)$$char[Character::LEVEL];
			}
			if ($aAbsExp == $bAbsExp) {
				return 0;
			}
			return ($aAbsExp < $bAbsExp) ? static::TOP : static::BOTTOM;
		});
		return $itemList;
	}

	// filter GMs out
	protected function filterItemOut($itemData) {
		return $itemData[Character::CHARACTER_LEVEL_CODE] > 7;
	}

	static public function resultColumns() {
		return [
			Character::NAME,
			Character::ACCOUNT,
			Character::LEVEL,
			Character::ID_CLASS,
			Character::STRENGTH,
			Character::DEXTERITY,
			Character::VITALITY,
			Character::ENERGY,
			Character::RESET,
		];
	}
}
