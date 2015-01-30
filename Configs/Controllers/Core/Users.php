<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 30.01.15
 * Time: 12:41
 */

namespace Configs\Controllers\Core;


class Users extends \Base\Config{
    private $requireEmailActivation = true;

    function __construct(){
        $this
            ->newMsg(1,"Activation key not found")
            ->newMsg(2,"Account activated")
            ->newMsg(3,"You are not logged")
            ->newMsg(4,"You have no permissions to edit this data")
            ->newMsg(5,"User not exists")
            ->newMsg(6,"Too many requests. Try to request again after a while")
            ->newMsg(7,"Password reset key not found or is expired. Try to generate new.")
            ->newMsg(8,"User fulfilling the criteria not found.")
            ->newMsg(9,"Password requirements not fulfilled. Password must contains at least 8 characters.")

        ;
    }


    public function getRequireEmailActivation(){
        return $this->requireEmailActivation;
    }
} 