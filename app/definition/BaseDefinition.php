<?php

declare(strict_types=1);

namespace App\Definition;

class BaseDefinition {

	static public function getConsts() {
		$classReflection = new \ReflectionClass(get_called_class());
		return $classReflection->getConstants();
	}
}
