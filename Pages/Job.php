<?php

namespace IdnoPlugins\SimpleQueue\Pages;

use Idno\Core\Idno;
use Idno\Common\Page;

class Job extends Page
{

    function getContent()
    {
        if (empty($this->arguments)) {
            $this->setResponse(404);
            echo 'Missing job ID';
            return;
        }

        $jobid = $this->arguments[0];
        $job = \IdnoPlugins\SimpleQueue\Job::getByID($jobid);

        if (!$job) {
            $this->setResponse(404);
            echo "No job with ID $jobid";
            return;
        }

        $t = Idno::site()->template();
        $t->setTemplateType('json');
        $t->__([
            'event' => $job->event,
            'data' => $job->data,
            'result' => $job->result,
            'error' => $job->error,
            'state' => $job->state,
        ])->drawPage();
    }

    function post()
    {
        $arguments = func_get_args();
        if (empty($arguments)) {
            $this->setResponse(404);
            echo 'Missing job ID';
            return;
        }

        $jobid = $arguments[0];
        Idno::site()->logging()->debug("running SimpleQueue job: $jobid");
        $job = \IdnoPlugins\SimpleQueue\Job::getByID($jobid);

        if (!$job) {
            $this->setResponse(404);
            echo "No job with ID $jobid";
            return;
        }

        if ($job->state === 'complete') {
            $this->setResponse(400);
            echo "Job already complete $jobid";
            return;
        }

        Idno::site()->session()->logUserOn($job->getOwner());
        $eventdata = Idno::site()->queue()->convertUUIDsToEntities($job->data);
        $result = Idno::site()->triggerEvent($job->event, $eventdata);

        $job->result = $result;
        $job->state = 'complete';
        $job->save();
    }

}