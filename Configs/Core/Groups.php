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
            ->newMsg(1,'No permission to edit')
            ->newMsg(2,'Group not found')
            ->newMsg(3,'You are not logged')
            ->newMsg(4,'User added to group')
            ->newMsg(5,'Unknown error')
            ->newMsg(6,'User removed from group')
            ->newMsg(7,'User does not belongs to group')
            ->newMsg(8,'User have to be a group member to make him admin')
            ->newMsg(9,'This user is now admin')
            ->newMsg(10,'This user is already admin')
            ->newMsg(11,'No Permission to make admins')
            ->newMsg(12,'Provided user is not admin')
            ->newMsg(13,'Provided user is no longer admin')
        ;
    }


} 