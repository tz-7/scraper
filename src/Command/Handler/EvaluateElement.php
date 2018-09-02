<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\Response\ScalarSeed;
use Tz7\WebScraper\Response\Seed;


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

        $result = $this->language->evaluate(
            $expression,
            $expressionContext->getArrayCopy()
        );

        return $command->setSeed($this->getSeedForEvaluatedResult($result));
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

    /**
     * @param mixed $result
     *
     * @return Seed
     */
    private function getSeedForEvaluatedResult($result)
    {
        if (is_array($result))
        {
            return new ArraySeed($result);
        }

        return new ScalarSeed($result);
    }
}
