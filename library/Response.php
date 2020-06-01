<?php
namespace Hoowu\Library;
// Response的Rest输出
class Response {
    public static $subCode;
    /**
     * 成功格式输出
     * @param array|null $data
     */
    public static function _success($data=[]) {
        $result = array(
            'status' => 1,
            'data' => $data
        );
        self::_json($result);
    }

    // code对应的文本读取各自项目的config/code.php

    /**
     * 错误格式输出
     * code 对应的中文解释 在各自项目里的config/code.php里进行配置,格式如下
     * return [
     *    'error_code'=>[
     *        1000=>'服务异常'
     *    ]
     * ]
     * @param int $code
     * @param string $subCode
     * @param string $info
     */
    public static function _error($code,$subCode='',$info='',$subError='') {
        $data = '';
        if ($subCode) {
            $data = [
                'sub_error'=>$subError ? $subError : Config::get('code.sub_error.'.$subCode),
                'sub_error_code'=>$subCode
            ];
            if ($info) {
                $data['sub_error_data'] = $info;
                $subError = Config::get('code.sub_error.'.$subCode.'_'.$info);
                if ($subError) {
					$data['sub_error'] = $subError;
				}
            }
        }

        $result = array(
            'status' => 0,
            'error_code'=>$code,
            'error'=>Config::get('code.error_code.'.$code),
            'data'=>$data
        );
        self::_json($result);
    }


    /**
     * 分页输出
     * @param $list 数据列表
     * @param $total 总数
     * @param $page 页码
     * @param $size 页长
     * @param array $others 其他附加参数
     * @return mixed
     */
    public static function _page($list,$total,$page,$size,$others=[]) {
        $data = [
            'page'=>(int)$page,
            'size'=>(int)$size,
            'total'=>(int)$total,
            'list'=>(array)$list
        ];
        if (!empty($others)) {
            $data = array_merge($data,$others);
        }
        self::_success($data);
    }

    public static function _json($data) {
		header('Content-type: application/json');
        if (APP_DEBUG === true) {
            $endTime    = microtime(true);
            $runtime    = ($endTime - $_GET['runtime.start.time']) * 1000; //将时间转换为毫秒
            $endMemory  = memory_get_usage();
            $usedMemory = ($endMemory - $_GET['runtime.start.memory']) / 1024;
            writeLog(
                Config::get('code.log_status.suc'),
                Config::get('code.log_msg.access'),
                md5($_SERVER['REQUEST_URI']),
                $_SERVER['REQUEST_URI'],
                [
                    "runTime"=>$runtime.'ms',
                    'runMemory'=>$usedMemory.'k',
                    "startTime"=>($_GET['runtime.start.time']*1000).'ms',
                    "endTime"=>($endTime*1000).'ms',
                    "startMemory"=>($_GET['runtime.start.memory']/1024).'k',
                    "endMemory"=>($endMemory/1024).'k'
                ]
            );
        }
        exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
}