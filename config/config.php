<?php
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
        'modelsDir'      => APP_PATH . '/models/',
        'viewsDir'       => APP_PATH . '/views/',
        'controllersDir' => APP_PATH . '/controllers',
        'baseUri'        => '/CoreBack/',
    )
));
