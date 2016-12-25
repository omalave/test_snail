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

$app->register(new Silex\Provider\SessionServiceProvider());

$app->get('/', function () use ($app) {


    if (empty($app['session']->get('token'))) {
        
        $app['session']->set('token', bin2hex(random_bytes(32)));
    }

    $token = $app['session']->get('token');

	return $app['twig']->render('home.view.twig', array('token' => $token ));
});


$app->mount('/snail', new TestSnail\Test\ProcessDataController($app));

$app->run();

