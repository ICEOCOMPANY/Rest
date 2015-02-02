<?php
namespace Models\Core;

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;

class PasswordsResetKeys extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     *
     * @var string
     */
    protected $key;

    /**
     *
     * @var string
     */
    protected $expiration_time;

    protected $config = false;

    public function initialize()
    {
        //$this->hasMany("id", "Models\Core\UsersGroups", "user_id", array('alias' => 'groups'));
        $this->config = new \Configs\Core\PasswordResetKeys();


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
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field key
     *
     * @param string $key
     * @return $this
     */
    public function setResetKey($reset_key)
    {

        $this->reset_key = $reset_key;

        return $this;
    }

    /**
     * Method to set the value of field key
     *
     * @param string $key
     * @return $this
     */
    public function setResetKeyForUser($user_id)
    {

        /*
        $this->reset_key = sha1(
            $user_id .
            \Helpers\Consts::appSecretKey .
            (new \DateTime())->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
        );

        */

        $this->reset_key = \Helpers\String::generateRandomString(
            $this->config->getResetKeyLength()
        );
        return $this;
    }

    /**
     * Method to set the value of field expiration_time
     *
     * @param string $expiration_time
     * @return $this
     */
    public function setExpirationTime($expiration_time = null)
    {

        if(!$expiration_time)
            $expiration_time = (new \DateTime())
                ->add(new \DateInterval($this->config->getResetKeyPermanence()))
                ->format(\Helpers\Consts::mysqlDateTimeColumnFormat);

        $this->expiration_time = $expiration_time;

        return $this;
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
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field key
     *
     * @return string
     */
    public function getResetKey()
    {
        return $this->reset_key;
    }

    /**
     * Returns the value of field expiration_time
     *
     * @return string
     */
    public function getExpirationTime()
    {
        return $this->expiration_time;
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(
            new Uniqueness(
                array(
                    'field' => 'reset_key'
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'reset_key' => 'reset_key',
            'expiration_time' => 'expiration_time'
        );
    }

}
