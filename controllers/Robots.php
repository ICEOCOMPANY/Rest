<?php

namespace controllers;

class Robots
{
    public function get($id){

        $model = \Models\Robots::find();
        $data = array();
        foreach($model as $person){
            array_push($data,
                array(
                    "name" => $person->getName(),
                    "type" => $person->getType()
                )
            );
        }
        $response = new \Helpers\RestResponse();
        $response->setJson($data);
        return $response;


    }

}
