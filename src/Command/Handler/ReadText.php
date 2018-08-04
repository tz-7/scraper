<?php

namespace Tz7\WebScraper\Command\Handler;


use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\ScalarSeed;


class ReadText extends ElementSelectAbstract
{
    /**
     * @inheritdoc
     */
    protected function execute(Command $command)
    {
        $element = $command->getElementStack()->top();

        if ($this->hasSelector($command))
        {
            $element = $element->findElement(
                $this->createSelector($command)
            );
        }

        $seed = new ScalarSeed($element->getText());

        return $command->setSeed($seed);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'read_text';
    }
}
