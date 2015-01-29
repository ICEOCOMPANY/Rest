<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 29.01.15
 * Time: 12:46
 */

namespace Configs\Controllers\Core;


class Auth extends \Base\Config{

    private $appSecretKey = "sFHePANXTQfhYprW7q2agtotD5YPNh"; // secret key which will encrypt/decrypt tokens
    private $tokenPermanence = "PT15M";                       // token permanence (DateInterval)
    private $resetKeyPermanence = "PT30M";                    // token permanence (DateInterval)
    private $minPasswordLength = 8;                           // mysql DateTime column format'

    function __construct(){
        $this
            ->newMsg(1,"password is invalid")
            ->newMsg(2,"not found user with this email")
            ->newMsg(3,"you are not logged")
            ->newMsg(4,"token not given")
            ->newMsg(5,"token incorrect")
            ->newMsg(6,"Logout Successful")
        ;
    }

    /**
     * @return string
     */
    public function getAppSecretKey()
    {
        return $this->appSecretKey;
    }

    /**
     * @return int
     */
    public function getMinPasswordLength()
    {
        return $this->minPasswordLength;
    }

    /**
     * @return string
     */
    public function getResetKeyPermanence()
    {
        return $this->resetKeyPermanence;
    }

    /**
     * @return string
     */
    public function getTokenPermanence()
    {
        return $this->tokenPermanence;
    }





} 