<?php

class Person
{
    public function get($id){
        //echo $id;
        $model = Robots::find();



        $response = array();
        foreach($model as $person){
            echo $person->getName() ." <br> ";

            array_push($response,
                array(
                    "name" => $person->getName(),
                    "type" => $person->getType(),

                )
            );
        }

    }

}
