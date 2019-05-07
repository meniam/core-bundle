<?php

namespace App\Traits;

use Psr\Container\ContainerInterface;
use \LogicException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @property ContainerInterface $container
 */
trait ValidatorTrait
{
    /**
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (!isset($this->container)) {
            throw new LogicException('ValidatorInterface must be defined');
        }

        return $this->container->get(ValidatorInterface::class);
    }

    /**
     * @param      $value
     * @param null $constraints
     * @param null $groups
     * @return ConstraintViolation[]|ConstraintViolationList
     */
    protected function validateValue($value, $constraints = null, $groups = null)
    {
        /** @var ConstraintViolationList|ConstraintViolation[] $violationList */
        $violationList = $this->getValidator()->validate($value, $constraints, $groups);
        return $violationList;
    }

    /**
     * @param      $value
     * @param null $constraints
     * @param null $groups
     * @return bool|string|null
     */
    protected function isInvalidValue($value, $constraints = null, $groups = null)
    {
        $violationList = $this->validateValue($value, $constraints, $groups);
        foreach ($violationList as $violation) break;
        if (isset($violation)) return $violation->getMessage();
        return false;
    }


    /**
     * @param      $value
     * @param null $constraints
     * @param null $groups
     * @return bool
     */
    protected function isValidValue($value, $constraints = null, $groups = null)
    {
        $violationList = $this->validateValue($value, $constraints, $groups);
        foreach ($violationList as $violation) break;
        return !isset($violation);
    }
}
