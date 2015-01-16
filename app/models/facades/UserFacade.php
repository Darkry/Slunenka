<?php

namespace Facade;

class UserFacade {

    /** @var \Doctrine\ORM\EntityRepository */
    private $userRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $roleRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $permissionRep;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->userRep = $em->getRepository("\Entity\User");
        $this->roleRep = $em->getRepository("\Entity\Role");
        $this->permissionRep = $em->getRepository("\Entity\Permission");
        $this->em = $em;
    }

    public function getRoleByName($name) {
        return $this->roleRep->findOneBy(array("name" => $name));
    }

    public function getAllUsers() {
        return $this->userRep->findAll();
    }

    public function getAllRoles() {
        return $this->roleRep->findAll();
    }

    public function getRolesInPairsIdName() {
        $roles = $this->em->createQuery("select partial r.{id,name} from \Entity\Role r")->getResult();
        $result = array();
        foreach ($roles as $role) {
            $result[$role->id] = $role->name;
        }
        return $result;
    }

    public function getRole($id) {
        return $this->roleRep->find($id);
    }

    public function getUser($id) {
        return $this->userRep->find($id);
    }

    public function updateUser($id, $values) {
        $entity = $this->userRep->find($id);
        foreach ($values as $k => $v) {
            if($k == "role") {
                $v = $this->roleRep->find($v);
            }
            $function = "set" . ucfirst($k);
            $entity->$function($v);
        }
        $this->em->flush();
    }

    public function getRoleName($id) {
        return $this->roleRep->find($id)->getName();
    }

    public function deletePermission($roleId, $module, $component, $action) {
        $role = $this->roleRep->find($roleId);
        $permission = $this->permissionRep->findOneBy(array("role" => $role, "module" => $module, "component" => $component, "action" => $action));
        $this->em->remove($permission);
        $this->em->flush();
    }

    public function hasUserRole($role) {
        if(count($role->getUsers()) == 0)
                return false;
        else
            return true;
    }

    public function deleteUserById($userId) {
        $user = $this->userRep->find($userId);
        $this->em->remove($user);
        $this->em->flush();
    }

    public function deleteRole($role) {
        foreach($role->getPermissions() as $permission) {
            $this->em->remove($permission);
        }
        $this->em->remove($role);
        $this->em->flush();
    }

    public function addRole($name) {
        $role = new \Entity\Role;
        $role->setName($name);
        $this->em->persist($role);
        $this->em->flush();
    }

    public function addUser(\Entity\User $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function addPermission($module, $component, $action, $roleId) {
        $role = $this->roleRep->find($roleId);
        $permission = new \Entity\Permission;
        $permission->setModule($module);
        $permission->setComponent($component);
        $permission->setAction($action);
        $permission->setRole($role);

        $this->em->persist($permission);
        $this->em->flush();
    }

    public function getUserByUserName($name) {
        return $this->userRep->findOneBy(array("userName" => $name));
    }

    public function getUserByEmail($mail) {
        return $this->userRep->findOneBy(array("email" => $mail));
    }

    public function isDuplicateAfterChange($userName, $email, $userId) {
        $user = $this->userRep->find($userId);
        if($userName != $user->getUserName() && $this->userRep->createQueryBuilder("u")
                    ->select("u")
                    ->where("u.userName = :userName AND u.id != :userId")
                    ->setParameter("userName", $userName)
                    ->setParameter("userId", $userId)->getQuery()->getOneOrNullResult() != NULL) {
            return true;
        }
        if($email != $user->getEmail() && $this->userRep->createQueryBuilder("u")
                    ->select("u")
                    ->where("u.email = :email AND u.id != :userId")
                    ->setParameter("email", $email)
                    ->setParameter("userId", $userId)->getQuery()->getOneOrNullResult() != NULL) {
            return true;
        }
        return false;
    }

    public function changePassword($userName, $password) {
        $user = $this->userRep->findOneBy(array("userName" => $userName));
        $user->setPassword($password);

        $this->em->flush();
    }

    public function changePasswordByEmail($email, $password) {
        $user = $this->userRep->findOneBy(array("email" => $email));
        $user->setPassword($password);

        $this->em->flush();
    }

    public function changeRealName($id, $realName) {
        $user = $this->userRep->find($id);
        $user->setRealName($realName);

        $this->em->flush();
    }

    public function changeEmail($id, $email) {
        $user = $this->userRep->find($id);
        $user->setEmail($email);

        $this->em->flush();
    }
}
