<?php
namespace Core;
$_url = '/'.trim(preg_replace('/\/+/', '/', strtolower(explode('?', $_SERVER['REQUEST_URI'])[0])), '/');
$_method = strtolower($_SERVER['REQUEST_METHOD']);
$_request = array_merge($_REQUEST, $_FILES);
foreach (config('routes') as $url => $route) {
    list($method, $class) = $route;
    $middleware = $route[2] ?? [];
    if($method != $_method && $method != '*') continue;
    if($url == $_url) return route($class, [$_request]);
    $route = '/'.preg_replace("/\[\]/", '(\w+?)', str_replace('/', '\/', $url)).'$/';
    if(preg_match($route, $_url, $matches)) if($matches[0] == $_url) {
        $params = array_slice($matches, 1);
        $params[] = $_request;
        return route($class, $params);
    }
}
throw new \Exception('Not Found', 404);
function route($class, $request) {
    list($class, $function) = explode('@', $class);
    include(__DIR__.'/../App/App.php');
    include(__DIR__.'/../App/'.$class.'.php');
    $class = 'App\\'.$class;
    $class = new $class();
    echo(call_user_func_array([
        $class, $function
    ], $request));
}
?>
