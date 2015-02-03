<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 23.01.15
 * Time: 17:19
 */

namespace Base;


abstract class Controller{

    protected $request;

    protected $response;

    protected $config = false;

    public function __construct($app = false){
        $this->request = new \Helpers\Request();
        $this->response = new \Helpers\Response();


        /**
         * Autoloading config file for controller - turned off
         */
        /*
        $configFileName = "\\Configs\\".get_class($this);
        if(class_exists($configFileName))
            $this->config = new $configFileName();
*/

        if($app != false)
            $this->setDI(
                $app->getDI()
            );

        $this->init();
    }

    function init(){}

    protected $_di;

    public function setDI(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->_di = $dependencyInjector;
        return $this;
    }

    public function getDI()
    {
        return $this->_di;
    }


}