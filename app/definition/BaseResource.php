<?php

declare(strict_types=1);

namespace App\Definition;

class BaseResource {

	static public function getColumnNames() {
		$classReflection = new \ReflectionClass(get_called_class());
		return $classReflection->getConstants();
	}
}
