<?php
date_default_timezone_set('PRC');
// 引入常用函数
include HOOWU_PATH . '/helper.php';
//批量设置环境变量
batchSetEnv(BASE_PATH);
// 当前环境
define('ENV_TYPE', env('ENV_TYPE','dev'));
define('APP_DEBUG', env('APP_DEBUG',true));
define('RUNTIME_PATH', env('RUNTIME_PATH','/data/runtime/admin-market-tmall-isv.51h5.com/'));
define('LOG_PATH', env('LOG_PATH','/data/log/web/admin-market-tmall-isv.51h5.com/'));
define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH',BASE_PATH.'/app');
define('CONFIG_PATH',BASE_PATH.'/config');
define('VENDOR_PATH',BASE_PATH.'/vendor');
define('LIBRARY_PATH',HOOWU_PATH.'/library');
define('STORAGE_PATH',BASE_PATH.'/storage');
define('SERVICE_PATH',APP_PATH.'/Services');
define('VIEWS_PATH',APP_PATH.'/Views');
include VENDOR_PATH.'/autoload.php';
