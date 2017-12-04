<?php

namespace App\Services;

class MupiClient
{
	const KEY_KEY = 'key';

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';

	const CURL_ERROR = 'curl_error';
	const CURL_ERROR_NO = 'curl_error_no';

	const VAL_UNKNOWN = 'unknown';

	const QM = '?';

	protected $baseUrl;
	protected $token;

	public function __construct($baseUrl, $token) {
		if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
			throw new \ErrorException('Invalid URL provided');
		}
		if (empty($token)) {
			throw new \ErrorException('Missing or empty auth token');
		}
		$this->baseUrl = $baseUrl;
		$this->token = $token;
	}

	protected function request($method, $data) {
		$data[self::KEY_KEY] = $this->token;
		$url = $method === self::METHOD_GET
			? $this->baseUrl . self::QM . http_build_query($data)
			: $this->baseUrl;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		switch ($method) {
		case self::METHOD_GET:
			break;
		case self::METHOD_POST:
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			break;
		case self::METHOD_PUT:
		case self::METHOD_DELETE:
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			break;
		default:
			throw new \Exception('NIY');
		}
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($response === false) {
			if (curl_error($ch)) {
				return [
					self::CURL_ERROR => curl_error($ch),
					self::CURL_ERROR_NO => curl_errno($ch),
				];
			}
			return [self::CURL_ERROR => self::VAL_UNKNOWN];
		}

		return [$httpCode => $response];
	}

	public function __call($name, $params) {
		foreach (self::allowedMethods() as $method) {
			$lcm = strtolower($method);
			if (preg_match("/^{$lcm}/", strtolower($name))) {
				return $this->request($method, $params);
			}
		}

		throw new \Exception("Unknown method '{$name}'");
	}

	static public function allowedMethods() {
		return [
			self::METHOD_GET,
			self::METHOD_POST,
			self::METHOD_PUT,
			self::METHOD_DELETE,
		];
	}
}
