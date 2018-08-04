<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\Response\KeyValueSeed;


class MapElement extends Handler
{
    const KEY   = 'key';
    const VALUE = 'value';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $seed = new KeyValueSeed(
            new CommandSeed($command->createChildByConfigName(self::KEY)),
            new CommandSeed($command->createChildByConfigName(self::VALUE))
        );

        return $command->setSeed($seed);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(
                [
                    self::KEY,
                    self::VALUE
                ]
            )
            ->setAllowedTypes(self::KEY, 'array')
            ->setAllowedTypes(self::VALUE, 'array');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'map_element';
    }
}
