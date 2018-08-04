<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;


/**
 * Click on the current or a newly selected element under the current DOM element.
 */
class Click extends ElementSelectAbstract
{
    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $element  = $command->getElementStack()->top();

        if ($this->hasSelector($command))
        {
            $element = $element->findElement(
                $this->createSelector($command)
            );
        }

        $element->click();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'click';
    }
}
