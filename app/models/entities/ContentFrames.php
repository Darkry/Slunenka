<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="contentframes")
*/
class ContentFrames extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /** @ORM\manyToOne(targetEntity="WebPages", inversedBy="frames") */
    private $page;

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setPage(\Entity\WebPages $page) {
        $this->page = $page;
    }

    public function getPage() {
        return $this->page;
    }
}
