<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 22.01.15
 * Time: 13:15
 */


namespace Controllers\Core;

class Auth extends \Base\Controller {

    public function createToken(){

        $email =  $this->request->getPostVar("email");
        $password = $this->request->getPostVar("password");

        $this->response
            ->setCode(201)
            ->setJson(array("msg"=>$email));
        return;
        
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
                        "ip"=> $this->request->getClientAddress()
                    ),
                    \Helpers\Consts::appSecretKey
                );


                $tokenModel->setToken($token);

                $tokenModel->setExpirationTime(
                    (new \DateTime())
                        ->add(new \DateInterval(\Helpers\Consts::tokenPermanence))
                        ->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
                );


                if($tokenModel->save())
                    $this->response
                        ->setCode(201)
                        ->setJson(array("token"=>$token));


            }else
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array("password is invalid"));


        }else
            $this->response
                ->setCode(401)
                ->setJsonErrors(array("not found user with this email"));


        return $this->response;
    }

    public function getCurrentUserId(){
        $token = $this->request->getHeaderMod('Authorization');

        if(!$token)
            return false;

        $response = false;
        $tokenIp = \Libs\JWT::decode(
            $token,
            \Helpers\Consts::appSecretKey
        );

        if($tokenIp != $this->request->getClientAddress())
            return $this->response->setCode(403)->setJsonErrors(array("security attack!"));


        $tokenModel = \Models\Core\Tokens::findFirst(array(
            "token = :token:",
            "bind" => array("token" =>  $token)
        ));

        if($tokenModel){
            $tokenModel->setExpirationTime(
                (new \DateTime())
                    ->add(new \DateInterval(\Helpers\Consts::tokenPermanence))
                    ->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
            );

            $tokenModel->save();

            $response = $tokenModel->getUserId();
        }

        return $response;
    }


    public function getCurrentUser($id){
        if($id){

            $userModel = \Models\Core\Users::findFirst($id);
            $this->response->setJson(array(
                "email" => $userModel->getEmail()
            ));

        }else
            $this->response
                ->setCode(401)
                ->setJsonErrors(array("you are not logged"));


        return $this->response;
    }

    public function destroyToken(){

        $token = $this->request->getHeaderMod('Authorization');

        if(!$token){
            $this->response
                ->setCode(405)
                ->setJsonErrors(array("token not given"));
        }

        $tokenModel = \Models\Core\Tokens::findFirst(array(
            "token = :token:",
            "bind" => array("token" =>  $token)
        ));

        if(!$tokenModel)
            $this->response
                ->setCode(405)
                ->setJsonErrors(array(
                    "token not right"
                ));
        elseif( $tokenModel->delete() )
            $this->response->setConfirmOperationMessage("logout");
        else
            $this->response->setCode(503);


        return $this->response;
    }

} 