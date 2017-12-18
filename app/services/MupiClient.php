<?php

declare(strict_types=1);

namespace App\Services;

class MupiClient {

	const KEY_KEY = 'key';
	const KEY_ORDER = 'order';
	const KEY_LIMIT = 'limit';
	const KEY_FILTER = 'filter';

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

	protected function request($relPath, $method, $data): array {
		$data[self::KEY_KEY] = $this->token;
		$requestUrl = "{$this->baseUrl}{$relPath}";
		if ($method === self::METHOD_GET) {
			$requestUrl .= (self::QM . http_build_query($data));
		} else {
			$data = json_encode($data);
		}
		$ch = curl_init($requestUrl);
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
			throw new \Exception('Method not allowed');
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

		return [$httpCode => json_decode($response, true)];
	}

	public function __call($name, array $params = []) {
		foreach (self::allowedMethods() as $method) {
			$lcm = strtolower($method);
			$pattern = "/^{$lcm}/";
			if (preg_match($pattern, $name)) {
				$junk = preg_replace($pattern, '', $name);
				return $this->request(self::convert($junk), $method, ...$params);
			}
		}

		throw new \Exception("Unknown operation '{$name}'");
	}

	static public function convert($name): string {
		$candidate = preg_replace_callback('/[A-Z]/', function ($matches) {
			return "/" . strtolower($matches[0]);
		}, $name);

		return preg_match('~^/~', $candidate)
			? $candidate
			: "/";
	}

	static public function allowedMethods(): array {
		return [
			self::METHOD_GET,
			self::METHOD_POST,
			self::METHOD_PUT,
			self::METHOD_DELETE,
		];
	}
}
