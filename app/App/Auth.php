<?php
namespace App;
class Auth extends App {
    function login($request) {
        if($request['code'] ?? false) {
            $response = json_decode($this->curl('https://oauth.vk.com/access_token?'.http_build_query([
                'client_id' => config('vk.id'),
                'client_secret' => config('vk.secret'),
                'code' => $request['code'],
                'redirect_uri' => config('app.url').'/login',
            ])), true);
            if($response['error'] ?? false) return $this->redirect('/login');
            if(!$user = db()->select("SELECT * FROM users WHERE `vk`='$response[user_id]'")[0] ?? false) {
                $profile = json_decode($this->curl('https://api.vk.com/method/users.get?'.http_build_query([
                    'user_ids' => $response['user_id'],
                    'access_token' => $response['access_token'],
                    'fields' => 'photo_100',
                    'v' => config('vk.version'),
                    'lang' => 'ru',
                ])), true)['response'][0];
                $id = db()->insert("INSERT INTO users(`vk`, `name`, `avatar`) VALUES('$profile[id]', '$profile[first_name] $profile[last_name]', '$profile[photo_100]')");
                $user = db()->select("SELECT * FROM users WHERE `id`='$id'")[0];
            }
            auth()->login($user);
            return $this->redirect('/');
        } else {
            return $this->redirect('http://oauth.vk.com/authorize?'.http_build_query([
                'client_id' => config('vk.id'),
                'redirect_uri' => config('app.url').'/login',
                'response_type' => 'code',
            ]));
        }
    }
    function logout(){
        auth()->logout();
        return $this->redirect('/');
    }
}
?>
