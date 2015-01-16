<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="role")
*/
class Role extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @ORM\oneToMany(targetEntity="Permission", mappedBy="role")
     */
    private $permissions;

    /**
     * @ORM\oneToMany(targetEntity="User", mappedBy="role")
     */
    private $users;

    public function __construct() {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection;
        $this->users = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = trim($name);
    }

    public function getName() {
        return $this->name;
    }

    /** @return \Doctrine\Common\Collections\Collection */
    public function getPermissions() {
        return $this->permissions;
    }

    /** @return \Doctrine\Common\Collections\Collection */
    public function getUsers() {
        return $this->users;
    }

}
