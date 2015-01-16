<?php

namespace AdminModule\Security;

/**
 * This class is an abstract parent for all CMS presenter which will be avaible only if the user is logged in.
 *
 * @author Kryštof Selucký
 */
abstract class SecuredPresenter extends \AdminModule\BasePresenter {

    /** @var \AdminModule\Modules\ModulesInfoParser */
    protected $modules;

    /** @var \AdminModule\Security\Acl */
    protected $acl;

    /** @var \AdminModule\Modules\ModuleInfo */
    protected $moduleOptions;

    /** @var string Actual module name */
    protected $moduleName;

    /** @var string actual user role */
    public $userRole;

    public $loggedUserName;

    /**
     * Settings some basic variables and controling if user is logged in
     * @return void
     */
    protected function startup() {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect(":Admin:Login:Login:");
        }

        $this->loggedUserName = $this->getUser()->getIdentity()->userName;
        $this->acl = $this->getService("acl");
        $this->modules = $this->getService("modulesInfoParser");
        $roles = $this->getUser()->getRoles();
        $this->userRole = $roles[0];
        $this->moduleName = explode(":", $this->getName());
        if ($this->moduleName[1] != "Index") {
            $this->moduleName = $this->moduleName[1] . "Module";
            $this->moduleOptions = $this->getService("modulesInfoParser")->getModuleInfo($this->moduleName);

            if ($this->moduleOptions->isEditable() == false) {
                $this->flashMessage("Tento modul administrace je vypnut.", "error");
                $this->redirect(":Admin:Index:Index:");
            }
        } else {
            $this->moduleName = NULL;
            $this->moduleOptions = NULL;
        }
    }

    /**
     * Transmitting user informations to template
     * @return void
     * @deprecated acl variable -> $presenter->isAllowed()
     */
    public function beforeRender() {
        $this->template->loggedUser = $this->getUser();
        $this->template->userName = $this->getUser()->getIdentity()->userName;
        $this->template->userRole = $this->userRole;
        $this->template->acl = $this->acl;
        $this->template->modules = $this->modules;
        $listM = $this->modules->getModuleInfo("ListsModule");
        if($listM->isEditable() && $listM->getOption("Lists", "listsInMenu")) {
            $this->template->lists = $this->getContext()->listFacade->getLists();
        }
    }

    /**
     * Logout user
     * @return void
     */
    public function handleLogout() {
        $this->getUser()->logout();
        $this->redirect(":Admin:Login:Login:");
    }

    /**
     * Checks if actual user is allowed to do something
     * @param string $component component name
     * @param string $action action name
     * @return boolean is allowed
     */
    public function isAllowed($component, $action) {
        return $this->acl->isAllowed($this->userRole, $this->moduleName, $component, $action);
    }

    /**
     * Checks if an option in actual module is allowed
     * @param string $component component name
     * @param string $option option name
     * @return boolean is option allowed
     */
    public function isOptionAllowed($component, $option) {
        return $this->moduleOptions->getOption($component, $option);
    }

    public function getSetting($component, $setting) {
        $set = $this->moduleOptions->getSetting($component, $setting);
        if(is_numeric($set)) $set = (int) $set;
        return $set;
    }

    public function addEvent($text, $userId = NULL, $date = "now") {
        if($date == "now")
            $date = new \Nette\DateTime();

        if($userId == NULL)
            $userId = $this->getUser()->getId();

        $event = new \Entity\History();
        $event->setDate($date);
        $event->setUser($this->getContext()->userFacade->getUser($userId));
        $event->setText($text);
        $this->getContext()->historyFacade->addEvent($event);
    }
}