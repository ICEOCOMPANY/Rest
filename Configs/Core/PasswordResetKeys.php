<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 02.02.15
 * Time: 13:06
 */

namespace Configs\Core;


class PasswordResetKeys {
    private $resetKeyPermanence = "PT30M";                    // token permanence (DateInterval)
    private $resetKeyLength = 20;

    /**
     * @return int
     */
    public function getResetKeyLength()
    {
        return $this->resetKeyLength;
    }

    /**
     * @return string
     */
    public function getResetKeyPermanence()
    {
        return $this->resetKeyPermanence;
    }




} 