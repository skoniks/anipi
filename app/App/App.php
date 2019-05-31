<?php
namespace App;
class App {
    function __construct() {
        // ...
    }
    function json($data = []) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    function e($s){
        return addslashes(htmlentities($s));
    }
    function redirect($url = '/') {
        return header('Location: '.$url);
    }
    function user($id, $lock = false) {
        return db()->select("SELECT * FROM users WHERE id='$id' LIMIT 1".($lock ? " FOR UPDATE" : ''))[0] ?? false;
    }
    function curl($url, $post = false, $h = false, $ip = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        !$post ?: curl_setopt($ch, CURLOPT_POST, true);
        !$post ?: curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        !$ip ?: curl_setopt($ch, CURLOPT_INTERFACE, $ip);
        !$h ?: curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
?>
