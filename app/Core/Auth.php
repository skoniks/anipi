<?php
// namespace Core;
class Auth {
    private $u = false;
    function __construct() {
        session_set_cookie_params(config('app.session.lifetime'), '/');
        session_name(config('app.session.name'));
        session_start();
    }
    function user() {
        if($this->u) return $this->u;
        if(!isset($_SESSION['user']) || !isset($_SESSION['token'])) return false;
        if(!$user = db()->select("SELECT * FROM users WHERE id='$_SESSION[user]' LIMIT 1")[0] ?? false) return false;
        if(md5($user['token'].session_id()) != $_SESSION['token']) return false;
        return $this->u = $user;
    }
    function login($user) {
        if(!isset($user['token']) || !$user['token']) {
            $user['token'] = bin2hex(random_bytes(10));
            db()->update("UPDATE users SET token='$user[token]' WHERE id='$user[id]'");
        }
        $_SESSION['user'] = $user['id'];
        $_SESSION['token'] = md5($user['token'].session_id());
    }
    function logout() {
        $this->u = false;
        session_unset();
    }
}
$Auth = new Auth();
function auth() {
    return $GLOBALS['Auth'];
}
?>
