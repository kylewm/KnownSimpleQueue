<?php

namespace IdnoPlugins\SimpleQueue;

use Idno\Common\Entity;
use Idno\Core\Idno;

class Job extends Entity
{

    function save()
    {
        // set a unique slug, or they'll all b called "idnopluginssimplequeuejob-n"
        $this->setSlug('sq-job-' . md5(rand(0,9999) . microtime(true)));
        parent::save();
    }

    function getURL()
    {
        if ($this->_id) {
            return Idno::site()->config()->getURL() . 'sq/job/' . $this->_id;
        }
        return false;
    }

}