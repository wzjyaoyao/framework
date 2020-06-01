<?php
namespace Hoowu\Library;
class Config {
    static $conf=[];
    static public function get($name) {
        $layers = explode('.',$name);
        if (empty($layers)) {
            return '';
        }
        $file = array_shift($layers);
        if (!isset(self::$conf[$file])) {
            $filePath = CONFIG_PATH . '/'.$file.'.php';
            if (!is_file($filePath)) {
                $filePath = STORAGE_PATH.'/config'.'/'.$file.'.php';
                if(!is_file($filePath)) {
                    return '';
                }
            }

            $config = include $filePath;
            if (empty($config)) {
                return '';
            }
            self::$conf[$file] = $config;
        }
        // 不存在则直接取整个文件配置
        if (empty($layers)) {
            return self::$conf[$file];
        }

        $newVal = '';
        $tmpConfig = self::$conf[$file];
        $tmp = &$tmpConfig;
        foreach ($layers as $_layer) {
            if (!isset($tmp[$_layer])) {
                $newVal = '';
                break;
            }
            $newVal = $tmp[$_layer];
            $tmp = $tmp[$_layer];
        }
        unset($tmp,$tmpConfig);
        return $newVal;
    }
}