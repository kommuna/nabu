<?php

namespace Core;

use \Nabu\Exceptions\APIException;

class Application {

    static public function notFound() {
        $app = \Slim\Slim::getInstance();
        $params = $app->request->get();
        $app->log->addWarning('Not found url: ' . $app->request->getPath() . ($params ? " GET params: ". print_r($params,1) : ''));
    }

    static public function error(\Exception $e) {

        $app = \Slim\Slim::getInstance();

        if($e instanceof APIException) {

            $app->log->addError("API error [{$e->getHTTPCode()}][{$e->getCode()}]: ".print_r($e->getErrors(),1));
            $app->halt($e->getHTTPCode(), json_encode(['error' => $e->getErrors()], JSON_FORCE_OBJECT));


        } else {
            $app->log->addError("API error [500][{$e->getCode()}]: {$e->getMessage()} \n {$e->getTraceAsString()}");
            $app->halt(500);
        }

    }
}