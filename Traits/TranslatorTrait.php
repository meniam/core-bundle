<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @required
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    public function plural($one, $two, $ten, $count)
    {
        return $this->translator->trans("{$one}|{$two}|{$ten}", ['%count%' => $count]);
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
