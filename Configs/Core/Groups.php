<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 30.01.15
 * Time: 13:37
 */

namespace Configs\Core;

class Groups extends \Base\Config{

    function __construct(){
        $this
            ->newMsg(1,"No permission to edit")
            ->newMsg(3,"You are not logged")
        ;
    }


} 