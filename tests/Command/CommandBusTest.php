<?php

namespace Tz7\WebScraper\Test\Command;


use ArrayObject;
use League\Tactician\CommandBus;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\Locator\InMemoryLocator;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\NullLogger;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Command\Middleware\CommandHandlerMiddleware;
use Tz7\WebScraper\Command\Middleware\NormalizerMiddleware;
use Tz7\WebScraper\Command\Middleware\PlantationMiddleware;
use Tz7\WebScraper\Command\Middleware\RedirectCheckMiddleware;
use Tz7\WebScraper\Command\Middleware\ScreenshotMiddleware;
use Tz7\WebScraper\Command\Middleware\TreeWalkMiddleware;
use Tz7\WebScraper\ExpressionLanguage\ExpressionLanguageProvider;
use Tz7\WebScraper\Factory\HandlerCollectionFactory;
use Tz7\WebScraper\Request\Context;
use Tz7\WebScraper\Request\ElementStack;
use Tz7\WebScraper\Request\History;
use Tz7\WebScraper\WebDriver\FacebookWebDriver\FacebookWebElementSelectAdapter;
use Tz7\WebScraper\WebDriver\WebDriverAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementAdapterInterface;
use Tz7\WebScraper\WebDriver\WebElementSelectorFactoryInterface;


class CommandBusTest extends CommandTestAbstract
{
    public function testComplexExecution()
    {
        $config = [
            'command'      => 'element_sequence',
            'sequence'     => '#sequence-selector',
            'on_each'      => [
                'command' => 'map_element',
                'key'     => [
                    'command'    => 'evaluate_element',
                    'expression' => 'element.getText()'
                ],
                'value'   => [
                    'command'    => 'evaluate_element',
                    'expression' => 'element.getText() ~ seeded'
                ]
            ],
            'prepared_by'  => [
                'command'      => 'read_attribute',
                'selector'     => '#attribute-selector',
                'attribute'    => 'attribute',
                'processed_by' => [
                    'command' => 'seed_expression_context',
                    'key'     => 'seeded'
                ]
            ]
        ];

        /** @var FacebookWebElementSelectAdapter|PHPUnit_Framework_MockObject_MockObject $attributeSelector */
        $attributeSelector = $this
            ->getMockBuilder(FacebookWebElementSelectAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var FacebookWebElementSelectAdapter|PHPUnit_Framework_MockObject_MockObject $sequenceSelector */
        $sequenceSelector = $this
            ->getMockBuilder(FacebookWebElementSelectAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var WebElementSelectorFactoryInterface|PHPUnit_Framework_MockObject_MockObject $selectorFactory */
        $selectorFactory = $this
            ->getMockBuilder(WebElementSelectorFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $selectorFactory
            ->expects($this->exactly(2))
            ->method('createByType')
            ->willReturnOnConsecutiveCalls(
                $attributeSelector,
                $sequenceSelector
            );

        /** @var WebDriverAdapterInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this
            ->getMockBuilder(WebDriverAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $driver
            ->method('getSelectorFactory')
            ->willReturn($selectorFactory);

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $top */
        $elementWithAttribute = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $elementWithAttribute
            ->expects($this->once())
            ->method('getAttribute')
            ->with('attribute')
            ->willReturn('returned attribute');

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $top */
        $firstElement = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $firstElement
            ->expects($this->atLeast(2))
            ->method('getText')
            ->willReturn('text1');

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $top */
        $secondElement = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $secondElement
            ->expects($this->atLeast(2))
            ->method('getText')
            ->willReturn('text2');

        /** @var WebElementAdapterInterface|PHPUnit_Framework_MockObject_MockObject $top */
        $top = $this
            ->getMockBuilder(WebElementAdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $top
            ->expects($this->once())
            ->method('findElement')
            ->with($attributeSelector)
            ->willReturn($elementWithAttribute);
        $top
            ->expects($this->once())
            ->method('findElements')
            ->with($sequenceSelector)
            ->willReturn([$firstElement, $secondElement]);

        $expressionContext = new ArrayObject();
        $elementStack      = new ElementStack();
        $elementStack->reset($top);

        $command = new Command(
            new Context($driver, '', $expressionContext, $config),
            new History([], $elementStack)
        );

        $bus = $this->buildCommandBus();

        $bus->handle($command);

        print_r($command->getSeed()->getData());

        $this->assertTrue($expressionContext->offsetExists('seeded'));
    }

    /**
     * @return CommandBus
     */
    private function buildCommandBus()
    {
        return new CommandBus(
            [
                new ScreenshotMiddleware(new NullLogger()),
                new TreeWalkMiddleware(),
                new NormalizerMiddleware(),
                new PlantationMiddleware(),
                new RedirectCheckMiddleware(),
                new CommandHandlerMiddleware($this->buildHandlerLocator())
            ]
        );
    }

    /**
     * @return HandlerLocator
     */
    private function buildHandlerLocator()
    {
        $logger = new Logger('test', [new StreamHandler('php://stdout')]);

        $collection = (
        new HandlerCollectionFactory(
            $logger,
            new ExpressionLanguage(
                null,
                [
                    new ExpressionLanguageProvider()
                ]
            )
        )
        )->getCommands();

        $locator = new InMemoryLocator();

        foreach ($collection as $handler)
        {
            $locator->addHandler($handler, $handler->getName());
        }

        return $locator;
    }
}
