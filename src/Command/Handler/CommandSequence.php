<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\CommandSeed;


/**
 * Seeding commands, processed by the PlantationMiddleware.
 */
class CommandSequence extends Handler
{
    const SEQUENCE = 'sequence';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $sequence = array_map(
            function (array $config) use ($command)
            {
                return new CommandSeed($command->createChildByConfig($config));
            },
            $command->getConfigBy(self::SEQUENCE)
        );

        $command->setSeed(new ArraySeed($sequence));
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::SEQUENCE)
            ->setAllowedTypes(self::SEQUENCE, 'array');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'command_sequence';
    }
}
