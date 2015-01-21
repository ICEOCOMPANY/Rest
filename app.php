<?php



$app->get("/person/{id:[0-9]+}",function($id) use ($app){
    $controller = new Person();
    $app->response = $controller->get($id);
});

/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
});
