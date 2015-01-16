<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="text")
*/
class Text extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=true)
     */
    private $name;

     /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortVersion;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    public function getId() {
        return $this->id;
    }

    public function setDate($datetime) {
        if(($datetime instanceof \Nette\DateTime) == false || ($datetime instanceof \DateTime) == false)
            if($datetime !== NULL)
                throw new \Exception("Invalid datetime format");
        $this->date = $datetime;
    }

    public function getDate() {
        return $this->date;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setShortVersion($text) {
        $this->shortVersion = $text;
    }

    public function getShortVersion() {
        return $this->shortVersion;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }
}
