<?php

namespace Meniam\Bundle\CoreBundle\Form\Transformer;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityTransformer implements DataTransformerInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param object $item
     * @return string
     */
    public function transform($item)
    {
        if (null === $item) {
            return '';
        }

        return $item->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $text
     * @return object
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($text)
    {
        return $this->getRepository()->findOneBy(['id' => $text]);
    }
}