<?php

namespace Tz7\WebScraper\WebDriver;


interface WebElementSelectAdapterInterface
{
    const TYPE_CSS_SELECTOR      = 'css_selector';
    const TYPE_ID                = 'id';
    const TYPE_NAME              = 'name';
    const TYPE_LINK_TEXT         = 'link_text';
    const TYPE_PARTIAL_LINK_TEXT = 'partial_link_text';
    const TYPE_TAG_NAME          = 'tag_name';
    const TYPE_XPATH             = 'xpath';

    /**
     * @return mixed
     */
    public function getSelector();

    /**
     * @return string
     */
    public function __toString();
}
