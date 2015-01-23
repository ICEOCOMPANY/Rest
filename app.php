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

/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found");
});
