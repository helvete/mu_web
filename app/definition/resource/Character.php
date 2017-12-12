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
}
