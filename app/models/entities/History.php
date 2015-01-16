<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="history")
*/
class History extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

     /**
     * @ORM\Column(type="string", length=200)
     */
    private $text;

    /**
     * @ORM\manyToOne(targetEntity="User", inversedBy="history")
     */
    private $user;

    public function getId() {
        return $this->id;
    }

    public function setDate($datetime) {
        if(($datetime instanceof \Nette\DateTime) == false || ($datetime instanceof \DateTime) == false)
            throw new \Exception("Invalid datetime format");
        $this->date = $datetime;
    }

    public function getDate() {
        return $this->date;
    }

    public function setUser(\Entity\User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }
}
