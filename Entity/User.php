<?php

namespace Meniam\Bundle\CoreBundle\Entity;

use \Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class User implements Serializable, UserInterface
{
    const ROLE_ROOT          = 'ROLE_ROOT';        // Круче только бог, его права даже не проверяются
    const ROLE_SUPER_ADMIN   = 'ROLE_SUPER_ADMIN'; // Роль супервайзера, владелец, но не разработчик, есть все права, кроме тех что нужны прогерам
    const ROLE_ADMIN         = 'ROLE_ADMIN';       // Администратор обычный, права минимальные
    const ROLE_EMPLOYEE      = 'ROLE_EMPLOYEE';    // Роль сотрудника без доступа в админку
    const ROLE_USER          = 'ROLE_USER';        // Пользователь сайта

    abstract function getRole();

    public function isRoot()
    {
        return ($this->getRole() == self::ROLE_ROOT);
    }

    public function isAdmin()
    {
        return in_array((string)$this->getRole(), [self::ROLE_ROOT, self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function isEmployee()
    {
        return ($this->getRole() == self::ROLE_EMPLOYEE) || $this->isAdmin();
    }
}