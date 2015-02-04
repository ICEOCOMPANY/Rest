<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 22.01.15
 * Time: 13:15
 */


namespace Controllers\Core;

class Auth extends \Base\Controller {

    public function init(){
        $this->config = new \Configs\Core\Auth();
    }


    public function createToken(){

        $email =  $this->request->getPostVar("email");
        $password = $this->request->getPostVar("password");

        $user = \Models\Core\Users::findFirst(array(
            "email = :email:",
            "bind" => array("email" => $email)
        ));

        if($user){
            $passwordVerify = \password_verify(
                $password,
                $user->getPassword()
            );

            if($passwordVerify){
                $tokenModel = new \Models\Core\Tokens();

                $tokenModel->setUserId( $user->getId() );
                $token = \Libs\JWT::encode(
                    array(
                        "email" => $user->getEmail() ,
                        "ip"=> $this->request->getClientAddress(),
                        "time" => time()
                    ),
                    $this->config->getAppSecretKey()
                );


                $tokenModel->setToken($token);

                $tokenModel->setExpirationTime(
                    (new \DateTime())
                        ->add(new \DateInterval(
                            $this->config->getTokenPermanence()
                        ))
                        ->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
                );


                if($tokenModel->save())
                    $this->response
                        ->setCode(201)
                        ->setJson(array("token"=>$token));


            }else
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(1)
                    ));


        }else
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(2)
                ));


        return $this->response;
    }

    public function getCurrentUserId(){
        $token = $this->request->getHeaderMod('Authorization');

        if(!$token)
            return false;

        $response = false;
        $tokenIp = \Libs\JWT::decode(
            $token,
            $this->config->getAppSecretKey()
        );

        if($tokenIp->ip != $this->request->getClientAddress())
            return false;

        $tokenModel = \Models\Core\Tokens::findFirst(array(
            "token = :token:",
            "bind" => array("token" =>  $token)
        ));

        if($tokenModel){

            $now = (new \DateTime())
                ->format(\Helpers\Consts::mysqlDateTimeColumnFormat);

            if(strtotime($tokenModel->getExpirationTime()) < strtotime($now)){
                $tokenModel->delete();
                return false;
            }

            $tokenModel->setExpirationTime(
                (new \DateTime())
                    ->add(new \DateInterval($this->config->getTokenPermanence()))
                    ->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
            );

            $tokenModel->save();

            $response = $tokenModel->getUserId();
        }

        return $response;
    }


    public function getCurrentUser(){
        $currentUser = $this->getDI()->get("user");

        if($currentUser->getId()){
            $userModel = $currentUser->getModel();

            $this->response->setJson(array(
                "id" => $userModel->getId(),
                "email" => $userModel->getEmail(),
                "registered" => $userModel->getRegistered(),
                "groups" => $userModel->getGroups()->toArray(),
                "active" => ($userModel->getActive()==1)?true:false,
                "permissions" => $currentUser->getPermissions()
            ));

        }else
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(3)
                ));

        return $this->response;
    }

    public function destroyToken(){

        $token = $this->request->getHeaderMod('Authorization');

        if(!$token){
            $this->response
                ->setCode(405)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(4)
                ));
        }

        $tokenModel = \Models\Core\Tokens::findFirst(array(
            "token = :token:",
            "bind" => array("token" =>  $token)
        ));

        if(!$tokenModel)
            $this->response
                ->setCode(405)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(5)
                ));
        elseif( $tokenModel->delete() )
            $this->response->setConfirmOperationMessage(
                $this->config->getMsgByCode(6)
            );
        else
            $this->response->setCode(503);


        return $this->response;
    }
}