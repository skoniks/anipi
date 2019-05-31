<?php
namespace Core;
class Handler {
    function __construct(\Exception $e) {
        header('HTTP/1.0 ' . $e->getCode() . ' ' . $e->getMessage());
    }
}
try {
    include(__DIR__.'/Config.php');
    include(__DIR__.'/DB.php');
    include(__DIR__.'/Redis.php');
    include(__DIR__.'/S3.php');
    include(__DIR__.'/Auth.php');
    include(__DIR__.'/View.php');
    include(__DIR__.'/Route.php');
} catch (\Exception $e) {
    var_dump($e);
    // return new Handler($e);
}
?>
