<?php
namespace Hoowu\Library;
class Url {
	// 将url合并参数
	static public function build($url = '', $parts = array()) {
		if (function_exists('http_build_url')) {
			return http_build_url($url, $parts);
		}

		if (empty($url) && (!is_array($parts) || count($parts) === 0)) {
			return false;
		}
		return self::unparse(self::mergeQuery(parse_url($url), $parts));
	}

	// 数据重新组装为url
	static public function unparse($parsed_url = null) {
		if (!is_array($parsed_url)) {
			return false;
		}

		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

		return "$scheme$user$pass$host$port$path$query$fragment";
	}

	// 合并url里的query参数
	static public function mergeQuery($urlParsed = null, $query = null) {
		// no url ?
		if (!is_array($urlParsed)) {
			return false;
		}

		// no new query ?
		if (!is_array($query) || empty($query)) {
			return $urlParsed;
		}

		// no old query ?
		if (empty($urlParsed['query'])) {
			$urlParsed['query'] = '';
		}


		// 1. parse old query
		$oldQuery = array();
		parse_str($urlParsed['query'], $oldQuery);

		// 2. merge new query
		// 3. update old query
		$urlParsed['query'] = http_build_query(array_merge($oldQuery, $query));


		return $urlParsed;
	}
}
