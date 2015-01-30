<?php

/**
 * AUTH
 */
// Loguje uzytkownika
$app->post("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth($app))->createToken();
});

// Wylogowywuje uzytkownika
$app->delete("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth($app))->destroyToken();
});

// Jesli uzytkownik jest zalogowany, to zwraca adres e-mail
$app->get("/auth",function() use ($app){
    $app->response = (new \Controllers\Core\Auth())->getCurrentUser(
        $app->getDI()->get("user")->getCurrentUserId()
    );
});



/**
 * USERS
 */
// Tworzy nowego uzytkownika
$app->post("/users",function() use ($app){
    $app->response = (new \Controllers\Core\Users())->create();
});

// aktywacja uzytkownika
$app->post("/users/activation",function() use ($app){
    $app->response = (new \Controllers\Core\Users())->activateAccount();
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
 * GROUPS
 */
// Tworzy nowa grupe
$app->post("/groups",function() use ($app){
    $app->response = (new \Controllers\Core\Groups())->create();
});




/**
 * FILES
 */
$app->post("/files",function() use ($app){
    $app->response = (new \Controllers\Core\Files())->upload();
});

$app->get("/files/{id}",function($id) use ($app){
    $app->response = (new \Controllers\Core\Files())->info($id);
});

$app->get("/files/{id}/download",function($id) use ($app){
    $app->response = (new \Controllers\Core\Files())->download($id);
});


/**
 * Not found handler
 */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found");
});