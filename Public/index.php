<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 86400");

use Phalcon\Mvc\Micro;


try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../Configs/System/Config.php";

    /**
     * Include Services
     */
    include APP_PATH . '/Configs/System/Services.php';

    /**
     * Include Autoloader
     */
    include APP_PATH . '/Configs/System/Loader.php';

    /**
     * Starting the application
     * Assign service locator to the application
     */
    $app = new Micro($di);


    require_once(APP_PATH . "/userDI.php");


    $app->options("/{route1}[/]?{route2}[/]?{route3}", function() use ($app){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization");
        header("Access-Control-Max-Age: 3628800");
    });



    /**
     * Include Application
     */
    include APP_PATH . '/app.php';

    /**
     * Handle the request
     */
    $app->handle();
    $app->response->send();

} catch (\Exception $e) {
    echo $e->getMessage();
}
