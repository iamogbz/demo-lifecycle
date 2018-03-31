<?php

// configure your app for the production environment
use Silex\Provider\DoctrineServiceProvider;

$app['twig.path']    = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');

// Doctrine: DB options
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__ . '/../sqlite/coding-challenge.db',
    ),
));
