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
}