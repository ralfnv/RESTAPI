<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 07.09.2015
 */

namespace Api\User;

use Base\AbstractController\AbstractController;
use Services\Service;

class UserController extends AbstractController
{

    /**
     * доступные поля для редактирования
     * @var array
     */
    protected $beforeActions = [
        'insert' => ['checkPermission', 'checkUserType'],
        'edit' => ['checkPermission', 'checkPermissionUser', 'checkUserType'],
        'delete' => ['checkPermission', 'checkPermissionUser'],
        'view' => ['checkPermissionUser'],
    ];


    /**
     * @var User;
     */
    private $user = null;

    /**
     * Проверка доступа к событиям
     */
    protected function checkPermission()
    {
        if (!(Service::user()->isAdmin() || Service::user()->isSuperAdmin())) {
            throw new \Exception('Ошибка доступа');
        }
    }

    /**
     * проврека на действия с пользователем
     * @throws \Exception
     */
    protected function checkPermissionUser()
    {
        $uData = Service::request()->post('userData');
        $userId = isset($uData['id']) ? $uData['id'] : null;
        if ($userId) {
            $this->user = new User($userId);
            if (!$this->user->id) {
                throw new \Exception('Пользователь не найден');
            }

            $loadedUser = $this->user;

            if (Service::user()->isAdmin() && ($loadedUser->isAdmin() || $loadedUser->isSuperAdmin())
                || Service::user()->isSuperAdmin() && $loadedUser->isSuperAdmin()
                || Service::user()->isUser() && Service::user()->id != $this->user->id
            ) {
                throw new \Exception('Ошибка доступа');
            }
        } else {
            throw new \Exception('Не указан пользователь');
        }
    }

    /**
     * Добавление
     * @return User
     * @throws \Exception
     */
    protected function insertAction()
    {
        $this->user = new User();

        $userData = Service::request()->post('userData');

        //для Insert Id не нужен
        if (isset($userData['id'])) {
            unset($userData['id']);
        }

        if (!$userData) {
            throw new \Exception('Заполните данные по пользователю');
        }
        return $this->user->assign($userData)->save()->getDataArr();
    }

    /**
     * Редактирование
     * @return User
     * @throws \Exception
     */
    protected function editAction()
    {
        $userData = Service::request()->post('userData');
        return $this->user->assign($userData)->save(array_keys($userData))->getDataArr();
    }

    /**
     * Удаление
     */
    protected function deleteAction()
    {
        return ['deleted' => $this->user->delete()];
    }

    /**
     * Просмотр
     * @return array
     */
    protected function viewAction()
    {
        if (Service::user()->isAdmin() || Service::user()->isSuperAdmin()) {
            return $this->user->getDataArr(['id', 'name', 'age', 'login', 'password', 'permission']);
        } else {
            return $this->user->getDataArr(['name', 'age']);
        }
    }

    /**
     * Проверка типов создаваемых/редактируемых пользователей
     * @return bool
     * @throws \Exception
     */
    protected function checkUserType()
    {
        $accessTypes = [];
        $userData = Service::request()->post('userData');
        $userType = isset($userData['permission']) ? $userData['permission'] : null;
        if (Service::user()->isSuperAdmin()) {
            array_push($accessTypes, User::USER, User::ADMIN);
        } elseif (Service::user()->isAdmin()) {
            $accessTypes[] = User::USER;
        }

        if ($userType !== null && !in_array($userType, $accessTypes)) {
            throw new \Exception('Не корректные права пользователя');
        }
    }
}