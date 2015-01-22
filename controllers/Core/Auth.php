<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 22.01.15
 * Time: 13:15
 */


namespace Controllers\Core;

use \Phalcon\Db\Column;

class Auth extends \Phalcon\Mvc\Controller {

    private $appSecretKey = "sFHePANXTQfhYprW7q2agtotD5YPNh";       // secret key which will encrypt/decrypt tokens
    private $tokenPermanence = "PT15M";                             // token permanence (DateInterval)

    public function createToken(){
        $response = new \Helpers\RestResponse();

        $parameters = array(
            "email" => $this->request->getPost("email")
        );

        $user = \Models\Core\Users::findFirst(array(
            "email = :email:",
            "bind" => $parameters
        ));


        if($user){
            $passwordVerify = \password_verify(
                $this->request->getPost("password"),
                $user->getPassword()
            );

            if($passwordVerify){
                $tokenModel = new \Models\Core\Tokens();

                $tokenModel->setUserId( $user->getId() );
                $token = \Libs\JWT::encode(
                    array( "email" => $user->getEmail() , "password" => $user->getPassword() ),
                    $this->appSecretKey
                );
                $tokenModel->setToken($token);

                $tokenModel->setExpirationTime(
                    (new \DateTime())->add(new \DateInterval($this->tokenPermanence))->format("Y-m-d H:i:s")
                );

                if($tokenModel->save()){
                    $response->setStatusCode("201","Token created");
                    $response->setJson(array("token"=>$token));
                }


            }else{
                $response->setStatusCode("401","Unauthorized");
                $response->setJsonErrors(array("password is invalid"));
            }

        }else{
            $response->setStatusCode("401","Unauthorized");
            $response->setJsonErrors(array("not found user with this email"));
        }

        return $response;
    }


    public function validateToken(){
        $response = false;

        $tokenModel = \Models\Core\Tokens::findFirst(array(
            "token = :token:",
            "bind" => array("token" => $this->request->getPost("token") )
        ));

        if($tokenModel){
            $tokenModel->setExpirationTime(
                (new \DateTime())->add(new \DateInterval($this->tokenPermanence))->format("Y-m-d H:i:s")
            );

            $tokenModel->save();

            $response = $tokenModel->getUserId();
        }

        return $response;


    }




} 