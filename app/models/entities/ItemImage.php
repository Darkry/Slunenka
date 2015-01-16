<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity
*   @ORM\Table(name="itemimage")
*/
class ItemImage extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $thumbnail;

    /** @ORM\manyToOne(targetEntity="ListItem", inversedBy="images") */
    private $item;

    public function getId() {
        return $this->id;
    }

    public function setImage($img) {
        $this->image = $img;
    }

    public function getImage() {
        return $this->image;
    }

    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail() {
        return $this->thumbnail;
    }

    public function setItem(\Entity\ListItem $item) {
        $this->item = $item;
    }

    public function getItem() {
        return $this->item;
    }
}
