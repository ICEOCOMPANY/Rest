<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 21.01.15
 * Time: 13:34
 */
namespace Helpers;

class Response extends \Phalcon\Http\Response{

    public function __construct(){
        $this->setContentType("application/json");
    }

    public function setJsonErrors($array){
        $this->setJson(array(
            "errors" => $array
        ));

        return $this;
    }

    public function setJson($data){
        $this->setContent(json_encode($data));
        return $this;
    }

    public function setConfirmOperationMessage($message){
        $this->setJson(array(
            "success" => $message
        ));

        return $this;
    }

    public function setStatusCode($code,$message){
        parent::setStatusCode($code,$message);
        return $this;
    }

    public function setCode($code){
        return $this->setStatusCode(
            $code,
            \Libs\StatusCodes::getMessageForCode($code)
        );
    }


}