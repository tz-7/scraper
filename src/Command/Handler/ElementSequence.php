<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LogLevel;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Exception\CommandNotFoundException;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use UnexpectedValueException;


/**
 * Generates a command seed on selected elements, processed by the PlantationMiddleware.
 */
class ElementSequence extends Handler
{
    const SEQUENCE = 'sequence';
    const ON_EACH  = 'on_each';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $sequence = array_map(
            function (WebElementAdapterInterface $element) use ($command)
            {
                $childCommand = $command->createChildByConfigName(self::ON_EACH);
                $childCommand->getElementStack()->append($element);

                return new CommandSeed($childCommand);
            },
            $this->getElements($command)
        );

        $command->setSeed(new ArraySeed($sequence));
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(
                [
                    self::SEQUENCE,
                    self::ON_EACH
                ]
            )
            ->setDefault(self::PROPAGATE, false)
            ->setAllowedValues(self::PROPAGATE, false)
            ->setAllowedTypes(self::ON_EACH, 'array')
            ->setAllowedTypes(self::SEQUENCE, 'string');
    }

    /**
     * @param Command $command
     *
     * @return WebElementAdapterInterface[]
     *
     * @throws CommandNotFoundException
     */
    private function getElements(Command $command)
    {
        $elementStack = $command->getElementStack();

        if ($elementStack->isEmpty())
        {
            throw new UnexpectedValueException('Element stack is empty.');
        }

        $element   = $elementStack->top();
        $rawSelect = $command->getConfigBy(self::SEQUENCE);
        $selector  = $command
            ->getDriver()
            ->getSelectorFactory()
            ->create($rawSelect);

        $elements      = $element->findElements($selector);
        $foundElements = count($elements);

        $this->log(
            $foundElements === 0 ? LogLevel::NOTICE : LogLevel::DEBUG,
            sprintf('ElementSequence on %d elements', $foundElements),
            [
                'element'   => $element->getTagName(),
                'rawSelect' => $rawSelect,
                'options'   => $command->getConfig()
            ]
        );

        return $elements;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'element_sequence';
    }
}
