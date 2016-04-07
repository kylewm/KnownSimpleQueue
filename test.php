<?php

require_once __DIR__ . '/external/aws.phar';

$QUEUE_URL = 'https://sqs.us-west-2.amazonaws.com/785341941021/known_kylewm_com';
$ACCESS_KEY_ID = 'AKIAINER3ADIIYRBKKJQ';
$SECRET_ACCESS_KEY = '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';

$client = new \Aws\Sqs\SqsClient([
    'credentials' => new \Aws\Credentials\Credentials($ACCESS_KEY_ID, $SECRET_ACCESS_KEY),
    'region'      => 'us-west-2',
    'version'     => '2012-11-05',
]);

$client->sendMessage([

    'MessageBody' => 'Hello World!',
    'QueueUrl'    => $QUEUE_URL,

]);
