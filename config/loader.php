<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    "Controllers" => $config->application->controllersDir,
    "Helpers" => $config->application->helpersDir,
    "Models" => $config->application->modelsDir
));

$loader->register();
