<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 29.01.15
 * Time: 12:46
 */

namespace Configs\Core;


class Api extends \Base\Config{

    private $pubicKeyLength = 20;
    private $privateKeyLength = 20;
    private $hashMethod = "sha256";

    /**
     * @return int
     */
    public function getPrivateKeyLength()
    {
        return $this->privateKeyLength;
    }

    /**
     * @return int
     */
    public function getPubicKeyLength()
    {
        return $this->pubicKeyLength;
    }

    /**
     * @return string
     */
    public function getHashMethod()
    {
        return $this->hashMethod;
    }



    function __construct(){
        $this
            ->newMsg(1,"This user has no access keys")
            ->newMsg(2,"Keys have been removed")
            ->newMsg(3,"Unknown error")
            ->newMsg(4,"No permission to create keys")
            ->newMsg(5,"No permission to delete keys")
        ;
    }
} 