<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 04.02.15
 * Time: 11:08
 */

namespace Configs\Core;


class Permissions  extends \Base\Config{

    protected $list = array(
        'UPDATE_PROFILE' => 1,
        'UPLOAD_FILE' => 2,
        'CREATE_API_KEYS' => 4,
        'REMOVE_API_KEYS' => 5,
        'CREATE_GROUPS' => 6,
        'ADD_GROUP_MEMBERS' => 7,
        'REMOVE_GROUP_MEMBERS' => 8,
        'MANAGE_GROUP_ADMINS' => 9,
    );

    public function getDefaultForRegistered(){
        return array();
    }

    public function getDefaultForActivated(){
        return array(
            $this->get("UPDATE_PROFILE"),
            $this->get("UPLOAD_FILE"),
            $this->get("CREATE_API_KEYS"),
            $this->get("REMOVE_API_KEYS"),
            $this->get("CREATE_GROUPS"),
            $this->get('ADD_GROUP_MEMBERS'),
            $this->get('MANAGE_GROUP_ADMINS')
        );
    }

    public function getDefaultForApi(){
        return array(
            $this->get("UPLOAD_FILE")
        );
    }

    public function get($key){
        return $this->list[$key];
    }
} 