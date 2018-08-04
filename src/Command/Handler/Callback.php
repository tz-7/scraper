<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\ScalarSeed;


/**
 * Calls an user defined function as fn(Command $command, array $parameters) and seeds it.
 */
class Callback extends Handler
{
    const USER_FUNCTION = 'callable';
    const PARAMETERS    = 'parameters';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $function = $command->getConfigBy(self::USER_FUNCTION);
        $parameters = $command->getConfigBy(self::PARAMETERS);

        $result = call_user_func($function, $command, $parameters);
        $seed   = is_array($result) ? new ArraySeed($result) : new ScalarSeed($result);

        return $command->setSeed($seed);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::USER_FUNCTION)
            ->setDefault(self::PARAMETERS, []);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'callback';
    }
}
