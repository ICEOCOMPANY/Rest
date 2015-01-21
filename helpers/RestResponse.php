<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 21.01.15
 * Time: 13:34
 */
namespace Helpers;

class RestResponse extends \Phalcon\Http\Response{

    public function __construct(){
        $this->setContentType("application/json");
    }

    public function setJson($data){
        $this->setContent(json_encode($data));
    }


}