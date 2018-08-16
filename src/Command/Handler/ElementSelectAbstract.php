<?php

namespace Tz7\WebScraper\Command\Handler;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


abstract class ElementSelectAbstract extends Handler
{
    const SELECTOR = 'selector';

    /**
     * @param Command $command
     *
     * @return bool
     */
    protected function hasSelector(Command $command)
    {
        return $command->hasConfiguration(self::SELECTOR);
    }

    /**
     * @param Command $command
     *
     * @return WebElementSelectAdapterInterface
     */
    protected function createSelector(Command $command)
    {
        $selector = $command->getConfigBy(self::SELECTOR);

        return $command->getDriver()->getSelectorFactory()->create($selector);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setDefaults(
                [
                    self::SELECTOR => null
                ]
            )
            ->setAllowedTypes(
                self::SELECTOR,
                [
                    'string',
                    'null'
                ]
            );
    }
}
