<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Exception\UnexpectedResponseException;
use Tz7\WebScraper\Response\Seed;


/**
 * Adds value from seed of processed_by node to the expression context used in evaluation.
 */
class SeedExpressionContext extends Handler
{
    const KEY = 'key';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $seed = $command->getSeed();

        if ($seed === null)
        {
            throw new UnexpectedResponseException(
                sprintf(
                    'Result instance of "%s" expected, got NULL instead',
                    Seed::class
                )
            );
        }

        if (!$seed instanceof Seed)
        {
            throw new UnexpectedResponseException(
                sprintf(
                    'Result instance of "%s" expected, got "%s" instead',
                    Seed::class,
                    get_class($seed)
                )
            );
        }

        $data = $seed->getData();

        $command->addToExpressionContext($command->getConfigBy(self::KEY), $data);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::KEY)
            ->setAllowedTypes(self::KEY, 'string');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'seed_expression_context';
    }
}
