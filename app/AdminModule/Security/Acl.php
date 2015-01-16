<?php
namespace AdminModule\Security;

class Acl {

    /** @var \Nette\Security\Permission */
    private $acl = null;

    /** @var \Facade\AclFacade */
    private $aclFacade;

    /** @var \Nette\Caching\Cache */
    private $cache;

    /** @var \AdminModule\Modules\ModuleInfoParser */
    private $modules;

    public function __construct(\Facade\AclFacade $aclFacade, \Nette\Caching\Cache $cache, \AdminModule\Modules\ModulesInfoParser $modules) {
        $this->aclFacade = $aclFacade;
        $this->cache = $cache;
        $this->modules = $modules;
    }

    /**
     * TRUE - user has permission, FALSE - user doesn't have permission, NULL - action is turned off
     * @return true, false or null
     */
    public function isAllowed($role, $module, $component, $action) {
        if ($this->modules->getModuleInfo($module)->getActionState($component, $action) == false) {
            return NULL;
        }

        if(strtolower($role) == "admin")
            return true;

        if ($this->acl == NULL) {
            $this->acl = $this->getPermissions();
        }

        $resource = $module . "-" . $component;
        return $this->acl->isAllowed($role, $resource, $action);
    }

    public function getPermissions() {
        if ($this->cache->load("acl") !== NULL)
            return $this->cache->load("acl");

        $permission = $this->aclFacade->getPermissionObject();
        $this->cache->save("acl", $permission);
        return $permission;
    }

    public function regenerate() {
        $permission = $this->aclFacade->getPermissionObject();
        $this->cache->save("acl", $permission);
        $this->acl = $permission;
    }

}