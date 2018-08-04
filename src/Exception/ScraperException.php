<?php

namespace Tz7\WebScraper\Exception;


use Exception;


class ScraperException extends Exception
{
    /** @var string */
    private $screenShot;

    /**
     * ScraperException constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param string    $screenShot
     * @param Exception $previous
     */
    public function __construct($message, $code, $screenShot, Exception $previous)
    {
        parent::__construct($message, $code, $previous);

        $this->screenShot = $screenShot;
    }

    /**
     * @return string
     */
    public function getScreenShot()
    {
        return $this->screenShot;
    }
}
