<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;


class RewindContextElementStack extends Handler
{
    const STEPS = 'steps';

    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $elementStack = $command->getElementStack();
        $last         = $elementStack->top();
        $steps        = $command->getConfigBy(self::STEPS);

        for ($i = 0; $i < $steps; $i++)
        {
            $element = $elementStack->pop();

            if ($element)
            {
                $this->logger->debug($this->getName() . ' Step back from element <' . $element->getTagName() . '>');
                $last = $element;
            }
            else
            {
                $this->logger->error($this->getName() . ' Stack underflow error.');
                $elementStack->append($last);

                break;
            }
        }

        $this->logger->info($this->getName() . ' Current top of stack: <' . $elementStack->top()->getTagName() . '>');
    }

    /**
     * @inheritdoc
     */
    protected function getOptionsResolver()
    {
        return parent::getOptionsResolver()
            ->setDefault(self::STEPS, 1)
            ->setAllowedTypes(self::STEPS, 'int');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rewind_context_element_stack';
    }
}
