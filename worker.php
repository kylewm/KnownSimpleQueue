<?php

namespace IdnoPlugins\SimpleQueue;

use Idno\Core\Idno;
use Idno\Core\Webservice;

require_once __DIR__ . '/../../Idno/start.php';

function getnextclean() {
    // random number between 0 and 1 day from now
    return time() + 24 * 60 * 60 * (rand() / getrandmax());
}

$joblife = 5 * 24 * 60 * 60;
$nextclean = getnextclean();

while (true) {

    // TODO it would be nice to fetch only the *first* job in
    // chronological order, instead of having to fetch all the jobs in
    // reverse chronological order
    $jobs = Job::get(['state' => 'new'], [], 1000);
    while ($jobs) {
        $job = array_pop($jobs);

        // note: this has to be getUUID rather than getURL. getURL relies on base URL being set.
        $joburl = $job->getUUID();

        error_log("sending job $joburl");

        $job->state = 'started';
        $job->save();

        $result = Webservice::post($joburl);

        if ($result['response'] < 200 || $result['response'] > 299) {
            error_log('error from /sq/job' . $result['response'] . ', ' . $result['content']);
            $job->state = 'error';
            $job->error = $result['content'];
            $job->save();
        }
    }

    if (time() >= $nextclean) {
        $toremove = [];

        for ($offset = 0; ; $offset += 1000) {
            $jobs = Job::get(['state' => ['$not' => 'new']], [], 1000, $offset);
            if (!$jobs) {
                break;
            }

            foreach ($jobs as $job) {
                if (time() - $job->published > $joblife) {
                    $toremove[] = $job;
                }
            }
        }

        foreach ($toremove as $job) {
            $job->delete();
        }

        $nextclean = getnextclean();
    }

    sleep(5);
}
