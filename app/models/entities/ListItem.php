<?php

namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="listitem")
*/
class ListItem extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $other;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\oneToMany(targetEntity="ItemImage", mappedBy="item")
     */
    private $images;

    /** @ORM\manyToOne(targetEntity="ListE", inversedBy="items") */
    private $list;

    /** 
    * @ORM\Column(type="datetime")
    */
    private $date;

    public function __construct() {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function setOther($other) {
        $this->other = $other;
    }

    public function getOther() {
        return $this->other;
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

    public function getImages() {
        return $this->images;
    }

    public function setList(\Entity\ListE $list) {
        $this->list = $list;
    }

    public function getList() {
        return $this->list;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

}
