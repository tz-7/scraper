<?php

namespace Tz7\WebScraper\Browser\Buzz\Util;


use Buzz\Util\Cookie;
use Buzz\Util\CookieJar as CookieJarBase;


class CookieJar extends CookieJarBase
{
    /**
     * @inheritdoc
     */
    public function addCookie(Cookie $cookie)
    {
        $this->removeCookieWithName($cookie->getName());

        parent::addCookie($cookie);
    }

    /**
     * @param string $name
     */
    private function removeCookieWithName($name)
    {
        foreach ($this->cookies as $i => $cookie)
        {
            if ($cookie->getName() === $name)
            {
                unset($this->cookies[$i]);
            }
        }

        $this->cookies = array_values($this->cookies);
    }
}
