<?php

declare(strict_types=1);

namespace App\Definition\Resource;

use App\Definition\BaseDefinition;

class Character extends BaseDefinition {

	const ACCOUNT = 'account';
	const NAME = 'name';
	const LEVEL = 'level';
	const LEVEL_UP_POINT = 'level_up_point';
	const ID_CLASS = 'id_class';
	const EXPERIENCE = 'experience';
	const STRENGTH = 'strength';
	const DEXTERITY = 'dexterity';
	const VITALITY = 'vitality';
	const ENERGY = 'energy';
	const INVENTORY = 'inventory';
	const MAGIC_LIST = 'magic_list';
	const MONEY = 'money';
	const LIFE = 'life';
	const MAX_LIFE = 'max_life';
	const MANA = 'mana';
	const MAX_MANA = 'max_mana';
	const ID_MAP = 'id_map';
	const MAP_POSITION_X = 'map_position_x';
	const MAP_POSITION_Y = 'map_position_y';
	const ID_DIRECTION = 'id_direction';
	const PK_COUNT = 'pk_count';
	const PK_LEVEL = 'pk_level';
	const PK_TIME = 'pk_time';
	const M_DATETIME = 'm_datetime';
	const L_DATETIME = 'l_datetime';
	const CHARACTER_LEVEL_CODE = 'character_level_code';
	const DB_VERSION = 'db_version';
	const QUEST = 'quest';
	const RESET = 'reset';

	static public function getColTypes() {
		return [
			self::ACCOUNT => static::TYPE_STRING,
			self::NAME => static::TYPE_STRING,
			self::LEVEL => static::TYPE_INT,
			self::LEVEL_UP_POINT => static::TYPE_INT,
			self::ID_CLASS => static::TYPE_INT,
			self::EXPERIENCE => static::TYPE_INT,
			self::STRENGTH => static::TYPE_INT,
			self::DEXTERITY => static::TYPE_INT,
			self::VITALITY => static::TYPE_INT,
			self::ENERGY => static::TYPE_INT,
			self::INVENTORY => static::TYPE_HEXBIN,
			self::MAGIC_LIST => static::TYPE_HEXBIN,
			self::MONEY => static::TYPE_INT,
			self::LIFE => static::TYPE_INT,
			self::MAX_LIFE => static::TYPE_INT,
			self::MANA => static::TYPE_FLOAT,
			self::MAX_MANA => static::TYPE_FLOAT,
			self::ID_MAP => static::TYPE_INT,
			self::MAP_POSITION_X => static::TYPE_INT,
			self::MAP_POSITION_Y => static::TYPE_INT,
			self::ID_DIRECTION => static::TYPE_INT,
			self::PK_COUNT => static::TYPE_INT,
			self::PK_LEVEL => static::TYPE_INT,
			self::PK_TIME => static::TYPE_INT,
			self::M_DATETIME => static::TYPE_DATETIME,
			self::L_DATETIME => static::TYPE_DATETIME,
			self::CHARACTER_LEVEL_CODE => static::TYPE_INT,
			self::DB_VERSION => static::TYPE_INT,
			self::QUEST => static::TYPE_HEXBIN,
			self::RESET => static::TYPE_INT,
		];
	}
}
