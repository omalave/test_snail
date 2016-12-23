<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function () use ($app) {
    return 'Hello ';
});

$app->mount('/processData', new TestSnail\Test\ProcessDataController($app));

$app->run();
