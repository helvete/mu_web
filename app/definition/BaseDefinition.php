<?php

declare(strict_types=1);

namespace App\Definition;

class BaseDefinition {

	const TYPE_STRING = 'string';
	const TYPE_INT = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_BOOL = 'boolean';
	const TYPE_HEXBIN = 'binary_in_hexadecimal';
	const TYPE_DATETIME = 'datetime';

	static public function getConsts() {
		$classReflection = new \ReflectionClass(get_called_class());
		return $classReflection->getConstants();
	}

	static public function forceType($value, $type) {
		if (!in_array($type, self::getConsts())) {
			throw new \InvalidArgumentException("Unknown data type '{$type}'!");
		}
		switch ($type) {
		case self::TYPE_FLOAT:
			return (float)$value;
		case self::TYPE_INT:
			return (int)$value;
		case self::TYPE_DATETIME:
			return new \DateTime($value);
		case self::TYPE_BOOL:
			return (bool)$value;
		case self::TYPE_HEXBIN:
		case self::TYPE_STRING:
			return (string)$value;
		}
	}
}
