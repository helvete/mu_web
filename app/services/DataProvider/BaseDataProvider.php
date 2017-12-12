<?php

declare(strict_types=1);

namespace App\Services\DataProvider;

use App\Services\MupiClient;

class BaseDataProvider {

	const TOP = 1;
	const BOTTOM = -1;

	protected $mupiClient;

	public function __construct(MupiClient $mupiClient) {
		$this->mupiClient = $mupiClient;
	}

	public function __call($name, $params) {
		return $this->retrieve(...array_merge([$name], $params));
	}

	protected function retrieve($methodName, $defClass, array $params = []) {
		$rawData = $this->mupiClient->{$methodName}($params);
		$valid = $this->handleValidResponse($rawData, $defClass);
		return $this->filterResultColumns($valid);
	}

	protected function handleValidResponse(array $rawData, $defClass) {
		$responseCode = (int)key($rawData);
		if ($responseCode > 0 && $responseCode < 400) {
			return static::retype($rawData[$responseCode], $defClass);
		}
		throw new \RuntimeException('Error occurred while retrieving data');
	}

	// order alphabetically or numerically by default
	protected function order(array $itemsList, $colName) {
		usort($itemsList, function($a, $b) use ($colName) {
			foreach (['a', 'b'] as $char) {
				$varName = "{$char}Eval";
				$$varName = $$char[$colName];
			}
			if ($aEval === $bEval) {
				return 0;
			}
			return ($aEval < $bEval) ? static::BOTTOM : static::TOP;
		});
		return $itemsList;
	}

	protected function filterResultColumns(array $itemsList) {
		$filtered = [];
		foreach ($itemsList as $itemData) {
			if ($this->filterItemOut($itemData)) {
				continue;
			}
			$relevantColData = [];
			foreach ($itemData as $colName => $colData) {
				if (in_array($colName, static::resultColumns())) {
					$relevantColData[$colName]
						= $this->customProcess($colName, $colData);
				}
			}
			$filtered[] = $relevantColData;
		}
		return $filtered;
	}

	// override to explicitly drop some of records
	protected function filterItemOut($itemData) {
		return false;
	}

	// override to somehow manipulate column value
	protected function customProcess($colName, $colData) {
		return $colData;
	}

	static public function getResultColumns() {
		throw ErrorException('Fatal error: column definition is missing!');
	}

	static protected function retype($itemsList, $defClass) {
		$rsrcColTypes = $defClass::getColTypes();
		foreach ($itemsList as $i => $item) {
			foreach ($item as $colName => $value) {
				if (!isset($rsrcColTypes[$colName])) {
					continue;
				}
				$type = $rsrcColTypes[$colName];
				$item[$colName] = $defClass::forceType($value, $type);
			}
			$itemsList[$i] = $item;
		}
		return $itemsList;
	}
}
