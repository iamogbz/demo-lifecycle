<?php

// configure your app for the production environment

$app['twig.path']    = array(__DIR__ . '/../templates');
$app['twig.options'] = array('cache' => __DIR__ . '/../var/cache/twig');

// Doctrine: DB options
$app['db.options'] = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/../sqlite/coding-challenge.db',
);