<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    "Controllers" => $config->application->controllersDir,
    "Helpers" => $config->application->helpersDir,
    "Configs" => $config->application->configsDir,
    "Models" => $config->application->modelsDir,
    "Libs" => $config->application->libsDir,
    "Base" => $config->application->baseDir
));

$loader->register();
