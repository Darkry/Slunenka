<?php

namespace Facade;

class AclFacade {

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \AdminModule\Modules\ModulesInfoParser */
    private $modules;

    public function __construct(\Doctrine\ORM\EntityManager $em, \AdminModule\Modules\ModulesInfoParser $modules) {
        $this->em = $em;
        $this->modules = $modules;
    }

    /**
     * @return \Nette\Security\Permission
     */
    public function getPermissionObject() {
        $q = $this->em->createQuery("SELECT r FROM Entity\Role r");
        $roles = $q->getResult();

        $acl = new \Nette\Security\Permission();

        foreach ($roles as $role) {
            $acl->addRole($role->name);
            foreach ($role->getPermissions() as $permission) {
                $resource = $permission->getModule() . "-" . $permission->getComponent();
                if (!$acl->hasResource($resource))
                    $acl->addResource($resource);
                $acl->allow($role->getName(), $resource, $permission->getAction());
            }

            foreach ($this->modules->getModulesInfo() as $module) {
                if ($module->isEditable() && $module->hasModulePrivilegies())
                    foreach ($module->getComponents() as $name => $component) {
                        $resource = $module->getModuleName() . "-" . $name;
                        if ($component["state"] == true && !$acl->hasResource($resource))
                            $acl->addResource($resource);
                    }
            }
        }

        return $acl;
    }

}

