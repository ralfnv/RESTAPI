<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 08.09.2015
 */

namespace Api\Router;

use Api\AuthUser\AuthUserController;
use Api\User\UserController;
use Services\Service;

class Router
{
    public static function run($action)
    {
        list($controller, $ctrlAct) = explode('.', $action, 2) + [null, null];
        switch ($controller) {
            //логиним пользователя
            case 'auth':
                $data = (new AuthUserController())->run($ctrlAct);
                break;
            //Действия над ползователем
            case 'user':
                if (Service::user()->id) {
                    $uc = new UserController();
                    $data = $uc->run($ctrlAct);
                } else {
                    $data = ['state' => false, 'msg' => 'Необходима авторизация'];
                }
                break;
            default:
                $data = ['state' => false, 'msg' => 'Ошибка роутинга'];
                break;
        }
        return json_encode($data);
    }
}