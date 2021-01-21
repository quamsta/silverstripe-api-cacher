<?php

namespace quamsta\ApiCacher;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;

class FeedHelper {

	public static function getJson($url) {
		$safeUrl = FeedHelper::safe_filename($url);
		$cache = Injector::inst()->get(CacheInterface::class . '.apiCacher');

		if (!$cache->has($safeUrl)) {
			$json = FeedHelper::fetchData($url);

			$cache->set($safeUrl, $json, 300);
		} else {

			$json = $cache->get($safeUrl);
			//always fetch data remotely(for testing):
			// $json = FeedHelper::fetchJson($url);
		}
		if ($json) {
			$jsonDecoded = json_decode($json, TRUE);
			return $jsonDecoded;
		}
	}

	public static function getUrl($url) {
		$safeUrl = FeedHelper::safe_filename($url);
		$cache = Injector::inst()->get(CacheInterface::class . '.apiCacher');

		if (!$cache->has($safeUrl)) {
			$data = FeedHelper::fetchData($url);

			$cache->set($safeUrl, $data, 300);
		} else {

			$data = $cache->get($safeUrl);
			//always fetch data remotely(for testing):
			// $data = FeedHelper::fetchData($url);
		}
		if ($data) {
			return $data;
		}
	}

	public static function safe_filename($filename) {
		return preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
	}

	public static function fetchData($url) {
		if (function_exists("curl_init")) {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$content = curl_exec($ch);

			curl_close($ch);
			return $content;
		} else {
			return file_get_contents($url);
		}
	}
}
