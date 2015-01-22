<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 21.01.15
 * Time: 19:29
 */

namespace controllers\Core;


class Person {
    public function get(){
        $data = array("what you got" => "person ;)");
        $response = new \Helpers\RestResponse();
        $response->setJson($data);
        return $response;
    }
} 