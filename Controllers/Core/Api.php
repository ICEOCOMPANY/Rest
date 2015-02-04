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
        $logged_user = $this->getDI()->get("user");

        if (!$logged_user->checkPermission($this->getPermissions()->get("CREATE_API_KEYS")))
            return $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(4)));


        $model = new \Models\Core\ApiKeys();
        $model->setUserId($logged_user->getId());

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

        $model->setPermissions(
           $this->getPermissions()->getDefaultForApi()
        );

        $model->save();

        return $this->response->setJson(array(
            "public" => $model->getPublic(),
            "private" => $model->getPrivate()
        ));
    }

    public function deleteApiKeys(){
        $logged_user = $this->getDI()->get("user");

        if (!$logged_user->checkPermission($this->getPermissions()->get("CREATE_API_KEYS")))
            return $this->response
                ->setCode(401)
                ->setJsonErrors(array($this->config->getMsgByCode(5)));


        $model = \Models\Core\ApiKeys::findFirst($logged_user->getId());

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
            'public = :public:',
            'bind' => array('public' => $publicKey)
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

        return array(
            'id' => $model->getUserId(),
            'permissions' => $model->getPermissions()
        );
    }
} 