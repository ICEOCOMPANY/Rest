<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 30.01.15
 * Time: 16:29
 */

class UserDi{

    private $currentUserId = -1;
    private $loggedViaApiKeys = false;
    private $apiPermissions = array();

    /**
     * @param bool $forceReCache
     * @return bool|int
     */
    public function getId($forceReCache = false){
        if($this->currentUserId === -1 || $forceReCache){
            $id = (new \Controllers\Core\Auth())->getCurrentUserId();

            if(!$id){
                $api = (new \Controllers\Core\Api())->loginViaApi();
                if($api){
                    $this->currentUserId = $api['id'];
                    $this->apiPermissions = $api['permissions'];
                    $this->loggedViaApiKeys = true;
                }

            }else{
                $this->loggedViaApiKeys = false;
                $this->currentUserId = $id;
            }




        }
        return $this->currentUserId;
    }

    private $currentUserModel = -1;

    /**
     * @param bool $forceReCache
     * @return \Models\Core\Users
     */
    public function getModel($forceReCache = false){

        if($this->currentUserModel === -1 || $forceReCache)
            $this->currentUserModel = \Models\Core\Users::findFirstById(
                $this->getId($forceReCache)
            );

        return $this->currentUserModel;
    }

    public function getPermissions(){

        if($this->getLoggedViaApiKeys())
            return $this->apiPermissions;
        else
            return $this->getModel()->getPermissions();


    }

    public function checkPermission($key){
        return in_array($key,$this->getPermissions());
    }

    /**
     * @return boolean
     */
    public function getLoggedViaApiKeys()
    {
        if($this->getId())
            return $this->loggedViaApiKeys;
        return false;
    }



}

/**
 * Wstrzyknięcie obiektu user w globalny DI $app z lazy loadem.
 */
$app->getDI()->set("user",function(){
    return new UserDi();
});