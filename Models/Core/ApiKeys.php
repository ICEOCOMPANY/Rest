<?php
/**
 * Created by PhpStorm.
 * User: dunio
 * Date: 2015-02-03
 * Time: 17:40
 */

namespace Models\Core;


class ApiKeys extends \Phalcon\Mvc\Model{
    protected $user_id;
    protected $public;
    protected $private;
    protected $permissions;
    /**
     * @return mixed
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @param mixed $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * @return mixed
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * @param mixed $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function beforeSave(){
        $this->permissions = join(",",$this->permissions);
    }

    public function afterFetch(){
        if($this->permissions == "")
            $this->permissions = array();
        else
            $this->permissions = explode(",",$this->permissions);
    }

    public function checkPermission($key){
        return in_array($key,$this->permissions);
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'user_id',
            'public' => 'public',
            'private' => 'private',
            'permissions' => 'permissions'
        );
    }



}