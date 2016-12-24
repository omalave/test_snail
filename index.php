<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;
//DB Provider
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'dbs.options' => array (
        'dataApiConnectorMain' => array(
            'driver'   => 'pdo_mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'dbname'   => 'dbsnail',
            'user'     => 'root',
            'password' => '1q2w3e4r',
        )
    ),
));

//Twig Provider
$app->register(
    new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/web',
    'twig.autoescape' => true
    )
);

$app->get('/', function () use ($app) {

	return $app['twig']->render('home.view.twig');
});


$app->mount('/processData', new TestSnail\Test\ProcessDataController($app));

$app->run();

