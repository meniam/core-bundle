<?php

namespace App\Form\Backend\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class JsonFieldTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param object $item
     * @return string
     */
    public function transform($item)
    {
        if (!$item) {
            $item = "{}";
        }
        return trim(json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), '" ');
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $text
     * @return ArrayCollection|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($text)
    {
        return json_decode($text, true);
    }
}