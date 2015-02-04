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

    public function __construct($app){
        $this->config = new \Configs\Core\Groups();
        parent::__construct($app);
    }

    /**
     * POST
     * Tworzenie nowej grupy
     *
     * @return \Helpers\Response
     */
    public function create(){

        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("CREATE_GROUPS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(17)));



        // Tworze grupe
        $group = new \Models\Core\Groups();
        $group->setParent(0);
        $group->setOwnerId($user->getId());

        // Przypisuje dane do grupy z posta
        $this->setGroupData($group,$this->request->getPost());


        return $this->response;
    }


    /**
     * PUT
     * Edycja istniejącej grupy
     *
     * @param $id - id uzytkownika
     * @return \Helpers\Response
     */
    public function edit($id){

        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("CREATE_GROUPS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(3)));



        // Szukam grupy po ID
        $group = \Models\Core\Groups::findFirstById($id);

        if(!$group)
            return $this->response->setCode(404)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(2)
                ));

        if($this->checkPermissionToEdit(
            $id,
            $user->getId(),
            $group
        )){

            // przypisuje dane do grupy z puta
            $this->setGroupData($group,$this->request->getPut());
        }

        return $this->response;
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PRYWATNE

    /**
     * Ustawia dane dla grupy (dla posta i puta)
     *
     * @param $group - obiekt grupy
     */
    private function setGroupData($group , $data){
        // Pobieram dane z posta
        foreach($data as $key => $value){
            $setter_name = "set" . ucfirst($key);
            if(method_exists($group ,$setter_name)){
                $group->{$setter_name}($value);
            }
        }

        if (!$group->save()) {

            // Nie udalo sie zapisac - zwracam blad
            // TODO Konwersja formatu błędów
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
                ->setJson( array("id" => $group->getId()) );

            return $group->getId();
        }
    }


    public function addToGroup($userId,$groupId){

        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("ADD_GROUP_MEMBERS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(14)));


        // Szukam grupy po ID
        $group = \Models\Core\Groups::findFirstById($groupId);

        if(!$group)
            return $this->response->setCode(404)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(2)
                ));

        if(
            $this->checkPermissionToEdit(
                $groupId,
                $user->getId(),
                $group
            )
        ){

            if(
            (new \Models\Core\UsersGroups())
                ->setUserId($userId)
                ->setGroupId($group->getId())
                ->save()
            )
                $this->response
                    ->setConfirmOperationMessage(
                        $this->config->getMsgByCode(4)
                    );
            else
                $this->response
                    ->setCode(418)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(5)
                    ));

        }

        return $this->response;
    }


    public function removeUserFromGroup($userId,$groupId){
        $can_edit = false;

        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("REMOVE_GROUP_MEMBERS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(15)));

        // Szukam grupy po ID
        $group = \Models\Core\Groups::findFirstById($groupId);

        if(!$group)
            return $this->response->setCode(404)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(2)
                ));

        if(
        $this->checkPermissionToEdit(
            $groupId,
            $user->getId(),
            $group
        )
        ){
            $model = \Models\Core\UsersGroups::findFirst(array(
                "user_id = :user: AND group_id = :group:",
                "bind" => array("user" =>  $userId , "group" => $groupId)
            ));

            if(!$model)
                return $this->response
                    ->setCode(409)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(7)
                    ));
            if($model->delete())
                return $this->response
                    ->setConfirmOperationMessage(
                        $this->config->getMsgByCode(6)
                    );
            else
                return $this->response
                    ->setCode(418)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(5)
                    ));

        }

        return $this->response;
    }

    private function checkPermissionToEdit($groupId,$userId,$group = -1){
        $can_edit = false;
        // Szukam grupy po ID
        if(!$group === -1)
            $group = \Models\Core\Groups::findFirstById($groupId);

        if($userId != $group->getOwnerId()){

            $administrator = \Models\Core\Groups::findFirst(array(
                "group_id = :group_id: AND user_id = :user_id:",
                "bind" => array(
                    "group_id" => $group->getId(),
                    "user_id" => $userId
                )
            ));

            if(!$administrator){
                // Nie ma uprawnien do edycji - zwracam blad
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(1)));
            } else {
                // Ma uprawnienia do edycji - edytuje
                $can_edit = true;
            }
        } else {

            // Ma uprawnienia do edycji - edytuje
            $can_edit = true;
        }

        return $can_edit;
    }


    public function makeAdministrator($userId,$groupId){


        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("MANAGE_GROUP_ADMINS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(16)));


        $group = \Models\Core\Groups::findFirstById($groupId);

        if(!$group)
            return $this->response->setCode(404)
                    ->setJsonErrors(array(
                        $this->config->getMsgByCode(2)
                    ));


        if($group->getOwnerId() != $user->getId())
            return $this->response->setCode(401)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(11)
                ));

        $userInGroup = $group->getUsers(array(
            'user_id = :id:',
            'bind' => array('id' => $userId)
        ));

        if(count($userInGroup) === 0)
            return $this->response->setCode(409)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(8)
                ));

        $adminGroup = \Models\Core\GroupsAdministrators::findFirst(array(
            'user_id = :user_id: AND group_id = :group_id:',
            'bind' => array('user_id' => $userId,'group_id'=>$groupId)
        ));

        if($adminGroup)
            return $this->response
                ->setCode(409)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(10)
                ));


        $adminModel = new \Models\Core\GroupsAdministrators();

        $adminModel->setUserId($userId)->setGroupId($groupId);

        if($adminModel->save())
            return
                $this->response->setConfirmOperationMessage(
                    $this->config->getMsgByCode(9)
                );

        else
            return
                $this->response->setCode(409)
                    ->setConfirmOperationMessage(
                        $this->config->getMsgByCode(5)
                    );
    }

    public function removeAdmin($userId,$groupId){

        $user = $this->getDI()->get("user");

        if (!$user->checkPermission($this->getPermissions()->get("MANAGE_GROUP_ADMINS")))
            return
                $this->response
                    ->setCode(401)
                    ->setJsonErrors(array($this->config->getMsgByCode(16)));


        $group = \Models\Core\Groups::findFirstById($groupId);

        if(!$group)
            return $this->response->setCode(404)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(2)
                ));


        if($group->getOwnerId() != $user->getId())
            return $this->response->setCode(401)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(11)
                ));

       $adminGroup = \Models\Core\GroupsAdministrators::findFirst(array(
            'user_id = :user_id: AND group_id = :group_id:',
            'bind' => array('user_id' => $userId,'group_id'=>$groupId)
        ));

        if(!$adminGroup)
            return $this->response
                ->setCode(409)
                ->setJsonErrors(array(
                    $this->config->getMsgByCode(12)
                ));


        if($adminGroup->delete())
            return
                $this->response->setConfirmOperationMessage(
                    $this->config->getMsgByCode(13)
                );

        else
            return
                $this->response->setCode(409)
                    ->setConfirmOperationMessage(
                        $this->config->getMsgByCode(5)
                    );
    }
}