<?php

namespace AdminModule\SettingsModule;

use \AdminModule\NetteExtension\Form;

/**
 * Allows to edit administration users
 *
 * @author Kryštof Selucký
 * @package DarkAdmin
 */
class UsersPresenter extends \AdminModule\Security\SecuredPresenter {

    private $facade;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Users") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->userFacade;
    }

    /**
     * Sends info about all users to the template
     */
    public function renderDefault() {
        $this->template->users = $this->facade->getAllUsers();
    }

    /**
     * Deletes one user
     * @param int $userId users id
     */
    public function handleDeleteUser($userId) {
        if ($this->isAllowed("Users", "deleteUsers")) {
            $userName = $this->facade->getUser($userId)->getUserName();
            $this->facade->deleteUserById($userId);
            $this->addEvent("Uživatel ".$this->loggedUserName." smazal uživatele ".$userName.".");
            if ($this->isAjax()) {
                $this->invalidateControl("usersTable");
            } else {
                $this->redirect("this");
            }
        }
    }

    /**
     * Renders form for editing users
     * @return Form form ready to render
     */
    public function createComponentEditUserForm() {
        $form = new Form();

        $form->addText("realName", NULL)->setRequired("Jméno uživatele musí být vyplněno!");
        $form->addText("userName", NULL)->setRequired("Přihlašovací jméno uživatele musí být vyplněno");
        $form->addText("email", NULL)->addRule(Form::EMAIL, "E-mail má neplatný formát!")->setRequired("E-mail musí být vyplněn!");
        $roles = $this->facade->getRolesInPairsIdName();
        if ($this->userRole != "admin") {
            foreach ($roles as $id => $role) { //TODO: opravit - prasárna
                if ($role == "admin")
                    unset($roles["id"]);
            }
        }
        $form->addSelect("role", NULL, $roles)->setRequired("Uživatel musí mít přidělenou roli!");
        $form->addHidden("userId", NULL, "0");
        $form->addSubmit("submit", "Upravit");

        $form["realName"]->getControlPrototype()->class = "realName";
        $form->getElementPrototype()->id = 'editUserForm1';
        $form->getElementPrototype()->class = "ajax";

        $form->onSuccess[] = callback($this, "editUserFormSubmitted");

        return $form;
    }

    /**
     * Proccess submitted form for editing users
     * @param Form $form submitted form
     */
    public function editUserFormSubmitted(Form $form) {
        $values = $form->getValues();
        $role = $this->facade->getRole($values->role);
        $origRole = $this->facade->getUser($values->userId)->getRole()->getName();
        if (($origRole == "admin" && $this->userRole == "admin") || $origRole != "admin") {
            if ($role->getName() != "admin" || ($role->getName() == "admin" && $this->userRole == "admin")) {
                if ($this->facade->isDuplicateAfterChange($values->userId, $values->userName, $values->email) == false) {
                    if ($this->isAllowed("Users", "editUsers")) {
                        $this->facade->updateUser($values->userId, array("role" => $role->getId(), "userName" => $values->userName, "realName" => $values->realName, "email" => $values->email));
                        $this->addEvent("Uživatel ".$this->loggedUserName." upravil údaje uživatele ".$values->userName.".");
                        $this->flashMessage("Údaje uživatele úspěšně upraveny", "success");
                    }
                    else
                        $this->flashMessage("Nemáte právo upravovat údaje uživatelů administrace!", "error");
                }
                else {
                    $form->addError("");
                    $this->flashMessage("Zadané jméno nebo e-mail je již použito.", "error");
                }
            } else {
                $this->flashMessage("Nemáte dostatečná práva pro přidělení role administrátora.");
            }
        } else {
            $this->flashMessage("Nemáte dostatečná práva pro editaci role administrátora.");
        }

        if ($this->isAjax()) {
            $this->invalidateControl("usersTable");
            $this->invalidateControl("flashMessages");
        }
        else
            $this->redirect("this");
    }

    /**
     * Creates form for adding users
     * @return Form form ready to render
     */
    public function createComponentAddUserForm() {
        $form = new Form();
        $facade = $this->facade;
        $_this = $this;
        $form->addText("realName", "Jméno:")->setRequired("Jméno uživatele musí být vyplněno!");
        $form->addText("userName", "Přihlašovací jméno:")->setRequired("Přihlašovací jméno uživatele musí být vyplněno")
                ->addRule(function ($control) use ($facade) {
                            return!$facade->getUserByUserName($control->value);
                        }, "Zadané uživatelské jméno je již obsazeno.");
        $form->addText("email", "E-mail:")->addRule(Form::EMAIL, "E-mail má neplatný formát!")->setRequired("E-mail musí být vyplněn!")
                ->addRule(function ($control) use ($facade) {
                            return!$facade->getUserByEmail($control->value);
                        }, "Zadaný e-mail je již použit.");

        $roles = $this->facade->getRolesInPairsIdName();
        if ($this->userRole != "admin") { //nikdo jiný než admin nemůže přiřadit adminská práva
            foreach ($roles as $id => $role) { //TODO: opravit - prasárna
                if ($role == "admin")
                    unset($roles["id"]);
            }
        }
        $form->addSelect("role", "Role:", $roles)->setRequired("Uživatel musí mít přidělenou roli!");
        $form->addSubmit("submit", "Přidat uživatele");

        $form->onSuccess[] = callback($this, "addUserFormSubmitted");

        return $form;
    }

    /**
     * Proccess submitted form for adding users
     * @param Form $form - submitted form
     */
    public function addUserFormSubmitted($form) {
        $val = $form->getValues();
        if ($val->role != "admin" || ($val->role == "admin" && $this->userRole == "admin")) {
            if ($this->isAllowed("Users", "addUsers")) {
                $pass = \AdminModule\Tools\PasswordGenerator::generatePassword();

                $user = new \Entity\User;
                $user->setRealName($val->realName);
                $user->setUserName($val->userName);
                $user->setPassword($this->getContext()->authenticator->calculateHash($pass));
                $user->setEmail($val->email);
                $user->setRole($this->facade->getRole($val->role));

                $this->facade->addUser($user);


                $url = new \Nette\Http\Url($this->getService("httpRequest")->getUrl());
                $url = $url->hostUrl;

                $mail = new \Nette\Mail\Message();
                $mail->setFrom("robot@dark-project.cz")
                        ->addTo($val->email)
                        ->setSubject('Nový uživatel')
                        ->setBody("Dobrý den,\nbyl jste přidán jako nový uživatel na webu " . $url . ". \n\nVaše přihlašovací jméno je: " . $val->userName . "\na heslo: " . $pass . "\n\nVaše údaje si můžete upravit po přihlášení na " . $url . "/admin\n\nHezký den, \n\nDark Project Webs")
                        ->send();

                $this->addEvent("Uživatel ".$this->loggedUserName." přidal uživatele ".$val->userName.".");
                $this->flashMessage("Uživatel byl úspěšně přidán!", "success");
            }
            else
                $this->flashMessage("Nemáte dostatečná práva na přidávání uživatelů!", "error");
        }
        else {
            $this->flashMessage("Nemáte dostatečná práva na přidávání uživatelů!", "error");
        }

        if ($this->isAjax()) {
            $this->invalidateControl("usersTable");
            $this->invalidateControl("flashMessages");
        } else {
            $this->redirect("this");
        }
    }

}