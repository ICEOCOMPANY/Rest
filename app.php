<?php

$userDataContainer = new stdClass();
$userDataContainer->id = (new \Controllers\Core\Auth())->getCurrentUserId();

$app->getDI()->set("user",$userDataContainer);

$app->options("/{type}", function() use ($app){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Authorization");
});

$app->options("/{type}/{route1}", function() use ($app){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Authorization");
});

$app->options("/{type}/{route1}/{route2}", function() use ($app){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Authorization");
});

// Loguje uzytkownika
$app->post("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->createToken();
});

// Wylogowywuje uzytkownika
$app->delete("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->destroyToken();
});

// Jesli uzytkownik jest zalogowany, to zwraca adres e-mail
$app->get("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->getCurrentUser(
        $app->getDI()->get("user")->id
    );
});

// Tworzy nowego uzytkownika
$app->post("/users",function() use ($app){
    $app->response = (new \Controllers\Core\Users())->create();
});

// Edycja uzytkownika
$app->put("/users/{id:[0-9]+}",function($id) use ($app){
    $app->response = (new \Controllers\Core\Users())->edit($id);
});

// Generuje klucz resetowania hasla
$app->post("/users/{email}/reset-password", function($email) use ($app){
    $app->response = (new \Controllers\Core\Users())->resetPasswordPOST($email);
});

// Resetuje haslo uzytkownika
// TODO: Zdecydowac, ktory link bedzie lepszy pod wzgledem REST'a
// TODO: Sprawdzic jak dziala metoda PUT w angularze
$app->put("/users/{email}/reset-password/{reset_key}", function($email,$reset_key) use ($app){
    $app->response = (new \Controllers\Core\Users())->resetPasswordPUT($reset_key);
});
$app->put("/users/reset-password/{reset_key}", function($reset_key) use ($app){
    $app->response = (new \Controllers\Core\Users())->resetPasswordPUT($reset_key);
});

/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found");
});