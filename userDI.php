<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 30.01.15
 * Time: 16:29
 */

class UserDi{

    private $currentUserId = -1;
    public function getCurrentUserId($forceReCache = false){
        if($this->currentUserId === -1 || $forceReCache)
            $this->currentUserId = (new \Controllers\Core\Auth())->getCurrentUserId();

        return $this->currentUserId;
    }

    private $currentUserModel = -1;

    public function getCurrentUserModel($forceReCache = false){

        if($this->currentUserModel === -1 || $forceReCache)
            $this->currentUserModel = \Models\Core\Users::findFirstById(
                $this->getCurrentUserId($forceReCache)
            );

        return $this->currentUserModel;
    }


}

/**
 * Wstrzyknięcie obiektu user w globalny DI $app z lazy loadem.
 */
$app->getDI()->set("user",function(){
    return new UserDi();
});