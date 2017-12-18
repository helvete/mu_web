<?php

declare(strict_types=1);

namespace App\Definition\Resource;

use App\Definition\BaseDefinition;

class AccountCharacter extends BaseDefinition {

	const ID = "id";
	const ACCOUNT = "account";
	const CHARACTER_1 = "character_1";
	const CHARACTER_2 = "character_2";
	const CHARACTER_3 = "character_3";
	const CHARACTER_4 = "character_4";
	const CHARACTER_5 = "character_5";
	const CHARACTER_LAST_ONLINE = "character_last_online";

	static public function getColTypes(): array {
		return [
			self::ID => static::TYPE_INT,
			self::ACCOUNT => static::TYPE_STRING,
			self::CHARACTER_1 => static::TYPE_STRING,
			self::CHARACTER_2 => static::TYPE_STRING,
			self::CHARACTER_3 => static::TYPE_STRING,
			self::CHARACTER_4 => static::TYPE_STRING,
			self::CHARACTER_5 => static::TYPE_STRING,
			self::CHARACTER_LAST_ONLINE => static::TYPE_STRING,
		];
	}
}
