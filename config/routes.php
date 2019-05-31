<?php
return [
    '/' => ['get', 'Main@index'],
    '/load' => ['post', 'Main@load'],
    '/tload' => ['post', 'Main@tload'],
    '/upload' => ['post', 'Main@upload', ['auth']],

    // '/user/[]' => ['get', 'Main@user'],

    '/login' => ['get', 'Auth@login'],
    '/logout' => ['get', 'Auth@logout'],
];
?>
