<?php

namespace AdminModule\SettingsModule;

use \AdminModule\NetteExtension\Form;

/**
 * Allows to set administration users availabilities
 *
 * @author Kryštof Selucký
 * @package DarkAdmin
 */
class RolesPresenter extends \AdminModule\Security\SecuredPresenter {

    /** @var \Facade\UserFacade */
    private $facade;

    protected function startup() {
        parent::startup();

        if ($this->moduleOptions->getComponentState("Roles") == false) {
            $this->flashMessage("Tato komponenta administrace je pro Vás momentálně nedostupná.", "error");
            $this->redirect(":Admin:Index:Index:");
        }

        $this->facade = $this->getContext()->userFacade;
    }

    /** Transmit data to template */
    public function renderDefault() {
        $this->template->users = $this->facade->getAllUsers();
        $this->template->roles = $this->facade->getAllRoles();
        $this->template->modulesInfo = $this->getService("modulesInfoParser")->getModulesInfo();
    }

    /**
     * Creates form for changing users role
     * @return Form form to draw
     */
    public function createComponentChangeUserRoleForm() {
        $form = new Form();
        $form->addSelect("role", NULL, $this->facade->getRolesInPairsIdName());
        $form->addHidden("userId", 0);
        $form->getElementPrototype()->class = 'changeUserRoleForm';
        $form->onSuccess[] = callback($this, 'changeUserRoleFormSubmitted');
        return $form;
    }

    /**
     * Proccess form after succesfully submiting
     * @param Form $form succesfully posted form
     */
    public function changeUserRoleFormSubmitted(Form $form) {
        $values = $form->getValues();
        if (!$this->isAllowed("Roles", "editUserRoles") || $this->getUser()->getId() == $values["userId"])
            $this->flashMessage("Nemáte potřebná práva pro změnu rolí (a nebo se pokoušíte změnit si svou vlastní roli)!", "error");
        else {
            $this->facade->updateUser($values->userId, array("role" => $values->role));
            $userName = $this->facade->getUser($values->userId)->getUserName();
            $this->addEvent("Uživatel ".$this->loggedUserName." změnil roli uživatele ".$userName.".");
            $this->flashMessage("Role uživatele byla úspěšně změněna!", "success");
        }
        if ($this->isAjax())
            $this->invalidateControl("flashMessages");
        else
            $this->redirect("this");
    }

    /**
     * Signal that add role permission
     * @param string $resource
     * @param string $actionName
     * @param int $roleId
     */
    public function handleAddPermission($modulen, $componentn, $actionName, $roleId) {
        $roleName = $this->facade->getRole($roleId)->getName();

        $roles = $this->getUser()->getRoles();
        $userRoleId = $this->facade->getRoleByName($roles[0])->getId();

        if ($userRoleId != $roleId && $this->acl->isAllowed($this->userRole, $modulen, $componentn, $actionName) && $this->isAllowed("Roles", "editRolesPermission") && $roleName != "admin") {
            $this->facade->addPermission($modulen, $componentn, $actionName, $roleId);
            $this->getService("acl")->regenerate();
            $this->addEvent("Uživatel ".$this->loggedUserName." přidal oprávnění roli ".$roleName.".");
        }

        if ($this->isAjax()) {
            $this->invalidateControl("roles");
        } else {
            $this->redirect("this");
        }
    }

    /**
     * Signal that allows to delete permission of role
     * @param string $resource
     * @param string $actionName
     * @param int $roleId
     */
    public function handleDeletePermission($modulen, $componentn, $actionName, $roleId) {
        $roleName = $this->facade->getRole($roleId)->getName();

        $roles = $this->getUser()->getRoles();
        $userRoleId = $this->facade->getRoleByName($roles[0])->getId();

        if ($userRoleId != $roleId && $this->acl->isAllowed($this->userRole, $modulen, $componentn, $actionName) && $this->isAllowed("Roles", "editRolesPermission") && $roleName != "admin") {
            $this->facade->deletePermission($roleId, $modulen, $componentn, $actionName);
            $this->getService("acl")->regenerate();
            $this->addEvent("Uživatel ".$this->loggedUserName." odebral oprávnění roli ".$roleName.".");
        }

        if ($this->isAjax()) {
            $this->invalidateControl("roles");
        } else {
            $this->redirect("this");
        }
    }

    /**
     * Delete whole role with all its permissions
     * @param int $roleId
     */
    public function handleDeleteRole($roleId) {
        if ($this->isAllowed("Roles", "deleteRoles")) {
            $role = $this->facade->getRole($roleId);
            if ($this->facade->hasUserRole($role)) {
                $this->flashMessage("Tuto roli má přidělenou nějaký uživatel, nemůžete ji smazat.", "error");
                if ($this->isAjax()) {
                    $this->invalidateControl("flashMessages");
                } else {
                    $this->redirect("this");
                }
            } else {
                $roleName = $role->getName();
                $this->addEvent("Uživatel ".$this->loggedUserName." odstranil roli ".$role->getName().".");
                $this->facade->deleteRole($role);

                if ($this->isAjax()) {
                    $this->invalidateControl("roles");
                    $this->invalidateControl("userRoles");
                } else {
                    $this->redirect("this");
                }
            }
        } else {
            $this->flashMessage("Nemáte dostatečná práva pro mazání rolí!.", "error");
            if ($this->isAjax()) {
                $this->invalidateControl("flashMessages");
            } else {
                $this->redirect("this");
            }
        }
    }

    /**
     * Creates form to adding roles
     * @return Form form to draw
     */
    public function createComponentAddRoleForm() {
        $form = new Form();
        $facade = $this->facade;
        $form->addText("name", "Název: ")
                ->setRequired("Název role musí být vyplněn")
                ->addRule(function ($control) use ($facade) {
			return !$facade->getRoleByName($control->value);
		}, "Role se zadaným jménem již existuje");

        $form->addSubmit("submit", "Přidat roli");

        $form->onSuccess[] = callback($this, "addRoleFormSubmitted");

        return $form;
    }

    /**
     * Adding role after form submitting and success validation
     * @todo reset text value after adding role
     * @param Form $form submitted form with values
     */
    public function addRoleFormSubmitted(Form $form) {
        $val = $form->getValues();
        if ($this->isAllowed("Roles", "addRoles")) {
            $this->facade->addRole($val["name"]);
            $this->addEvent("Uživatel ".$this->loggedUserName." přidal roli s názvem ".$val->name.".");
        } else {
            $this->flashMessage("Nemáte dostatečná práva pro přidávání rolí!", "error");
        }
        $this->redirect("this");
    }

}