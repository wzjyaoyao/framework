<?php
include HOOWU_PATH . '/base.php';
use Hoowu\Library\Config;
use Hoowu\Library\Response;
debug(APP_DEBUG);
$routes = Config::get('route.path');
if (APP_DEBUG === true) {
    $_GET['runtime.start.time'] = microtime(true);
    $_GET['runtime.start.memory'] = memory_get_usage();
    // 记录日志调试模式
    writeLog(
        Config::get('code.log_status.suc'),
        Config::get('code.log_msg.access'),
        md5($_SERVER['REQUEST_URI']),
        $_SERVER['REQUEST_URI'],
        $_POST
    );
}
// 路由处理
$routeUri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$check = false;
if (isset($routes[$routeUri])) {
    $check = true;
};
if ($check === false) {
    foreach (Config::get('route.rule') as $_k=>$_r) {
        $count=0;
        $_r = preg_replace("|$_k|", $_r, $routeUri,-1,$count);
        if ($count<1) {
            continue;
        }
        $query = parse_url($_r);
        $routeUri = $query['path'];
        parse_str($query['query'], $queryParsed);
        $_GET = $_GET + $queryParsed;
        $check = true;
        break;
    }
}

if ($check === false) {
    writeLog(Config::get('code.log_status.fail'),Config::get('code.log_msg.route'),md5($_SERVER['REQUEST_URI']),$_SERVER['REQUEST_URI'],'');
    header("Location: /404.html");
    exit;
}

list($controller,$action) = explode('@',$routes[$routeUri]);
$_GET = $_GET + ['controller'=>strtolower(str_replace('Controller','',$controller)),'action'=>$action];
$controllerPath = 'App\Controllers\\'.$controller;
try{
    (new $controllerPath())->$action();
}
catch(\Exception $e){
    $extInfo = [
        'paras'=>$_GET,
        'exception'=>$e->getCode(),
        'info'=>$e->getMessage(),
    ];
    writeLog(Config::get('code.log_status.fail'),Config::get('code.log_msg.logic'),md5($_SERVER['REQUEST_URI']),$_SERVER['REQUEST_URI'],$extInfo);
}
catch(\Error $error) {
    $info = '';
    if (APP_DEBUG === true) {
        $info = $error->getMessage();
    }

    Response::_error(1005,'service_catch',$info);
}
finally {
    Response::_error(1005,'service_catch',$error->getMessage());
}
