<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="liste")
*/
class ListE extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\oneToMany(targetEntity="ListItem", mappedBy="list")
     */
    private $items;

    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setDescription($desc) {
        $this->description = $desc;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getItems() {
        return $this->items;
    }
}
