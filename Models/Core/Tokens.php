<?php

namespace Models\Core;

class Tokens extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     *
     * @var string
     */
    protected $token;

    /**
     *
     * @var string
     */
    protected $expiration_time;

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
     * Method to set the value of field token
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Method to set the value of field expiration_time
     *
     * @param string $expiration_time
     * @return $this
     */
    public function setExpirationTime($expiration_time)
    {
        $this->expiration_time = $expiration_time;

        return $this;
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
     * Returns the value of field token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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

    public function getSource()
    {
        return 'tokens';
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'user_id', 
            'token' => 'token', 
            'expiration_time' => 'expiration_time'
        );
    }

}
