<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="permission")
*/
class Permission extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $module;

    /** @ORM\Column(type="string", length=50) */
    private $component;

    /** @ORM\Column(type="string", length=50) */
    private $action;

    /** @ORM\manyToOne(targetEntity="Role", inversedBy="permissions") */
    private $role;

    public function getId() {
        return $this->id;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function getModule() {
        return $this->module;
    }

    public function setComponent($component) {
        $this->component = $component;
    }

    public function getComponent() {
        return $this->component;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function setRole(Role $role) {
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }
}
