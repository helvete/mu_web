<?php

declare(strict_types=1);

namespace App\Definition\Resource;

use App\Definition\BaseDefinition;

class Account extends BaseDefinition {

	const MEMB_GUID = "memb_guid";
	const ACCOUNT = "account";
	const PASSWORD = "password";
	const NAME = "name";
	const SNO_NUMB = "sno_numb";
	const POST_CODE = "post_code";
	const ADDR_INFO = "addr_info";
	const ADDR_DETA = "addr_deta";
	const TEL_NUMB = "tel_numb";
	const PHON_NUMB = "phon_numb";
	const MAIL_ADDR = "mail_addr";
	const FPAS_QUES = "fpas_ques";
	const FPAS_ANSW = "fpas_answ";
	const JOB_CODE = "job_code";
	const REGISTRATION_DATETIME = "registration_datetime";
	const MODI_DAYS = "modi_days";
	const OUT_DAYS = "out_days";
	const TRUE_DAYS = "true_days";
	const MAIL_CHEK = "mail_chek";
	const BLOC_CODE = "bloc_code";
	const CTL1_CODE = "ctl1_code";

	static public function getColTypes() {
		return [
			self::MEMB_GUID => static::TYPE_BOOL,
			self::ACCOUNT => static::TYPE_STRING,
			self::PASSWORD => static::TYPE_STRING,
			self::NAME => static::TYPE_STRING,
			self::SNO_NUMB => static::TYPE_INT,
			self::POST_CODE => static::TYPE_STRING,
			self::ADDR_INFO => static::TYPE_STRING,
			self::ADDR_DETA => static::TYPE_STRING,
			self::TEL_NUMB => static::TYPE_STRING,
			self::PHON_NUMB => static::TYPE_STRING,
			self::MAIL_ADDR => static::TYPE_STRING,
			self::FPAS_QUES => static::TYPE_STRING,
			self::FPAS_ANSW => static::TYPE_STRING,
			self::JOB_CODE => static::TYPE_STRING,
			self::REGISTRATION_DATETIME => static::TYPE_DATETIME,
			self::MODI_DAYS => static::TYPE_DATETIME,
			self::OUT_DAYS => static::TYPE_DATETIME,
			self::TRUE_DAYS => static::TYPE_DATETIME,
			self::MAIL_CHEK => static::TYPE_BOOL,
			self::BLOC_CODE => static::TYPE_BOOL,
			self::CTL1_CODE => static::TYPE_INT,
		];
	}
}
