<?php

namespace Tz7\WebScraper\Command\Handler;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ScalarSeed;


class ReadAttribute extends ReadText
{
    const ATTRIBUTE = 'attribute';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $element = $command->getElementStack()->top();

        if ($this->hasSelector($command))
        {
            $element = $element->findElement(
                $this->createSelector($command)
            );
        }

        $attribute = $command->getConfigBy(self::ATTRIBUTE);
        $seed      = new ScalarSeed($element->getAttribute($attribute));

        return $command->setSeed($seed);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::ATTRIBUTE)
            ->setAllowedTypes(self::ATTRIBUTE, 'string');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'read_attribute';
    }
}
