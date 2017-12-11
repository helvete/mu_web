<?php

declare(strict_types=1);

namespace App\Services\DataProvider;

use App\Services\MupiClient;
use App\Definition\Resource\Character;

class TopList {

	const TOP = 1;
	const BOTTOM = -1;

	protected $mupiClient;

	public function __construct(MupiClient $mupiClient) {
		$this->mupiClient = $mupiClient;
	}

	public function getData(array $params = [], $limit = null, $offset = 0) {
		$rawData = $this->mupiClient->getCharacter($params);
		$saneData = $this->handleValidResponse($rawData);
		$ordered = $this->reorderByAbsExp($saneData);
		$readyToPrint = $this->getRelevantData($ordered, $limit, $offset);

		return $readyToPrint;
	}

	protected function handleValidResponse(array $rawData) {
		$responseCode = (int)key($rawData);
		if ($responseCode > 0 && $responseCode < 400) {
			return $rawData[$responseCode];
		}
		throw new \RuntimeException('Error occurred while retrieving data');
	}

	protected function reorderByAbsExp(array $characterList) {
		usort($characterList, function($a, $b) {
			foreach (['a', 'b'] as $char) {
				$varName = "{$char}AbsExp";
				$$varName = ((int)$$char[Character::RESET]) * 350;
				$$varName += (int)$$char[Character::LEVEL];
			}
			if ($aAbsExp == $bAbsExp) {
				return 0;
			}
			return ($aAbsExp < $bAbsExp) ? self::TOP : self::BOTTOM;
		});
		return $characterList;
	}

	protected function getRelevantData(array $orderedList, $limit, $offset) {
		$filtered = [];
		$topListCols = self::getTopListColumns();
		foreach ($orderedList as $characterData) {
			if ($characterData[Character::CHARACTER_LEVEL_CODE] > 7) {
				continue;
			}
			$relevantColData = [];
			foreach ($characterData as $colName => $colData) {
				if (in_array($colName, $topListCols)) {
					// TODO: handle column data types @ definition level
					if ($colName === Character::RESET) {
						$relevantColData[$colName] = (int)$colData;
					} else {
						$relevantColData[$colName] = $colData;
					}
				}
			}
			$filtered[] = $relevantColData;
		}
		// temporarily handle limit and offset manually
		if (!is_null($limit) && (int)$limit > 0) {
			return array_slice($filtered, $offset, $limit);
		}
		return $filtered;
	}

	static public function getTopListColumns() {
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
