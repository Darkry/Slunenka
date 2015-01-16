<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="user")
*/
class User extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $password;

     /**
     * @ORM\Column(type="string", length=50)
     */
    private $realName;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @ORM\manyToOne(targetEntity="Role", inversedBy="users")
     */
    private $role;

    /**
     * @ORM\oneToMany(targetEntity="History", mappedBy="user")
     */
    private $history;

    public function __construct() {
        $this->history = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function setUserName($name) {
        $this->userName = trim($name);
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setRealName($realName) {
        $this->realName = $realName;
    }

    public function getRealName() {
        return $this->realName;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setRole(Role $role) {
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }

    public function getHistory() {
        return $this->history;
    }
}
