<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\CommandSeed;


/**
 * Switch between multiple cases, seeding multiple commands processed by the PlantationMiddleware.
 */
class ConditionalSwitchStatement extends Handler
{
    const CASES = 'cases';

    const CASE_CONDITION = 'condition';
    const CASE_EXECUTE   = 'execute';

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
        $cases = array_map(
            function (array $case) use ($command)
            {
                $success = $this->language->evaluate(
                    $case[self::CASE_CONDITION],
                    $command->getExpressionContext()->getArrayCopy()
                );

                if ($success)
                {
                    return new CommandSeed($command->createChildByConfig($case[self::CASE_EXECUTE]));
                }
                else
                {
                    return null;
                }
            },
            $command->getConfigBy(self::CASES)
        );

        $command->setSeed(new ArraySeed(array_filter($cases)));
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::CASES)
            ->setAllowedTypes(self::CASES, 'array')
            ->setAllowedValues(
                self::CASES,
                function (array $cases)
                {
                    return !array_filter(
                        $cases,
                        function ($case)
                        {
                            return !is_array($case) || !isset($case[self::CASE_CONDITION], $case[self::CASE_EXECUTE]);
                        }
                    );
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'switch';
    }
}
