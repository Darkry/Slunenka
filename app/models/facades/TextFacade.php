<?php

namespace Facade;

class TextFacade {

    /** @var \Doctrine\ORM\EntityRepository */
    private $rep;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->rep = $em->getRepository("\Entity\Text");
        $this->em = $em;
    }

    /**
     * Return text list (uses paginator)
     * @param \VisualPaginator $vp
     * @return array texts
     */
    public function getTextList(\VisualPaginator $vp, $itemsPerPage = 20) {
        $query = $this->rep->createQueryBuilder("t");

        $paginator = $vp->getPaginator();
        $paginator->itemsPerPage = $itemsPerPage;
        $paginator->itemCount = $this->rep->createQueryBuilder("t")->select("count(t.id)")->getQuery()->getSingleScalarResult();
        $selection = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $selection = $selection->getQuery();
        $selection->setFirstResult($paginator->getOffset());
        $selection->setMaxResults($paginator->getItemsPerPage());

        return $selection->getResult();
    }

    public function deleteText($id) {
        $text = $this->em->getReference("\Entity\Text", $id);
        $this->em->remove($text);
        $this->em->flush();
    }

    public function getTextById($id) {
        return $this->rep->find($id);
    }

    public function updateText($id, $values) {
        $entity = $this->rep->find($id);
        foreach ($values as $k => $v) {
            $function = "set" . ucfirst($k);
            $entity->$function($v);
        }
        $this->em->flush();
    }

    public function getTextName($id) {
        return $this->rep->find($id)->getName();
    }

}
