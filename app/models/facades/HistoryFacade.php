<?php
namespace Facade;

class HistoryFacade {

    
    private $rep;


    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->rep = $em->getRepository("\Entity\History");
        $this->em = $em;
    }

    public function addEvent(\Entity\History $event) {
        $this->em->persist($event);
        $this->em->flush();
    }

        /**
     * Return events list (uses paginator)
     * @param \VisualPaginator $vp
     * @return array texts
     */
    public function getEventsList(\VisualPaginator $vp, $itemsPerPage = 20) {
        $query = $this->rep->createQueryBuilder("e")->orderBy("e.date", "DESC");

        $paginator = $vp->getPaginator();
        $paginator->itemsPerPage = $itemsPerPage;
        $paginator->itemCount = $this->rep->createQueryBuilder("e")->select("count(e.id)")->getQuery()->getSingleScalarResult();
        $selection = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $selection = $selection->getQuery();
        $selection->setFirstResult($paginator->getOffset());
        $selection->setMaxResults($paginator->getItemsPerPage());

        return $selection->getResult();
    }
}