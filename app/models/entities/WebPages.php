<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="webpages")
*/
class WebPages extends \Nette\Object {

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
     * @ORM\Column(type="string", length=100)
     */
    private $adress;

    /**
     * @ORM\oneToMany(targetEntity="ContentFrames", mappedBy="page")
     */
    private $frames;

    public function __construct() {
        $this->frames = new \Doctrine\Common\Collections\ArrayCollection;
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

    public function setAdress($adress) {
        $this->adress = $adress;
    }

    public function getAdress() {
        return $this->name;
    }


    public function getFrames() {
        return $this->frames;
    }
}
