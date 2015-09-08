<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 09.09.2015
 */

namespace Api\AuthUser;


use Base\AbstractController\AbstractController;
use Services\Service;

class AuthUserController extends AbstractController
{
    protected function loginAction()
    {
        $user = AuthUser::getInstance();
        $request = Service::request();
        $token = $user->login($request->post('login'), $request->post('password'));
        return  $token ? ['state' => true, 'token' => $token] : ['state' => false, 'msg' => 'Неверные логин/пароль'];
    }
}