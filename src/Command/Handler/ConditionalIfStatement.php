<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\CommandSeed;


/**
 * Conditional command seeding, processed by the PlantationMiddleware.
 */
class ConditionalIfStatement extends Handler
{
    const STATEMENT = 'statement';
    const THEN      = 'then';
    const OTHERWISE = 'otherwise';

    /** @var ExpressionLanguage */
    private $language;

    /**
     * @param LoggerInterface    $logger
     * @param ExpressionLanguage $language
     */
    public function __construct(LoggerInterface $logger, ExpressionLanguage $language)
    {
        parent::__construct($logger);

        $this->language = $language;
    }

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $expressionContext = clone $command->getExpressionContext();
        $expressionContext->offsetSet('element',  $command->getElementStack()->top());

        $success = $this->language->evaluate(
            $command->getConfigBy(self::STATEMENT),
            $expressionContext->getArrayCopy()
        );

        $then = $command->getConfigBy(self::THEN);
        $else = $command->getConfigBy(self::OTHERWISE);

        if ($success && $then !== null)
        {
            $command->setSeed(new CommandSeed($command->createChildByConfig($then)));
        }
        elseif (!$success && $else !== null)
        {
            $command->setSeed(new CommandSeed($command->createChildByConfig($else)));
        }
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setDefaults(
                [
                    self::THEN      => null,
                    self::OTHERWISE => null
                ]
            )
            ->setRequired(self::STATEMENT)
            ->setAllowedTypes(
                self::THEN,
                [
                    'array',
                    'null'
                ]
            )
            ->setAllowedTypes(
                self::OTHERWISE,
                [
                    'array',
                    'null'
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'if';
    }
}
