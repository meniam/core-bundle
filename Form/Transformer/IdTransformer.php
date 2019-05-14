<?php

namespace Meniam\Bundle\CoreBundle\Form\Transformer;

use Meniam\Bundle\CoreBundle\Service\IdableInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdTransformer implements DataTransformerInterface
{
    private $service;

    public function __construct(IdableInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param object $item
     * @return string
     */
    public function transform($item)
    {
        return $this->service->getDisplayId($item);
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
        return $this->service->getId($text);
    }

}