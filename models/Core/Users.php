<?php

namespace Models\Core;

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\StringLength as StringLength;

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
        if(strlen($password) < \Helpers\Consts::minPasswordLength){
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'email' => 'email', 
            'registered' => 'registered', 
            'password' => 'password'
        );
    }

}
