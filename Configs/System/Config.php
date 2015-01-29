<?php
//define('APP_PATH', realpath(''));

date_default_timezone_set('UTC');

define('APP_PATH', realpath('..'));

return new \Phalcon\Config(array(

    'database' => array(
        'adapter'    => 'Mysql',
        'host'       => 'core.iceo.zone',
        'username'   => 'phalcon',
        'password'   => 'ZY5ZUsDTtee4UXQ8',
        'dbname'     => 'phalcon',
        'charset'    => 'utf8',
    ),

    'application' => array(
        'modelsDir'      => APP_PATH . '/Models/',
       // 'viewsDir'       => APP_PATH . '/Views/',
        'controllersDir' => APP_PATH . '/Controllers',
        'helpersDir'     => APP_PATH . '/Helpers',
        'configsDir'     => APP_PATH . '/Configs',
        'libsDir'        => APP_PATH . '/Libs',
        'baseDir'        => APP_PATH . '/Base',
        'baseUri'        => '/CoreBack/',
    )
));
