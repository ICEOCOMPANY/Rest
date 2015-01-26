<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 23.01.15
 * Time: 17:19
 */

namespace Base;


class Controller {

    protected $request;

    protected $response;

    public function __construct(){
        $this->request = new \Helpers\Request();
        $this->response = new \Helpers\Response();
    }
} 