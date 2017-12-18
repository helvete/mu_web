<?php

declare(strict_types=1);

namespace App\Definition\Resource;

use App\Definition\BaseDefinition;

class AccountProperties extends BaseDefinition {

	const ACCOUNT = "account";
	const IS_ONLINE = "is_online";
	const SERVER_NAME = "server_name";
	const IP_ADDRESS = "ip_address";
	const CONNECT_TIME = "connect_time";
	const DISCONNECT_TIME = "disconnect_time";

	static public function getColTypes(): array {
		return [
			self::ACCOUNT => static::TYPE_STRING,
			self::IS_ONLINE => static::TYPE_BOOL,
			self::SERVER_NAME => static::TYPE_STRING,
			self::IP_ADDRESS => static::TYPE_STRING,
			self::CONNECT_TIME => static::TYPE_DATETIME,
			self::DISCONNECT_TIME => static::TYPE_DATETIME,
		];
	}
}
