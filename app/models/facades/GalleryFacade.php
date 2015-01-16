<?php
namespace Facade;

class GalleryFacade {

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \Doctrine\ORM\EntityRepository */
    private $galRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $imgRep;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
        $this->galRep = $em->getRepository("\Entity\Gallery");
        $this->imgRep = $em->getRepository("\Entity\Image");
    }

    public function getGalleriesInPairsIdName() {
        $galleries = $this->em->createQuery("select partial g.{id,name} from \Entity\Gallery g")->getResult();
        $result = array();
        foreach ($galleries as $gallery) {
            $result[$gallery->id] = $gallery->name;
        }
        return $result;
    }

    public function getGalleries() {
        return $this->galRep->findAll();
    }

    public function getGallery($id) {
        return $this->galRep->find($id);
    }

    public function addGallery($name, $description = NULL) {
        $gal = new \Entity\Gallery();
        $gal->setName($name);
        if($description != NULL) $gal->setText($description);
        $this->em->persist($gal);
        $this->em->flush();
        return $gal;
    }

    public function editGallery($id, $name, $description = NULL) {
        $gal = $this->getGallery($id);
        $gal->setName($name);
        if($description != NULL)
            $gal->setText($description);
        $this->em->flush();
    }

    public function addImage($values) {
        $img = new \Entity\Image();
        foreach ($values as $k => $v) {
            $function = "set" . ucfirst($k);
            $img->$function($v);
        }
        $this->em->persist($img);
        $this->em->flush();
        return $img;
    }

    public function deleteImageFromGallery($id) {
        $img = $this->imgRep->find($id);
        $this->em->remove($img);
        $this->em->flush();
    }

    public function getImage($id) {
        return $this->imgRep->find($id);
    }

    public function editImage($id, $name = NULL, $description = NULL) {
        $img = $this->imgRep->find($id);
        if($name != NULL) $img->setName($name);
        if($description != NULL) $img->setText($description);
        $this->em->flush();
    }

    public function getGalleryImages($id) {
        return $this->galRep->find($id)->getImages();
    }

    public function deleteGalleryWithImages($id) {
        $gallery = $this->galRep->find($id);
        foreach($gallery->getImages() as $image) {
            $this->em->remove($image);
        }
        $this->em->remove($gallery);
        $this->em->flush();
    }

    public function doesGalleryNameExist($name) {
        if($this->galRep->findOneBy(array("name" => $name)) === NULL)
            return false;
        else
            return true;
    }
}
