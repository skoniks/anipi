<?php
// namespace Core;
function view($template, $in = []) {
    extract($in);
    ob_start();
    include(__DIR__.'/../../template/'.$template.'.php');
    return ob_get_clean();
}
?>
