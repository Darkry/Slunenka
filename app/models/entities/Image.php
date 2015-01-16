<?php
namespace Entity;

use \Doctrine\ORM\Mapping as ORM;

/** 
*   @ORM\Entity 
*   @ORM\Table(name="image")
*/
class Image extends \Nette\Object {

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="text",nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $thumbnail;

    /** @ORM\manyToOne(targetEntity="Gallery", inversedBy="images") */
    private $gallery;

    public function getId() {
        return $this->id;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getText() {
        return $this->text;
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

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setGallery(\Entity\Gallery $gallery) {
        $this->gallery = $gallery;
    }

    public function getGallery() {
        return $this->gallery;
    }
}
