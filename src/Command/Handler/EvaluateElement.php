<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ScalarSeed;


/**
 * Seeds a DOM element evaluation.
 */
class EvaluateElement extends ElementSelectAbstract
{
    const EXPRESSION = 'expression';

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
        $element    = $command->getElementStack()->top();
        $expression = $command->getConfigBy(self::EXPRESSION);

        if ($this->hasSelector($command))
        {
            $this->logger->debug(
                sprintf(
                    'Selecting "%s" under %s:"%s")',
                    $command->getConfigBy(self::SELECTOR),
                    $element->getTagName(),
                    $element->getText()
                )
            );

            $element = $element->findElement(
                $this->createSelector($command)
            );
        }

        $expressionContext = clone $command->getExpressionContext();
        $expressionContext->offsetSet('element',  $element);

        $this->logger->debug(
            sprintf(
                'Evaluating "%s" on %s:"%s")',
                $expression,
                $element->getTagName(),
                $element->getText()
            )
        );

        $seed = new ScalarSeed(
            $this->language->evaluate(
                $expression,
                $expressionContext->getArrayCopy()
            )
        );

        return $command->setSeed($seed);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setRequired(self::EXPRESSION);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'evaluate_element';
    }
}
