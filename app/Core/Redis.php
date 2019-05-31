<?php
// namespace Core;
function redis() {
    return $GLOBALS['redis'] = $GLOBALS['redis'] ?? new Predis\Client(config('redis'));
}
?>
