<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 29.01.15
 * Time: 13:16
 */

namespace Base;


abstract class Config {

    private $errors = array();

    public function getMsgByCode($code){
        return (array_key_exists($code,$this->errors))?
            array("code"=>$code,"message"=>$this->errors[$code])
            : false ;
    }
    protected function newMsg($code,$message){
        $this->errors[$code] = $message;
        return $this;
    }
} 