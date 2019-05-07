<?php

namespace Meniam\Bundle\CoreBundle\Entity;

use \Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class User implements Serializable, UserInterface
{
    const ROLE_ROOT = 'ROLE_ROOT';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_DEVELOPER = 'ROLE_DEVELOPER';
    const ROLE_RUBRIC_EDITOR = 'ROLE_RUBRIC_EDITOR';
}