<?php

namespace Tz7\WebScraper\Command\Handler;


use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ArraySeed;
use Tz7\WebScraper\WebDriver\WebElementSelectAdapterInterface;
use UnexpectedValueException;


/**
 * Handles a form submit.
 */
class FormSubmit extends Handler
{
    const FORM     = 'form';
    const FIELDS   = 'fields';
    const EVALUATE = 'evaluate';
    const SUBMIT   = 'submit';
    const OFFSET_X = 'offset_x';
    const OFFSET_Y = 'offset_y';

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
        $this->prepareFormData($command);

        $driver          = $command->getDriver();
        $selectorFactory = $driver->getSelectorFactory();
        $element         = $command->getElementStack()->top();
        $formSelector    = $command->getConfigBy(self::FORM);
        $submitSelector  = $command->getConfigBy(self::SUBMIT);

        $form = $element->findElement(
            $selectorFactory->createByType(
                $this->getSelectorType($formSelector, WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR),
                $this->getCleanedSelector($formSelector)
            )
        );

        if ($form === null)
        {
            throw new UnexpectedValueException('Form not found by selector: ' . $formSelector);
        }

        $submit = $form->findElement(
            $selectorFactory->createByType(
                $this->getSelectorType($submitSelector, WebElementSelectAdapterInterface::TYPE_CSS_SELECTOR),
                $this->getCleanedSelector($submitSelector)
            )
        );

        $options = $command->getConfig();
        $root    = $driver->submit($form, $submit, $options[self::FIELDS]);

        $command->getElementStack()->reset($root);
        $command->appendNavigationHistory($driver->getCurrentURL());

        return $command->setSeed(null);
    }

    /**
     * @param Command $command
     */
    private function prepareFormData(Command $command)
    {
        $options = $command->getConfig();

        if (isset($options[self::PREPARED_BY]))
        {
            $seed = $command->getSeed();

            if ($seed instanceof ArraySeed && $seed->getData()->offsetExists(self::FIELDS))
            {
                $options[self::FIELDS] = array_merge(
                    $options[self::FIELDS],
                    $seed->getData()->offsetGet(self::FIELDS)
                );
            }
        }

        if (!empty($options[self::EVALUATE]))
        {
            $expressionContext = $command->getExpressionContext()->getArrayCopy();

            foreach ($options[self::EVALUATE] as $name => $expression)
            {
                $options[self::FIELDS][$name] = $this->language->evaluate($expression, $expressionContext);
            }
        }

        $command->setConfig($options);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setDefaults(
                [
                    self::SLEEP_AFTER => 1000,
                    self::FIELDS      => [],
                    self::EVALUATE    => []
                ]
            )
            ->setRequired(
                [
                    self::FORM,
                    self::SUBMIT
                ]
            )
            ->setAllowedTypes(self::FIELDS, 'array')
            ->setAllowedTypes(self::EVALUATE, 'array');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'form_submit';
    }
}
