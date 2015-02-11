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

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $contentId;

    public function getId() {
        return $this->id;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }

    public function setContentId($contentId) {
        $this->contentId = $contentId;
    }

    public function getContentId() {
        return $this->contentId;
    }

    public function setPage(\Entity\WebPages $page) {
        $this->page = $page;
    }

    public function getPage() {
        return $this->page;
    }
}
