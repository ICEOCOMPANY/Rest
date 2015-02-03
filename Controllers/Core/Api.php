<?php
/**
 * Created by PhpStorm.
 * User: dunio
 * Date: 2015-02-03
 * Time: 17:43
 */

namespace Controllers\Core;


class Api  extends \Base\Controller  {

    public function init(){
        $this->config = new \Configs\Core\Api();
    }
    public function createApiKeys(){
        $logged_user = $this->getDI()->get("user")->getCurrentUserId();

        if (!$logged_user)
            return $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(3)));


        $model = new \Models\Core\ApiKeys();
        $model->setUserId($logged_user);

        $model->setPrivate(
            \Helpers\String::generateRandomString(
                $this->config->getPrivateKeyLength()
            )
        );

        $model->setPublic(
            \Helpers\String::generateRandomString(
                $this->config->getPubicKeyLength()
            )
        );

        $model->save();

        return $this->response->setJson(array(
            "public" => $model->getPublic(),
            "private" => $model->getPrivate()
        ));
    }

    public function deleteApiKeys(){
        $logged_user = $this->getDI()->get("user")->getCurrentUserId();

        if (!$logged_user)
            return $this->response
                ->setCode(401)
                ->setJsonErrors(array($this->config->getMsgByCode(3)));


        $model = \Models\Core\ApiKeys::findFirst($logged_user);

        if(!$model)
            return
                $this->response
                    ->setCode(404)
                    ->setJsonErrors(array(
                    $this->config->getMsgByCode(1)
                    ));

        if($model->delete())
            return
                $this->response
                    ->setConfirmOperationMessage(
                        $this->config->getMsgByCode(2)
                    );


        return
            $this->response->setJsonErrors(array(
                $this->config->getMsgByCode(3)
            ));

    }

    public function loginViaApi(){

        $publicKey = $this->request->getHeaderMod("X-Public");
        $sign = $this->request->getHeaderMod("X-Hash");

        if(!$publicKey || !$sign)
            return false;

        $model = \Models\Core\ApiKeys::findFirst(array(
            "public = :public:",
            "bind" => array("public" => $publicKey)
        ));

        if(!$model)
            return false;

        $dbSign = hash_hmac(
            $this->config->getHashMethod()
            , $model->getPublic()
            , $model->getPrivate()
        );
        if($dbSign != $sign)
            return false;

        //TODO docelowo zwraca tylko id
        return $this->response->setJson(array(
            "id"=>$model->getUserId()
        ));
    }
} 