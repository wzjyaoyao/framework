<?php
if (!function_exists('debug')) {
    function debug($debug=false){
        if ($debug === false) {
            // 关闭所有PHP错误报告
            error_reporting(0);
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
        }
    }
}

if (!function_exists('pr')) {
    function pr($array){
        if (is_array($array)){
            echo "<pre>";
            print_r($array);
            echo "</pre><hr/>";
            die;
        }else{
            echo $array;
            die;
        }
    }
}

// 浏览器友好的变量输出
if (!function_exists('dd')) {
    function dd($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else{
            return $output;
        }
    }
}

// 13位时间戳转换日期
if (!function_exists('toDate')) {
    function toDate($time,$formate){
        return date($formate,substr($time,0,10));
    }
}


if (!function_exists('writeLog')) {
    function writeLog($status,$message,$request_id,$action,$ext_info) {
        // 空格转化
        if ($ext_info) {
            $ext_info = json_encode($ext_info,JSON_UNESCAPED_UNICODE);
            $ext_info = replace($ext_info);
        }
        if ($action == '/favicon.ico'){
            return true;
        }
        error_log("[".date('c')."] '{$status}' '{$message}' '{$request_id}' '{$action}' '".get_server_ip()."' '{$ext_info}'\r\n",3,LOG_PATH.'all.log');
        return true;
    }
}
if (!function_exists('replace')) {
    function replace($ext_info)
    {
        return str_replace(' ', '_', $ext_info);
    }
}
if (!function_exists('get_server_ip')) {
    function get_server_ip()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }
        return gethostbyname($_SERVER['HOSTNAME']);
    }
}


/**
 * 随机字符串支持传唯一值再次生成唯一算法
 * @param $uniqueStr 参与随机的唯一key
 * @return string
 */
if (!function_exists('uuidStr')) {
    function uuidStr($uniqueStr)
    {
        return md5(uniqid(md5(microtime(true)) . '-' . rand() . '-' . $uniqueStr, true));
    }
}


/**
 * 生成高并发不重复12位数字
 * @param $uniqueStr
 * @return string
 */
if (!function_exists('makeUniqueNumber')) {
    function makeUniqueNumber($uniqueStr) {
        return mt_rand(10,99).str_pad(crc32(microtime().'-'.$uniqueStr.'-'.mt_rand(1,9999999999)), 10, '0', STR_PAD_LEFT);
    }
}


/**
 * Curl send get request, support HTTPS protocol
 * @param string $url The request url
 * @param string $refer The request refer
 * @param int $timeout The timeout seconds
 * @return mixed
 */
if (!function_exists('getRequest')) {
    function getRequest($url, $refer = "", $timeout = 10)
    {
        $ssl = stripos($url, 'https://') === 0 ? true : false;
        $curlObj = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_HTTPHEADER => ['Expect:'],
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ];
        if ($refer) {
            $options[CURLOPT_REFERER] = $refer;
        }
        if ($ssl) {
            //support https
            $options[CURLOPT_SSL_VERIFYHOST] = false;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }
        curl_setopt_array($curlObj, $options);
        $returnData = curl_exec($curlObj);
        if (curl_errno($curlObj)) {
            //error message
            $returnData = curl_error($curlObj);
        }
        curl_close($curlObj);
        return $returnData;
    }
}

/**
 * Curl send post request, support HTTPS protocol
 * @param string $url The request url
 * @param array $data The post data
 * @param string $refer The request refer
 * @param int $timeout The timeout seconds
 * @param array $header The other request header
 * @return mixed
 */
if (!function_exists('postRequest')) {
    function postRequest($url, $data, $refer = "", $timeout = 10, $header = [])
    {
        $curlObj = curl_init();
        $ssl = stripos($url, 'https://') === 0 ? true : false;
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_HTTPHEADER => ['Expect:'],
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_REFERER => $refer
        ];
        if (!empty($header)) {
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        if ($refer) {
            $options[CURLOPT_REFERER] = $refer;
        }
        if ($ssl) {
            //support https
            $options[CURLOPT_SSL_VERIFYHOST] = false;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }
        curl_setopt_array($curlObj, $options);
        $returnData = curl_exec($curlObj);
        if (curl_errno($curlObj)) {
            //error message
            $returnData = curl_error($curlObj);
        }
        curl_close($curlObj);
        $returnData = json_decode($returnData,true);
        return $returnData;
    }
}

// 递归数组合并
function recursion_merge(&$a,$b){
	foreach($a as $key=>&$val){
		if(is_array($val) && array_key_exists($key, $b) && is_array($b[$key])){
			recursion_merge($val,$b[$key]);
			$val = $val + $b[$key];
		}else if(is_array($val) || (array_key_exists($key, $b) && is_array($b[$key]))){
			$val = is_array($val)?$val:$b[$key];
		}
	}

	$a = $a + $b;
}

function is_list(array $a) {
	$count = count($a);
	if ($count === 0) return true;
	return !array_diff_key($a, array_fill(0, $count, NULL));
}

if (! function_exists('env')) {
    /**
     * Search the different places for environment variables and return first value found.
     *
     * @param string $name
     * @param string $default
     * @return string|null
     */
    function env($name,$default=null)
    {
        switch (true) {
            case array_key_exists($name, $_ENV):
                return $_ENV[$name];
            case array_key_exists($name, $_SERVER):
                return $_SERVER[$name];
            default:
                $value = getenv($name);
                return $value === false ? $default : $value; // switch getenv default to null
        }
    }
}

if (! function_exists('setEnv')){
    /**
     * Set an environment variable.
     *
     * This is done using:
     * - putenv,
     * - $_ENV,
     * - $_SERVER.
     *
     * The environment variable value is stripped of single and double quotes.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    function setEnv($name, $value = null)
    {
        if (function_exists('putenv')) {
            putenv("$name=$value");
        }
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

if (! function_exists('clearEnv')){
    /**
     * Clear an environment variable.
     *
     * This is not (currently) used by Dotenv but is provided as a utility
     * method for 3rd party code.
     *
     * This is done using:
     * - putenv,
     * - unset($_ENV, $_SERVER).
     *
     * @param string $name
     *
     * @see setEnvironmentVariable()
     *
     * @return void
     */
    function clearEnv($name)
    {
        if (function_exists('putenv')) {
            putenv($name);
        }

        unset($_ENV[$name], $_SERVER[$name]);
    }
}

function batchSetEnv($path){
    $filePath = $path.'/env.php';
    if(!is_file($filePath)) {
        return;
    }
    $config = include $filePath;
    if (empty($config)) {
        return;
    }
    foreach($config as $name=>$value){
        setEnv($name,$value);
    }
    return;
}

