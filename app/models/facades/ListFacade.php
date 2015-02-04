<?php

namespace Facade;

class ListFacade {

    /** @var \Doctrine\ORM\EntityRepository */
    private $listRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $itemRep;

    /** @var \Doctrine\ORM\EntityRepository */
    private $imageRep;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->listRep = $em->getRepository("\Entity\ListE");
        $this->itemRep = $em->getRepository("\Entity\ListItem");
        $this->imageRep = $em->getRepository("\Entity\ItemImage");
        $this->em = $em;
    }

    public function getLists() {
        return $this->listRep->findAll();
    }

    public function getList($id) {
        return $this->listRep->find($id);
    }

    public function getListItems($id, $fromNewest=false) {
        if($fromNewest == true) {
            $qb = $this->itemRep->createQueryBuilder("e");
            $qb->where("e.list.id = ")->where("e.list = ?1")->orderBy("e.date", "DESC");
            $qb->setParameter(1, $id);
            return $qb->getQuery()->getResult();
        }

        return $this->listRep->find($id)->getItems();
    }

    public function addItem(\Entity\ListItem $item, $images) {
        $this->em->persist($item);
        foreach ($images as $image)
            $this->em->persist($image);
        $this->em->flush();
    }

    /** @return \Entity\ListItem */
    public function getItem($id) {
        return $this->itemRep->find($id);
    }

    public function getAllItems() {
        return $this->itemRep->findAll();
    }

    public function deleteItem($id) {
        $item = $this->getItem($id);

        foreach ($item->getImages() as $image) {
            $imageName = $image->getImage();
            unlink(WWW_DIR . "/upload/" . $imageName);
            if ($image->getThumbnail() != NULL) {
                unlink(WWW_DIR . "/upload/" . $image->getThumbnail());
            }
            $this->em->remove($image);
        }
        $this->em->remove($item);
        $this->em->flush();
    }

    public function editItem($id, $name, $description = NULL, $other = NULL) {
        $item = $this->getItem($id);
        $item->setName($name);
        if ($description != NULL)
            $item->setDescription($description);
        if ($other != NULL)
            $item->setOther($other);
        
        $this->em->flush();
    }
}