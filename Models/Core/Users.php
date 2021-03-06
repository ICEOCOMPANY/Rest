<?php

namespace Models\Core;

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $registered;

    /**
     *
     * @var string
     */
    protected $password;


    protected $active;
    protected $permissions;

    protected $config = false;

    public function initialize()
    {
        //$this->hasMany("id", "Models\Core\UsersGroups", "user_id", array('alias' => 'groups'));
        $this->config = new \Configs\Core\Users();

        $this->setSource('users');
        $this->hasManyToMany(
            "id",
            "Models\Core\UsersGroups",
            "user_id",
            "group_id",
            "Models\Core\Groups",
            "id",
            array('alias' => 'groups')
        );
    }

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field registered
     *
     * @param string $registered
     * @return $this
     */
    public function setRegistered($registered = null)
    {
        if(!$registered)
            $registered = (new \DateTime())
                ->format(\Helpers\Consts::mysqlDateTimeColumnFormat);


        $this->registered = $registered;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        if(strlen($password) <= $this->config->getMinPasswordLength()){
            return NULL;
        } else {
            $this->password = \password_hash($password, PASSWORD_DEFAULT);
            return $this;
        }
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field registered
     *
     * @return string
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    public function getActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
        return $this;
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(
            new Uniqueness(
                array(
                    'field' => 'email'
                )
            )
        );

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    public function getSource()
    {
        return 'users';
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'email' => 'email', 
            'registered' => 'registered', 
            'password' => 'password',
            'permissions' => 'permissions',
            'active' => 'active'
        );
    }

}
