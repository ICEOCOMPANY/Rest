<?php
/**
 * Kontroler grup
 *
 * Created by PhpStorm.
 * Author: dawid
 * Date: 28.01.15
 * Time: 19:51
 */


namespace Controllers\Core;

class Groups extends \Base\Controller {

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PUBLICZNE

    /**
     * POST
     * Tworzenie nowej grupy
     *
     * @return \Helpers\Response
     */
    public function create(){

        $logged_user = (new \Controllers\Core\Auth())->getCurrentUserId();

        // Sprawdzam, czy zalogowany
        if (!$logged_user) {

            // Niezalogowany - zwracam blad
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(\Helpers\Messages::notLoggedError));
        } else {

            // Tworze grupe
            $group = new \Models\Core\Groups();
            $group->setParent(0);
            $group->setOwnerId($logged_user);

            // Przypisuje dane do grupy z posta
            $this->setGroupData($group);
        }

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

        $can_edit = false;
        $logged_user = (new \Controllers\Core\Auth())->getCurrentUserId();

        // Sprawdzam, czy zalogowany
        if (!$logged_user) {

            // Niezalogowany - zwracam blad
            $this->response
                ->setCode(401)
                ->setJsonErrors(array(\Helpers\Messages::notLoggedError));
        } else {

            // Szukam grupy po ID
            $group = \Models\Core\Groups::findFirstById($id);

            if(!$group || $logged_user != $group->getOwnerId()){


                $administrator = \Models\Core\Groups::findFirst(array(
                    "group_id = :group_id: AND user_id = :user_id:",
                    "bind" => array(
                        "group_id" => $group->getId(),
                        "user_id" => $logged_user
                    )
                ));

                if(!$administrator){

                    // Nie ma uprawnien do edycji - zwracam blad
                    $this->response
                        ->setCode(401)
                        ->setJsonErrors(array(\Helpers\Messages::noPermissionsToEditError));
                } else {


                    // Ma uprawnienia do edycji - edytuje
                    $can_edit = true;
                }
            } else {

                // Ma uprawnienia do edycji - edytuje
                $can_edit = true;
            }
        }

        if($can_edit){

            // przypisuje dane do grupy z puta
            $this->setGroupData($group);
        }

        return $this->response;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PRYWATNE

    /**
     * Ustawia dane dla grupy (dla posta i puta)
     *
     * @param $group - obiekt grupy
     */
    private function setGroupData($group){
        // Pobieram dane z posta
        foreach($this->request->getPost() as $key => $value){
            $setter_name = "set" . ucfirst($key);
            if(method_exists($group ,$setter_name)){
                $group->{$setter_name}($this->request->getPostVar($key));
            }
        }

        if (!$group->save()) {

            // Nie udalo sie zapisac - zwracam blad
            $errors = array();
            foreach ($group->getMessages() as $message) {
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
                ->setJson(array("id" => $group->getId()));

            return $group->getId();
        }
    }

}