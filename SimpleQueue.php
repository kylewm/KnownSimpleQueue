<?php

namespace IdnoPlugins\SimpleQueue;

use Idno\Core\Idno;
use Idno\Core\EventQueue;

class SimpleQueue extends EventQueue
{

    function init()
    {
    }

    function enqueue($queueName, $eventName, array $eventData)
    {
        $job = new Job();
        $job->event = $eventName;
        $job->data = $this->convertEntitiesToUUIDs($eventData);
        $job->state = 'new';
        $job->save();

        Idno::site()->logging()->debug('sending to simple queue: ' . var_export($job, true));

        return $job->getUUID();
    }

    function isComplete($id)
    {
        $job = Job::getByUUID($id);
        return $job && $job->state === 'complete';
    }

    function getResult($id)
    {
        $job = Job::getByUUID($id);
        return $job ? $job->result : null;
    }

}