<?php

namespace Meniam\Bundle\CoreBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PriceTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param object $item
     * @return string
     */
    public function transform($item)
    {
        $item = (int)$item;
        $price = round($item/100, 2);
        return ($price > 0) ? $price : '';
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
        return ceil((int)$text * 100);
    }

}