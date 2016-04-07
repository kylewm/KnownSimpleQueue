<?php

namespace IdnoPlugins\SimpleQueue;

use Idno\Common\Plugin;
use Idno\Core\Idno;

class Main extends Plugin
{

    function init()
    {
        parent::init();
        Idno::site()->queue = new SimpleQueue();
    }

    function registerPages()
    {
        Idno::site()->addPageHandler('sq/job/([a-zA-Z0-9]+)', '\IdnoPlugins\SimpleQueue\Pages\Job');
    }

}