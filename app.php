<?php

$userDataContainer = new stdClass();
$userDataContainer->id = (new \Controllers\Core\Auth())->getCurrentUserId();

$app->getDI()->set("user",$userDataContainer);



$app->post("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->createToken();
});

$app->delete("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->destroyToken();
});

$app->get("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->getCurrentUser(
        $app->getDI()->get("user")->id
    );
});

$app->options("/remind", function() use ($app){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT");
    header("Access-Control-Allow-Headers: X-Custom-Header");
});

$app->put("/remind", function() use ($app){
    $reponse = new Phalcon\Http\Response();
    $response->setStatusCode("204","No Content");
    $response->setJson(array("message"=>"OK nigga"));
});

/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found");
});
