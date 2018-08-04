<?php

namespace Tz7\WebScraper\Factory;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Handler\Callback;
use Tz7\WebScraper\Command\Handler\Click;
use Tz7\WebScraper\Command\Handler\CommandSequence;
use Tz7\WebScraper\Command\Handler\ConditionalIfStatement;
use Tz7\WebScraper\Command\Handler\ConditionalSwitchStatement;
use Tz7\WebScraper\Command\Handler\ElementSequence;
use Tz7\WebScraper\Command\Handler\EvaluateElement;
use Tz7\WebScraper\Command\Handler\FormSubmit;
use Tz7\WebScraper\Command\Handler\Handler;
use Tz7\WebScraper\Command\Handler\MapElement;
use Tz7\WebScraper\Command\Handler\Navigate;
use Tz7\WebScraper\Command\Handler\ReadAttribute;
use Tz7\WebScraper\Command\Handler\ReadText;
use Tz7\WebScraper\Command\Handler\RewindContextElementStack;
use Tz7\WebScraper\Command\Handler\SeedExpressionContext;


class HandlerCollectionFactory
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ExpressionLanguage */
    private $language;

    /**
     * @param LoggerInterface    $logger
     * @param ExpressionLanguage $language
     */
    public function __construct(LoggerInterface $logger, ExpressionLanguage $language)
    {
        $this->logger   = $logger;
        $this->language = $language;
    }

    /**
     * @return Handler[]
     */
    public function getCommands()
    {
        return [
            new Callback($this->logger),
            new Click($this->logger),
            new CommandSequence($this->logger),
            new ConditionalIfStatement($this->logger, $this->language),
            new ConditionalSwitchStatement($this->logger, $this->language),
            new ElementSequence($this->logger),
            new EvaluateElement($this->logger, $this->language),
            new FormSubmit($this->logger, $this->language),
            new MapElement($this->logger),
            new Navigate($this->logger, $this->language),
            new ReadAttribute($this->logger),
            new ReadText($this->logger),
            new RewindContextElementStack($this->logger),
            new SeedExpressionContext($this->logger)
        ];
    }
}
