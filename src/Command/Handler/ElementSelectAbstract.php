<?php

namespace Tz7\WebScraper\Command\Handler;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;


abstract class ElementSelectAbstract extends Handler
{
    const SELECTOR      = 'selector';
    const SELECTOR_TYPE = 'selector_type';

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
        $selector     = $command->getConfigBy(self::SELECTOR);
        $selectorType = $command->getConfigBy(self::SELECTOR_TYPE);

        if ($selectorType === null)
        {
            $selectorType = $this->getSelectorType($selector);
        }

        return $command->getDriver()->getSelectorFactory()->createByType(
            $selectorType,
            $this->getCleanedSelector($selector)
        );
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setDefaults(
                [
                    self::SELECTOR      => null,
                    self::SELECTOR_TYPE => null
                ]
            )
            ->setAllowedTypes(
                self::SELECTOR,
                [
                    'string',
                    'null'
                ]
            )
            ->setAllowedTypes(
                self::SELECTOR_TYPE,
                [
                    'string',
                    'null'
                ]
            )
            ->setAllowedValues(
                self::SELECTOR_TYPE,
                [
                    WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR,
                    WebElementSelectAdapterInterface::TYPE_ID,
                    WebElementSelectAdapterInterface::TYPE_NAME,
                    WebElementSelectAdapterInterface::TYPE_LINK_TEXT,
                    WebElementSelectAdapterInterface::TYPE_PARTIAL_LINK_TEXT,
                    WebElementSelectAdapterInterface::TYPE_TAG_NAME,
                    WebElementSelectAdapterInterface::TYPE_XPATH,
                    null
                ]
            );
    }
}
