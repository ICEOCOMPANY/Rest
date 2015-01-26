<?php
/**
 * Created by PhpStorm.
 * Author: dawid
 * Date: 26.01.15
 * Time: 16:09
 */


namespace Controllers\Core;

class Users extends \Base\Controller {

    /**
     * Tworzenie nowego uzytkownika
     *
     * @return \Helpers\Response
     */
    public function create(){

        // Tworze uzytkownika
        $user = new \Models\Core\Users();
        $user->setRegistered();

        // Przypisuje dane do uzytkownika z posta
        $this->setUserData($user, "post");

        return $this->response;
    }

    /**
     * Edycja istniejcego uzytkownika
     *
     * @param $id - id uzytkownika
     * @return \Helpers\Response
     */
    public function edit($id){

        $logged_user = (new \Controllers\Core\Auth())->getCurrentUserId();

        // Sprawdzam, czy zalogowany
        if (!$logged_user) {

            // Niezalogowany - zwracam blad
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(\Helpers\Messages::notLoggedError));
        } else {

            // Zalogowany - lece dalej

            // Szukam uzytkownika po ID
            $user = \Models\Core\Users::findFirstById($id);

            if(!$user || $logged_user != $user->getId()){

                // Nie ma uprawnien do edycji - zwracam blad
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array(\Helpers\Messages::noPermissionsToEditError));
            } else {

                // Ma uprawnienia do edycji - edytuje

                // przypisuje dane do uzytkownika z puta
                $this->setUserData($user, "put");
            }
        }

        return $this->response;
    }

    /**
     * Ustawia dane dla uzytkownika (dla posta i puta)
     *
     * @param $user - obiekt uzytkownia
     * @param string $method - metoda (domyslnie post)
     */
    private function setuserData($user, $method = "post"){
        // Pobieram dane z posta
        foreach($this->request->{"get".ucfirst($method)}() as $key => $value){
            $setter_name = "set" . ucfirst($key);
            if(method_exists($user ,$setter_name)){
                $user->{$setter_name}($this->request->getPostVar($key));
            }
        }

        if (!$user->save()) {

            // Nie udalo sie zapisac - zwracam blad
            $errors = array();
            foreach ($user->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $this->response
                ->setCode(405)
                ->setJson($errors);

        } else {

            // Wszystko poszlo dobrze - zwracam ID
            $this->response
                ->setCode(201)
                ->setJson(array("id" => $user->getId()));
        }
    }


    /**
     * prosba o wygenerowanie klucza resetowania hasla
     *
     * @param $email - email uzytkownika
     * @return \Helpers\Response
     */
    public function resetPasswordPOST($email){

        // Filtruje email pod wzgledem poprawnosci
        $email = (new \Phalcon\Filter())->sanitize($email, "email");

        // Szukam uzytkownika o podanym mailu
        $user = \Models\Core\Users::findFirstByEmail($email);

        if(!$user){

            // Nie znalazlo uzytkownika - zwracam blad
            $this->response
                ->setCode(404)
                ->setJsonErrors([
                    'user not exists'
                ]);
        } else {

            // Znalazlo uzytkownika - ide dalej

            // Tworze klucz resetowania hasla
            $reset_key = new \Models\Core\PasswordsResetKeys();
            $reset_key->setUserId($user->getId());
            $reset_key->setResetKeyForUser($user->getId());
            $reset_key->setExpirationTime();

            if (!$reset_key->save()) {

                // Nie udalo sie stworzyc klucza - zwracam blad
                $this->response
                    ->setCode(405)
                    ->setJsonErrors([
                        'too many requests'
                    ]);
            } else {

                // Wszystko poszlo dobrze - zwracam klucz
                $this->response
                    ->setJson(array("reset_key" => $reset_key->getResetKey()));
            }
        }
        return $this->response;
    }

    /**
     * Resetowanie hasla przy pomocy klucza
     *
     * @param $reset_key - klucz resetowania hasla
     *
     * @return \Helpers\Response
     */
    public function resetPasswordPUT($reset_key){

        // Sprawdzam, czy klucz istnieje w bazie
        $reset_key = \Models\Core\PasswordsResetKeys::findFirst(array(
            "reset_key = :reset_key:",
            "bind" => array("reset_key" => $reset_key)
        ));

        if(!$reset_key){

            // Klucz nie istnieje - zwracam blad
            $this->response
                ->setCode(404)
                ->setJsonErrors([
                    'reset key not found'
                ]);
        } else {

            // Klucz istnieje - lece dalej

            // Pobieram uzytkownika
            $user = \Models\Core\Users::findFirstById($reset_key->getUserId());

            if(!$user){

                // Nie znaleziono uzytkownika - zwracam blad
                $this->response
                    ->setCode(404)
                    ->setJsonErrors([
                        'user not found'
                    ]);
            } else {

                // Znaleziono uzytkownika - ide dalej

                // Ustawiam nowe haslo
                if(!$user->setPassword($this->request->getPutVar("password"))){
                    $this->response
                        ->setCode(409)
                        ->setJsonErrors([
                            "password requirements not fulfilled"
                        ]);
                } else {
                    if(!$user->save()){

                        // Nie udalo sie zmienic hasla - wyswietlam bledy
                        $errors = array();
                        foreach ($user->getMessages() as $message) {
                            $errors[] = $message->getMessage();
                        }

                        $this->response
                            ->setCode(409)
                            ->setJsonErrors($errors);
                    } else {
                        // Wszystko poszlo dobrze
                    }
                }
            }
        }
        return $this->response;
    }

}