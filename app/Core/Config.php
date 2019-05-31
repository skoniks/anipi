<?php
// namespace Core;
function config($path) {
    $path = explode('.', $path);
    $GLOBALS['config_'.$path[0]] = $config = $GLOBALS['config_'.$path[0]] ?? include(__DIR__.'/../../config/'.$path[0].'.php');
    foreach (array_slice($path, 1) as $step) if(isset($config[$step])) $config = $config[$step]; else return NULL;
    return $config;
}
?>
