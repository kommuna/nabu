<?php

// Config and modules auto-loading
$config = require '/etc/eropic.nabu/config.php';
$loader = require './../vendor/autoload.php';

ini_set('error_log', $config['log']['path']);
date_default_timezone_set($config['app']['timezone']);

use \Controllers\APICategoriesController as CC;
use Core\Application;


/*
 * Slim initialisation
 */
$app = new \Slim\Slim($config['app']);
$app->appConfig = $config;


//Init logger
$app->container->singleton('log', function() {

    $app = \Slim\Slim::getInstance();
    $log = new \Monolog\Logger('slim');
    $log->pushHandler(new \Monolog\Handler\StreamHandler($app->appConfig['log']['path'], \Monolog\Logger::DEBUG));
    return $log;

});

//Error listner
$app->error('Core\Application::error');
$app->notFound('Core\Application::notFound');


/********************************************
 * CONTROLLERS
 */

/*
 * Default route conditions
 */


$app->get('/test', function() {
    phpinfo();
});


/****************************************************************************
 *                              VIDEOS's ROUTES                              *
 ****************************************************************************/


$app->get('/categories/:id', function($id) {
    (new CC())->getItem($id);
});

$app->put('/categories/:id', function($id) {
    (new CC())->updateItem($id);
});

$app->delete('/categories/:id', function($id) {
    (new CC())->markItemAsDeleted($id);
});

$app->get('/categories', function() {
    (new CC())->getList();
});

$app->post('/categories', function() {
    (new CC())->addItem();
});


/*
$app->get('/items/:id', function($id) {
    (new IC())->getItem($id);
});

$app->put('/items/:id', function($id) {
    (new IC())->updateItem($id);
});

$app->delete('/items/:id', function($id) {
    (new IC())->markItemAsDeleted($id);
});

$app->get('/items', function() {
    (new IC())->getList();
});

$app->post('/items', function() {
    (new IC())->addItem();
});



$app->post('/items/:id/votes_up', function($id) {
    (new IC())->increaseViewCounter($id);
});

$app->post('/items/:id/votes_down', function($id) {
    (new IC())->increaseViewCounter($id);
});

$app->post('/items/:id/views', function($id) {
    (new IC())->increaseViewCounter($id);
});

*/



$app->run();