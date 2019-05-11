<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use HtmlSanitizer\Sanitizer;
use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class HtmlSanitize extends FilterRule
{
    /** @var Sanitizer */
    private $sanitizer;

    public function filter($value)
    {
        return $this->getSanitize()->sanitize($value);
    }

    /**
     * @return Sanitizer
     */
    private function getSanitize()
    {
        if (!$this->sanitizer) {
            $this->sanitizer = Sanitizer::create([
                'extensions' => ['basic', 'image'],
                'tags' => [
                    'a' => [
                        'allowed_hosts' => null,
                        'allow_mailto' => false,
                    ],
                    'blockquote' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'br' => [
                        'allowed_attributes' => [],
                    ],
                    'code' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'pre' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'dd' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'del' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'div' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'dl' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'dt' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'em' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h1' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h2' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h3' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h4' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h5' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'h6' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'hr' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'i' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'li' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'ol' => [
                        'allowed_attributes' => ['class', 'title'],
                    ],
                    'p' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'q' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'small' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'span' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'strong' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'sub' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'sup' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'table' => [
                        'allowed_attributes' => ['class', "colspan", "rowspan", "border"],
                    ],
                    'tbody' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'td' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'tfoot' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'thead' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'th' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'tr' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'ul' => [
                        'allowed_attributes' => ['class'],
                    ],
                    'img' => [
                        'allowed_attributes' => ['src', 'alt', 'title', 'class'],
                        'allowed_hosts' => null,
                        'allow_data_uri' => false,
                    ],
                ],
            ]);
        }

        return $this->sanitizer;
    }
}
