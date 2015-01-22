<?php



$app->get("/person",function() use ($app){
    $controller = new \Controllers\Core\Person();
    $app->response = $controller->get();
});

$app->post("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->post();

});

/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found");
});
