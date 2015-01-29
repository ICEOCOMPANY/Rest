<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 23.01.15
 * Time: 17:19
 */

namespace Base;


abstract class Controller {

    protected $request;

    protected $response;

    protected $config = false;

    public function __construct(){
        $this->request = new \Helpers\Request();
        $this->response = new \Helpers\Response();

        $configFileName = "\\Configs\\".get_class($this);

        if(class_exists($configFileName))
            $this->config = new $configFileName();

    }
} 