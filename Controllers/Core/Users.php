<?php
/**
 * Kontroler uzytkownikow
 *
 * Path: /Controllers/Core/Users.php
 * Created by PhpStorm.
 * Author: dawid
 * Date: 26.01.15
 * Time: 16:09
 */


namespace Controllers\Core;

class Users extends \Base\Controller {

    public function __construct(){
        $this->config = new \Configs\Core\Users();
        parent::__construct();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PUBLICZNE

    /**
     * POST
     * Tworzenie nowego uzytkownika
     *
     * @return \Helpers\Response
     */
    public function create(){

        // Tworze uzytkownika
        $user = new \Models\Core\Users();
        $user->setRegistered();

        $user->setActive(
            ($this->config->getRequireEmailActivation())?0:1
        );

        // Przypisuje dane do uzytkownika z posta
        $result = $this->setUserData($user);

        if($result){

            if($this->config->getRequireEmailActivation()){
                $activationKey = \Helpers\String::generateRandomString(20);

                $activationKeyModel = new \Models\Core\UsersActivationKeys();
                $activationKeyModel
                    ->setUserId($result)
                    ->setKey($activationKey)
                    ->save();


                $mailer = new \Helpers\Mailer();

                $vars = array(
                    'user_email' => $user->getEmail(),
                    'activation_key' => $activationKey
                );

                $mailer->SendTemplateEmail("registered",$vars,"",$user->getEmail(),"Success");

            }

        }

        return $this->response;
    }


    public function activateAccount(){

        $activateKey = \Models\Core\UsersActivationKeys::findFirst(array(
            "key = :key:",
            "bind" => array("key" => $this->request->getPostVar("key"))
        ));

        if($activateKey){
            $userId = $activateKey->getUserId();
            $activateKey->delete();

            $userModel = \Models\Core\Users::findFirst($userId);
            $userModel->setActive(1);

            if($userModel->save())
                $this->response->setConfirmOperationMessage(
                    $this->config->getMsgByCode(2)
                );

        }else
            $this->response
                ->setCode(406)
                ->setJsonErrors(array(
                        $this->config->getMsgByCode(1)
                    ));

        return $this->response;
    }

    /**
     * PUT
     * Edycja istniejcego uzytkownika
     *
     * @param $id - id uzytkownika
     * @return \Helpers\Response
     */
    public function edit($id){
        // TODO Zmienić na DI
        $logged_user = $this->getDI()->get("user")->getCurrentUserId();

        // Sprawdzam, czy zalogowany
        if (!$logged_user) {

            // Niezalogowany - zwracam blad
            $this->response
                ->setCode(401)
                ->setJsonErrors(array($this->config->getMsgByCode(3)));

        } else {

            // Zalogowany - lece dalej

            // Szukam uzytkownika po ID
            $user = \Models\Core\Users::findFirstById($id);

            if(!$user || $logged_user != $user->getId()){

                // Nie ma uprawnien do edycji - zwracam blad
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(4)
                    ));


            } else {

                // Ma uprawnienia do edycji - edytuje

                // przypisuje dane do uzytkownika z puta
                $this->setUserData($user);
            }
        }

        return $this->response;
    }

    /**
     * POST
     * prosba o wygenerowanie klucza resetowania hasla
     *
     * @param $email - email uzytkownika
     * @return \Helpers\Response
     */
    public function resetPasswordPOST(){

        // Filtruje email pod wzgledem poprawnosci
        //$email = (new \Phalcon\Filter())->sanitize($email, "email");
        $email = $this->request->getPostVar("email");

        // Szukam uzytkownika o podanym mailu
        $user = \Models\Core\Users::findFirstByEmail($email);

        if(!$user){

            // Nie znalazlo uzytkownika - zwracam blad
            $this->response
                ->setCode(404)
                ->setJsonErrors(
                    $this->config->getMsgByCode(5)
                );

        } else {

            // Znalazlo uzytkownika - ide dalej

            // Tworze klucz resetowania hasla
            $reset_key = new \Models\Core\PasswordsResetKeys();
            $reset_key ->setUserId($user->getId())
                       ->setResetKeyForUser($user->getId())
                       ->setExpirationTime();

            if (!$reset_key->save()) {

                // Nie udalo sie stworzyc klucza - zwracam blad
                $this->response
                    ->setCode(405)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(6)
                    ));

            } else {

                // Wszystko poszlo dobrze - zwracam klucz
                $this->response->setConfirmOperationMessage(
                    $this->config->getMsgByCode(10)
                );


                $mailer = new \Helpers\Mailer();

                $vars = array(
                    'user_email' => $user->getEmail(),
                    'key' => $reset_key->getResetKey()
                );

                $mailer->SendTemplateEmail("resetpassword",$vars,"",$user->getEmail(),"Success");


            }
        }
        return $this->response;
    }

    /**
     * PUT
     * Resetowanie hasla przy pomocy klucza
     *
     * @param $reset_key - klucz resetowania hasla
     *
     * @return \Helpers\Response
     */
    public function resetPasswordPUT(){

        $reset_key = $this->request->getPutVar('key');
        // Sprawdzam, czy klucz istnieje w bazie
        $reset_key = \Models\Core\PasswordsResetKeys::findFirst(array(
            "reset_key = :reset_key:",
            "bind" => array("reset_key" => $reset_key)
        ));

        if(!$reset_key){

            // Klucz nie istnieje - zwracam blad
            $this->response
                ->setCode(404)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(7)
                ));

        } else {

            // Klucz istnieje - lece dalej

            // Pobieram uzytkownika
            $user = \Models\Core\Users::findFirstById($reset_key->getUserId());

            if(!$user){

                // Nie znaleziono uzytkownika - zwracam blad
                $this->response
                    ->setCode(404)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(8)
                    ));

            } else {

                // Znaleziono uzytkownika - ide dalej

                // Ustawiam nowe haslo
                if(!$user->setPassword($this->request->getPutVar('new_password'))){
                    $this->response
                        ->setCode(409)
                        ->setJsonErrors(array(
                            $this->config->getMsgByCode(9)
                        ));
                } else {
                    if(!$user->save()){

                        // Nie udalo sie zmienic hasla - wyswietlam bledy
                        $errors = array();
                        //TODO Konwersja formatu błędów
                        foreach ($user->getMessages() as $message) {
                            $errors[] = $message->getMessage();
                        }

                        $this->response
                            ->setCode(409)
                            ->setJsonErrors($errors);
                    } else {
                        $reset_key->delete();
                        $this->response->setConfirmOperationMessage(
                            $this->config->getMsgByCode(11)
                        );
                    }
                }
            }
        }
        return $this->response;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PRYWATNE

    /**
     * Ustawia dane dla uzytkownika (dla posta i puta)
     *
     * @param $user - obiekt uzytkownia
     */
    private function setUserData($user){
        // Pobieram dane z posta
        foreach($this->request->getPost() as $key => $value){
            $setter_name = "set" . ucfirst($key);
            if(method_exists($user ,$setter_name)){
                $user->{$setter_name}($this->request->getPostVar($key));
            }
        }

        if (!$user->save()) {

            // Nie udalo sie zapisac - zwracam blad
            // TODO Konwersja formatu błędów
            $errors = array();
            foreach ($user->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $this->response
                ->setCode(405)
                ->setJson($errors);

            return false;
        } else {

            // Wszystko poszlo dobrze - zwracam ID
            $this->response
                ->setCode(200)
                ->setJson(array('id' => $user->getId()));

            return $user->getId();
        }
    }

}