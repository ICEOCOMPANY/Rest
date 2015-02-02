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
$app->post("/users/password", function() use ($app){
    $app->response = (new \Controllers\Core\Users())->resetPasswordPOST();
});

// Zapisywanie nowego hasła bazująca na kluczu
$app->put("/users/password", function() use ($app){
    $app->response = (new \Controllers\Core\Users())->resetPasswordPUT();
});



/**
 * GROUPS
 */
// Tworzy nowa grupe
$app->post("/groups",function() use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->create();
});

// Zmiana danych grupy
$app->put("/groups/{id:[0-9]+}",function($id) use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->edit($id);
});

// Dodanie użytkownika do grupy
$app->post("/groups/{groupId:[0-9]+}/users/{userId:[0-9]+}",function($groupId,$userId) use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->addToGroup($userId,$groupId);
});

// Usunięcie użytkownika z grupy
$app->delete("/groups/{groupId:[0-9]+}/users/{userId:[0-9]+}",function($groupId,$userId) use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->removeUserFromGroup($userId,$groupId);
});

// Nadanie użytkownikowi praw administratora w grupie
$app->post("/groups/{groupId:[0-9]+}/admins/{userId:[0-9]+}",function($groupId,$userId) use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->makeAdministrator($userId,$groupId);
});

// Usunięcie uprawnień administratora
$app->delete("/groups/{groupId:[0-9]+}/admins/{userId:[0-9]+}",function($groupId,$userId) use ($app){
    $app->response = (new \Controllers\Core\Groups($app))->removeAdmin($userId,$groupId);
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