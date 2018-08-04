<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;


class Navigate extends Handler
{
    const URL = 'url';

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
        $url = $this->language->evaluate(
            $command->getConfigBy(self::URL),
            $command->getExpressionContext()->getArrayCopy()
        );

        $this->logger->debug(sprintf('Navigate to "%s"', $url));
        $driver = $command->getDriver();
        $root   = $driver->get($url);

        $command->getElementStack()->reset($root);
        $command->appendNavigationHistory($driver->getCurrentURL());

        $urlContext = preg_quote($url, '/');
        $this->logger->debug(sprintf('Set url context to "%s"', $url));
        $command->setUrlContextPattern($urlContext);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()->setRequired(self::URL);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'navigate';
    }
}
